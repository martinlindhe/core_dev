<?php
/**
 * $Id$
 *
 * Parses MIME formatted email messages
 *
 * @author Martin Lindhe, 2008-2009 <martin@startwars.org>
 */

//STATUS: WIP rewrite into a class & rename: MimeReader

//FIXME: parseHeader() limitation - multiple keys with same name will just be glued together (Received are one such common header key)

//FIXME: dont handle non-multipart mails???

class EMail
{
    var $id;
    var $headers;
    var $attachments;
}

class MimeReader
{
    private $headers = array(); //parsed array of mime headers
    private $attachments = array(); //parsed array of  mail attachments
    private $allowed_mime_types = array('text/plain', 'image/jpeg', 'image/png', 'video/3gpp');

    function getHeaders() { return $this->headers; }
    function getAttachments() { return $this->attachments; }

    function getAsEMail($id)
    {
        $mail = new EMail();
        $mail->id          = $id;
        $mail->headers     = $this->headers;
        $mail->attachments = $this->attachments;
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
        return true;
    }

    /**
     * Parses a string of email headers into an array
     */
    function parseHeader($raw_head)
    {
        $arr = explode("\n", $raw_head);
        $header = array();

        foreach ($arr as $row)
        {
            $pos = strpos($row, ': ');
            if ($pos) $curr_key = substr($row, 0, $pos);
            if (!$curr_key) die('super error');
            if (empty($header[ $curr_key ])) {
                $header[ $curr_key ] = substr($row, $pos + strlen(': '));
            } else {
                $header[ $curr_key ] .= $row;
            }

            $header[ $curr_key ] = normalizeString($header[ $curr_key ]);
        }

        return $header;
    }


    /**
     * Parses and decodes attachments
     */
    function parseAttachments($body)
    {
        $att = array();

        //find multipart separator
        $content = explode(';', $this->headers['Content-Type']);

        //Content-Type: multipart/mixed; boundary="------------020600010407070807000608"
        $multipart_id = '';
        foreach ($content as $part)
        {
            $part = trim($part);
            if ($part == 'multipart/mixed' || $part == 'multipart/related')
                continue;

            $pos = strpos($part, '=');
            if ($pos === false) die("multipart header err\n");
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
            throw new Exception ('didnt find multipart id');

        //echo "Splitting msg using id '".$multipart_id."'\n";

        //Parses attachments into array
        $part_cnt = 0;
        do {
            $p1 = strpos($body, $multipart_id);
            $p2 = strpos($body, $multipart_id, $p1+strlen($multipart_id));

            if ($p1 === false || $p2 === false) {
                echo "p1: ".$p1.", p2: ".$p2."\n";
                die("error parsing attachment\n");
            }

            //$current contains a whole block with attachment & attachment header
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

            $params = explode('; ', $att[ $part_cnt ]['header']['Content-Type']);
            $att[ $part_cnt ]['mimetype'] = $params[0];

            if (!empty($att[ $part_cnt ]['header']['Content-Location'])) $att[ $part_cnt ]['filename'] = $att[ $part_cnt ]['header']['Content-Location'];
            if (empty($att[ $part_cnt ]['filename'])) {
                //Extract name from [Content-Type] => image/jpeg; name="header.jpg"
                //or                [Content-Type] => image/jpeg; name=DSC00071.jpeg
                if (isset($params[1]) && substr($params[1], 0, 5) == 'name=') {
                    $att[ $part_cnt ]['filename'] = str_replace('"', '', substr($params[1], 5) );
                }
            }

            if (!in_array($att[ $part_cnt ]['mimetype'], $this->allowed_mime_types)) {
                echo "Unknown mime type: ". $att[ $part_cnt ]['mimetype']."\n";
                continue;
            }

            switch ($att[ $part_cnt ]['header']['Content-Transfer-Encoding']) {
            case '7bit':
                break;

            case '8bit':
                break;

            case 'base64':
                $att[ $part_cnt ]['body'] = base64_decode($att[ $part_cnt ]['body']);
                break;

            default:
                echo "Unknown transfer encoding: '". $att[ $part_cnt ]['header']['Content-Transfer-Encoding']."'\n";
                break;
            }

            $part_cnt++;

        } while ($body != $multipart_id.'--');

        return $att;
    }

}


/**
 * Parses a MIME Authenticate response, used in client_smtp.php, input_sip.php
 */
function parseAuthRequest($s)   //XXX move into MimeReader class???
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

?>
