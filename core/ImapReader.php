<?php
/**
 * $Id$
 *
 * Fetches mails from a IMAP email server by using the php5-imap extension
 *
 * References:
 * http://tools.ietf.org/html/rfc3501
 *
 * @author Martin Lindhe, 2008-2012 <martin@ubique.se>
 */

//STATUS: wip - expose in GetMail interface. move getMail() logic from here to GetMail class

//FIXME: cant find a way to specify connection timeout

namespace cd;

require_once('MimeReader.php');

class ImapReader extends CoreBase
{
    var $handle = false;

    private $server;
    private $port      = 143;     //XXX: "ssl" default port is 993
    private $username;
    private $password;
    private $emails    = array(); ///< array of EMail objects
    private $tot_mails = 0;
    private $use_ssl   = false;

    function __construct()
    {
        if (!extension_loaded('imap'))
            throw new \Exception ('php5-imap extension is required');
    }

    function setServer($s) { $this->server = $s; }
    function setUsername($s) { $this->username = $s; }
    function setPassword($s) { $this->password = $s; }
    function setPort($n) { $this->port = $n; }

    function useSsl($b) { $this->use_ssl = $b; }

    function __destruct()
    {
        $this->disconnect();
    }

    private function connect()
    {
        $inbox = '{'.$this->server.':'.$this->port.'/imap/'.($this->use_ssl ? 'ssl': 'novalidate-cert').'}INBOX';

        $this->handle = imap_open($inbox, $this->username, $this->password);
        if (!$this->handle)
            return false;

        return true;
    }

    private function disconnect()
    {
        if (!$this->handle)
            return;

        //deletes all mails marked for deletion
        imap_expunge($this->handle);

        imap_close($this->handle);
    }

    function deleteMail($mail_id)
    {
        imap_delete($this->handle, $mail_id);
    }

    /**
     * @param $callback function that is called with parsed mail header + attachments
     */
    function getMail($callback = '', $timeout = 30)
    {
        if (!$this->connect()) {
            echo "ERROR: IMAP connection to ".$this->server.":".$this->port." failed\n";
            return false;
        }

        $folders = imap_listmailbox($this->handle, '{'.$this->server.':'.$this->port.'}', '*');

        $msginfo = imap_mailboxmsginfo($this->handle);

        //dp('found '.$msginfo->Nmsgs.' messages in mailbox');

        $this->tot_mails = $msginfo->Nmsgs;

        for ($i=1; $i<= $this->tot_mails; $i++)
        {
            //dp("Downloading ".$i." of ".$this->tot_mails." ...");

            //XXX hack because retarded imap_fetchbody() dont allow to fetch the whole message
            $fp = fopen('php://temp', 'w');
            imap_savebody($this->handle, $fp, $i);
            rewind($fp);
            $msg = stream_get_contents($fp);
            fclose($fp);

            $mime = new MimeReader();
            $mime->parseMail($msg);

            $this->emails[] = $mime->getAsEMail($i);
        }

        if (!function_exists($callback))
            throw new \Exception ('ERROR callback function '.$callback.' not found');

        call_user_func($callback, $this->emails, $this);
    }

}

?>
