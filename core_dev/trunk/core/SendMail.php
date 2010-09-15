<?php
/**
 * $Id$
 *
 * Util class to send email messages
 *
 * MIME attachments:                 http://tools.ietf.org/html/rfc2231
 * MIME message bodies:              http://tools.ietf.org/html/rfc2045
 * MIME media types & multipart:     http://tools.ietf.org/html/rfc2046
 * MIME header Content-Disposition:  http://tools.ietf.org/html/rfc2183
 * MIME validator:                   http://www.apps.ietf.org/msglint.html
 *
 * @author Martin Lindhe, 2008-2010 <martin@startwars.org>
 */

//STATUS: good

//TODO: untangle smtp class, create a new connect() method

require_once('class.CoreBase.php');
require_once('client_smtp.php');
require_once('network.php'); //for is_email()
require_once('files.php'); //for file_get_mime_by_suffix()

class MailAttachment
{
    var $data;
    var $filename;
    var $content_id = ''; //set for embedded images
    var $mimetype;
}

class SendMail extends CoreBase
{
    private $smtp;
    private $version     = 'core_dev Sendmail 0.9';

    private $from_adr, $from_name;
    private $rply_adr, $rply_name;
    protected $subject;
    private $to_adr      = array();
    private $cc_adr      = array();
    private $bcc_adr     = array();
    private $html        = false;
    private $attachments = array();  ///< MailAttachment objects

    private $connected = false;

    function __construct($server = '', $username = '', $password = '', $port = 25)
    {
        $this->setServer($server, $username, $password, $port);
    }

    function __destruct()
    {
        $this->disconnect();
    }

    function setServer($server = '', $username = '', $password = '', $port = 25)
    {
        mb_internal_encoding('UTF-8');    //XXX: required for utf8-encoded text (php 5.2)

        $this->smtp = new smtp($server, $username, $password, $port);
        $this->from_adr = $username;
    }

    private function connect()
    {
        if ($this->getDebug())
            $this->smtp->debug = true;

        if (!$this->smtp->login())
            throw new Exception ('Cant connect to smtp server');

        $this->connected = true;
    }

    private function disconnect()
    {
        if ($this->connected)
            $this->smtp->close();

        $this->connected = false;
    }

    function setSubject($s) { $this->subject = $s; }

    function setHtml($bool = true) { $this->html = $bool; }

    function setFrom($s, $n = '')
    {
        $s = trim($s);
        if (!is_email($s))
            throw new Exception ('Cant set invalid from address '.$s);

        $this->from_adr  = $s;
        $this->from_name = $n;
    }

    function setReplyTo($s, $n = '')
    {
        $s = trim($s);
        if (!is_email($s))
            throw new Exception ('Cant set reply-to to invalid address '.$s);

        $this->rply_adr  = $s;
        $this->rply_name = $n;
    }

    function addRecipient($s)
    {
        $s = trim($s);
        if (!is_email($s))
            throw new Exception ('Cant add invalid recipient '.$s);

        $this->to_adr[] = $s;
    }

    function addCc($s)
    {
        $s = trim($s);
        if (!is_email($s))
            throw new Exception ('Cant add invalid cc '.$s);

        $this->cc_adr[] = $s;
    }

    function addBcc($s)
    {
        $s = trim($s);
        if (!is_email($s))
            throw new Exception ('Cant add invalid bcc '.$s);

        $this->bcc_adr[] = $s;
    }

    /**
     * Adds a list of recipients
     * @param $a is a array or string (separated by comma/semicolon/newlines/whitespace) of mail addresses
     */
    function addRecipients($a)
    {
        if (!is_array($a)) {
            $a = trim($a);
            $a = str_replace("\n", ' ', $a);
            $a = str_replace("\r", ' ', $a);
            $a = str_replace(';', ',', $a);

            //translate comma-separated string to array
            $a = explode(',', $a);
        }

        foreach ($a as $s)
            $this->addRecipient($s);
    }

