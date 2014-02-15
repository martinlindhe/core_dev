<?php
/**
 * $Id$
 *
 * Parses MIME formatted email messages
 *
 * @author Martin Lindhe, 2008-2011 <martin@ubique.se>
 */

//STATUS: WIP

//TODO: rework this to a static class

//TODO hmm: make a base mime reader class and extend a EMailReader class from it??
//FIXME: parseHeader() limitation - multiple keys with same name will just be glued together (Received are one such common header key)

namespace cd;

class EMail
{
    var $id;
    var $from;
    var $subject;
    var $headers;
    var $attachments;
}

class MimeReader
{
    private $headers            = array(); ///< parsed array of mime headers
    private $attachments        = array(); ///< parsed array of  mail attachments
    private $from_adr;                     ///< from e-mail address
    private $allowed_mime_types = array('text/plain', 'text/html', 'image/jpeg', 'image/png', 'video/3gpp', 'application/pdf');

    function getHeaders() { return $this->headers; }
    function getAttachments() { return $this->attachments; }

    function getHeader($s, $arr = false)
    {
        if (!$arr)
            $arr = $this->headers;

        foreach ($arr as $key => $val)
            if (strtolower($key) == strtolower($s))
                return $val;

        return false;
    }

    function getAsEMail($id) //XXX rework somehow when class is static
    {
        $mail = new EMail();
        $mail->id          = $id;
        $mail->from        = strtolower($this->from_adr);
        $mail->headers     = $this->headers;
        $mail->attachments = $this->attachments;

        if (isset($this->headers['subject']))
            $mail->subject = $this->headers['subject'];

        return $mail;
    }

    /**
     * Parses a email (header & body)
     */
    function parseMail($data)
    {
        //Separate header from mail body
        $pos = strpos($data, "\r\n\r\n");
        if ($pos === false) return false;

        $this->headers = $this->parseHeader(substr($data, 0, $pos));

        $body = trim(substr($data, $pos + strlen("\r\n\r\n")));
        $this->attachments = $this->parseAttachments($body);

        $from = $this->headers['from'];
        if (is_email($from))
            $this->from_adr = strtolower($from);
        else {
            //XXX simplify extraction
            $p = strrpos($from, ' ');
            $s = substr($from, $p+1);
            $s = str_replace('<', '', $s);
            $s = str_replace('>', '', $s);
            if (is_email($s))
                $this->from_adr = strtolower($s);
            else
                // XXX should not be possilbe
                throw new \Exception ('FAILED TO extract adr from '.$from);
        }

        return true;
    }

    /**
     * Parses a string of email headers into an array
     */
    function parseHeader($raw_head)
    {
        $headers = iconv_mime_decode_headers($raw_head);

        // lowercase array keys
        return array_change_key_case($headers, CASE_LOWER);
    }

    /**
     * Parses and decodes attachments
     */
    function parseAttachments($body)
    {
        $att = array();

        // find multipart separator
        $content = explode('; ', $this->getHeader('content-type') );

        if ($content[0] == 'text/plain')
        {
            // if mail header "Content-Type" dont contain "multipart/XXXX" (see below) then it's a plain message

            // returns message body as an attachment
            $att[0]['mimetype'] = $content[0];
            $att[0]['body']     = $body;
            return $att;
        }

        // Content-Type: multipart/mixed; boundary="------------020600010407070807000608"
        $multipart_id = '';

        foreach ($content as $part)
        {
            if ($part == 'multipart/mixed' || $part == 'multipart/related' || $part == 'multipart/alternative')
                continue;

            $pos = strpos($part, '=');

            if ($pos === false)
                throw new \Exception ("multipart header error, Content-Type: ".$this->getHeader('content-type'));

            $key = substr($part, 0, $pos);
            $val = substr($part, $pos+1);

            switch ($key) {
            case 'boundary':
                $multipart_id = '--'.str_replace('"', '', $val);
                break;

            default:
                echo "Unknown param: ".$key." = ".$val."\n";
                break;
            }
        }
        if (!$multipart_id)
            throw new \Exception ('didnt find multipart id');

        //echo "Splitting msg using id '".$multipart_id."'\n";

        // parses attachments into array
        $part_cnt = 0;
        do {
            $p1 = strpos($body, $multipart_id);
            $p2 = strpos($body, $multipart_id, $p1+strlen($multipart_id));

            if ($p1 === false || $p2 === false) {
                echo "p1: ".$p1.", p2: ".$p2.", multipart_id: ".$multipart_id."\n";
                die("error parsing attachment\n");
            }

            // $current contains a whole block with attachment & attachment header
            $current = substr($body, $p1 + strlen($multipart_id), $p2 - $p1 - strlen($multipart_id));

            $head_pos = strpos($current, "\r\n\r\n");
            if ($head_pos) {
                $a_head = trim(substr($current, 0, $head_pos));
                $a_body = trim(substr($current, $head_pos+2));
            } else {
                die("error: '".$current."'\n");
            }

            $att[ $part_cnt ]['header'] = $this->parseHeader($a_head); //attachment headers
            $att[ $part_cnt ]['body']   = $a_body;
            $body = substr($body, $p2);

            $params = explode('; ', $this->getHeader('content-type', $att[ $part_cnt ]['header']) );
            $att[ $part_cnt ]['mimetype'] = $params[0];

            if ($this->getHeader('content-location', $att[ $part_cnt ]['header']))
                $att[ $part_cnt ]['filename'] = $this->getHeader('content-location', $att[ $part_cnt ]['header'] );

            if (empty($att[ $part_cnt ]['filename']))
            {
                // extract name from [Content-Type] => image/jpeg; name="header.jpg"
                // or                [Content-Type] => image/jpeg; name=DSC00071.jpeg
                if (isset($params[1]) && substr($params[1], 0, 5) == 'name=')
                    $att[ $part_cnt ]['filename'] = str_replace('"', '', substr($params[1], 5) );
            }

            if (!in_array($att[ $part_cnt ]['mimetype'], $this->allowed_mime_types)) {
                echo "XXX Skipping attachment due to unknown mime type: ". $att[ $part_cnt ]['mimetype']."\n";
                d($att[$part_cnt]);
                continue;
            }

            $enc = $this->getHeader('content-transfer-encoding', $att[ $part_cnt ]['header']);
            $enc = strtolower($enc); /// HACK: Outlook 11 sends in uppercase

            switch ($enc) {
            case '7bit': break;
            case '8bit': break;
            case 'quoted-printable': break;

            case 'base64':
                $att[ $part_cnt ]['body'] = base64_decode($att[ $part_cnt ]['body']);
                break;

            default:
                throw new \Exception ("Unknown transfer encoding: ".$enc );
            }

            $part_cnt++;

        } while ($body != $multipart_id.'--');

        return $att;
    }

    /** Parses a MIME Authenticate response */
    static function parseAuthRequest($s)
    {
        $chal_str = explode(',', $s);

        foreach ($chal_str as $row) {
            $pos = strpos($row, '=');
            if (!$pos) continue;
            $name = trim(substr($row, 0, $pos));
            $val = substr($row, $pos+1);
            if (substr($val, 0, 1) == '"' && substr($val, -1) == '"') {
                $val = substr($val, 1, -1);
            }
            $chal[ $name ] = $val;
        }

        return $chal;
    }

}

?>
