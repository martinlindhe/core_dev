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
 * @author Martin Lindhe, 2008-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: untangle smtp class, create a new connect() method
//XXXX: why are stuff static arrays?

require_once('CoreBase.php');
require_once('SmtpClient.php');
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
    protected static $_instance; ///< singleton

    protected $smtp;                         ///< SmtpClient object
    protected $version            = 'core_dev Sendmail 0.9';
    protected $connected          = false;

    protected $server_host, $server_port;
    protected $username, $password;

    protected static $from_adr, $from_name;
    protected static $rply_adr, $rply_name;
    protected static $subject;
    protected static $to_adr      = array();
    protected static $cc_adr      = array();
    protected static $bcc_adr     = array();
    protected static $html        = false;
    protected static $attachments = array();  ///< MailAttachment objects

    private function __construct()
    {
        set_time_limit(0);
    }

    private function __clone() {}      //singleton: prevent cloning of class

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        self::resetInstance();

        return self::$_instance;
    }

    function getServer() { return $this->server_host; }

    function getRecipients() { return self::$to_adr; }

    protected static function resetInstance()
    {
//        self::$from_adr = '';
//        self::$from_name = '';
//        self::$rply_adr = '';
//        self::$rply_name = '';
        self::$subject = '';
        self::$to_adr = array();
        self::$cc_adr = array();
        self::$bcc_adr = array();;
        self::$attachments = array();
    }

    function setServer($server = '', $username = '', $password = '', $port = 25)
    {
        mb_internal_encoding('UTF-8');    //XXX: required for utf8-encoded text (php 5.2)

        $this->server_host = $server;
        $this->server_port = $port;
        $this->username    = $username;
        $this->password    = $password;

        self::$from_adr = $username;

        $this->smtp = new SmtpClient($server, $username, $password, $port);
    }

    private function connect()
    {
        if ($this->getDebug())
            $this->smtp->debug = true;

        if (!$this->server_host)
            // throw new Exception ('No email server configured');
            return false;

        if (!$this->smtp->login())
            throw new Exception ('Cant connect to smtp server '.$this->server_host.':'.$this->server_port);

        $this->connected = true;
        return true;
    }

    private function disconnect()
    {
        if ($this->connected)
            $this->smtp->close();

        $this->connected = false;
    }

    function setSubject($s) { self::$subject = $s; }

    function setHtml($bool = true) { self::$html = $bool; }

    function setFrom($adr, $n = '')
    {
        $adr = trim($adr);
        if (!is_email($adr))
            throw new Exception ('Cant set invalid from address '.$adr);

        self::$from_adr  = $adr;
        self::$from_name = $n;
    }

    function setReplyTo($adr, $n = '')
    {
        $adr = trim($adr);
        if (!is_email($adr))
            throw new Exception ('Cant set reply-to to invalid address '.$adr);

        self::$rply_adr  = $adr;
        self::$rply_name = $n;
    }

    function addCc($s)
    {
        $s = trim($s);
        if (!is_email($s))
            throw new Exception ('Cant add invalid cc '.$s);

        self::$cc_adr[] = $s;
    }

    function addBcc($s)
    {
        $s = trim($s);
        if (!is_email($s))
            throw new Exception ('Cant add invalid bcc '.$s);

        self::$bcc_adr[] = $s;
    }

    function addRecipient($s)
    {
        $s = trim($s);
        if (!$s)
            return;

        if (!is_email($s))
            throw new Exception ('Cant add invalid recipient '.$s);

        self::$to_adr[] = $s;
    }

    /**
     * Adds a list of recipients
     * @param $a is a array or string (separated by comma/semicolon/newlines/whitespace) of mail addresses
     */
    function addRecipients($a)
    {
        if (!is_array($a)) {
            $a = reduce_whitespace($a);
            $a = str_replace(';', ',', $a);
            $a = str_replace(' ', ',', $a);

            // translate comma-separated string to array
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

        self::$attachments[] = $a;
    }

    function attachFile($filename)
    {
        $this->embedFile($filename);
    }

    function embedFile($filename, $cid = '')
    {
        if (!file_exists($filename))
            throw new Exception ('File '.$filename.' not found');

        $a = new MailAttachment();
        $a->data       = file_get_contents($filename);
        $a->filename   = basename($filename);
        $a->mimetype   = file_get_mime_by_suffix($filename);
        $a->content_id = $cid;                  //<img src="cid:pic_name">

        self::$attachments[] = $a;
    }

    /**
     * Sends a email
     */
    function send($msg = '')
    {
        if (!$this->connect()) {
            dp('SendMail failed to send mail because no mail server configured: '.substr($msg, 0, 200) );
            return false;
        }

        if (!$this->smtp->_MAIL_FROM(self::$from_adr))
            throw new Exception ('Failed to set from address');

        $header =
        "Date: ".date('r')."\r\n".
        "From: ".(mb_encode_mimeheader(self::$from_name, 'UTF-8') ? mb_encode_mimeheader(self::$from_name, 'UTF-8')." <".self::$from_adr.">" : self::$from_adr)."\r\n".
        "Subject: ".mb_encode_mimeheader(self::$subject, 'UTF-8')."\r\n".
        "User-Agent: ".$this->version."\r\n".
        "MIME-Version: 1.0\r\n";

        if (self::$rply_adr)
            $header .= "Reply-To: ".(mb_encode_mimeheader(self::$rply_name, 'UTF-8') ? mb_encode_mimeheader(self::$rply_name, 'UTF-8')." <".self::$rply_adr.">" : self::$rply_adr)."\r\n";

        foreach (self::$to_adr as $to) {
            if (!$this->smtp->_RCPT_TO($to)) continue;
            $header .= "To: ".$to."\r\n";
        }
        foreach (self::$cc_adr as $cc) {
            if (!$this->smtp->_RCPT_TO($cc)) continue;
            $header .= "Cc: ".$cc."\r\n";
        }
        foreach (self::$bcc_adr as $bcc) {
            if (!$this->smtp->_RCPT_TO($bcc)) continue;
            $header .= "Bcc: ".$bcc."\r\n";
        }

        if (count(self::$attachments)) {
            $rnd = md5( mt_rand().'))<>(('.microtime() );
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
        "Content-Type: ".(self::$html ? "text/html" : "text/plain")."; charset=utf-8\r\n".
        "Content-Transfer-Encoding: 7bit\r\n".
        "\r\n".
        $msg."\r\n";

        $attachment_data = '';
        foreach (self::$attachments as $a)
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

        if (count(self::$attachments))
            $attachment_data .= "--".$boundary."--";

        return $this->smtp->_DATA($header.$attachment_data);
    }

}

?>