    function attachData($data, $filename, $mimetype = '')
    {
        $a = new MailAttachment();
        $a->data     = $data;
        $a->filename = basename($filename);
        $a->mimetype = $mimetype ? $mimetype : file_get_mime_by_suffix($filename);

        $this->attachments[] = $a;
    }

    function attachFile($filename)
    {
        $this->embedFile($filename);
    }

    function embedFile($filename, $cid = '')
    {
        if (!file_exists($filename))
            throw new Exception ('File ".$filename." not found');

        $a = new MailAttachment();
        $a->data       = file_get_contents($filename);
        $a->filename   = basename($filename);
        $a->mimetype   = file_get_mime_by_suffix($filename)
        $a->content_id = $cid;                  //<img src="cid:pic_name">

        $this->attachments[] = $a;
    }

    /**
     * Sends a email
     */
    function send($msg = '')
    {
        $this->connect();

        if (!$this->smtp->_MAIL_FROM($this->from_adr))
            throw new Exception ('Failed to set from address');

        $header =
        "Date: ".date('r')."\r\n".
        "From: ".(mb_encode_mimeheader($this->from_name, 'UTF-8') ? mb_encode_mimeheader($this->from_name, 'UTF-8')." <".$this->from_adr.">" : $this->from_adr)."\r\n".
        "Subject: ".mb_encode_mimeheader($this->subject, 'UTF-8')."\r\n".
        "User-Agent: ".$this->version."\r\n".
        "MIME-Version: 1.0\r\n";

        if ($this->rply_adr)
            $header .= "Reply-To: ".(mb_encode_mimeheader($this->rply_name, 'UTF-8') ? mb_encode_mimeheader($this->rply_name, 'UTF-8')." <".$this->rply_adr.">" : $this->rply_adr)."\r\n";

        foreach ($this->to_adr as $to) {
            if (!$this->smtp->_RCPT_TO($to)) continue;
            $header .= "To: ".$to."\r\n";
        }
        foreach ($this->cc_adr as $cc) {
            if (!$this->smtp->_RCPT_TO($cc)) continue;
            $header .= "Cc: ".$cc."\r\n";
        }
        foreach ($this->bcc_adr as $bcc) {
            if (!$this->smtp->_RCPT_TO($bcc)) continue;
            $header .= "Bcc: ".$bcc."\r\n";
        }

        if (count($this->attachments)) {
            $rnd = md5( mt_rand(0, 999999999999).'))<>(('.microtime() );
            $boundary = '------------0'.substr($rnd, 0, 23);
            $header .=
            "Content-Type: multipart/mixed;\r\n".
            " boundary=\"".$boundary."\"\r\n".
            "\r\n".
            "This is a multi-part message in MIME format.\r\n".
            "\r\n".
            "--".$boundary."\r\n";
        }

        $header .=
        "Content-Type: ".($this->html ? "text/html" : "text/plain")."; charset=utf-8\r\n".
        "Content-Transfer-Encoding: 7bit\r\n".
        "\r\n".
        $msg."\r\n";

        $attachment_data = '';
        foreach ($this->attachments as $a)
        {
            $attachment_data .=
            "\r\n".
            "--".$boundary."\r\n".
            "Content-Type: ".$a->mimetype.";\r\n".
            " name=\"".mb_encode_mimeheader($a->filename, 'UTF-8')."\"\r\n".
            "Content-Transfer-Encoding: base64\r\n".
            ($a->content_id ? "Content-ID: <".$a->content_id.">\r\n" : "").
            "Content-Disposition: ".($a->content_id ? "inline" : "attachment").";\r\n".
            " filename=\"".mb_encode_mimeheader($a->filename, 'UTF-8')."\"\r\n".
            "\r\n".
            chunk_split(base64_encode($a->data));
        }

        if (count($this->attachments))
            $attachment_data .= "--".$boundary."--";

        return $this->smtp->_DATA($header.$attachment_data);
    }

}

?>
