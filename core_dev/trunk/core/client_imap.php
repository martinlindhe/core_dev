<?php
/**
 * $Id$
 *
 * Fetches mails from a IMAP email server
 *
 * Required PHP extension: php_imap (sudo aptitude install php5-imap)
 *
 * References:
 * http://tools.ietf.org/html/rfc3501
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

//TODO: ability to use SSL to connect to server, see imap_open()

require_once('input_mime.php');

class imap extends CoreDevBase
{
	var $handle = false;

	var $server, $port;
	var $username, $password;

	var $tot_mails = 0;

	function __construct($server = '', $username = '', $password = '', $port = 143)
	{
		global $config;
		if (!empty($config['debug'])) $this->debug = true;
		$this->server = $server;
		$this->port = $port;	//XXX: "ssl" default port is 993
		$this->username = $username;
		$this->password = $password;
	}

	function __destruct()
	{
		if ($this->handle) imap_close($this->handle);
	}

	function login()
	{
		$this->handle = imap_open('{'.$this->server.':'.$this->port.'/imap/novalidate-cert}INBOX', $this->username, $this->password);
		if (!$this->handle) return false;

		return true;
	}

	function getMail($callback = '', $timeout = 30)	//FIXME: cant find a way to specify connection timeout
	{
		if (!$this->login()) return false;

		$folders = imap_listmailbox($this->handle, "{".$this->server.":".$this->port."}", "*");

		$msginfo = imap_mailboxmsginfo($this->handle);
		$this->tot_mails = $msginfo->Nmsgs;

		for ($i=1; $i<= $this->tot_mails; $i++) {
			if ($this->debug) echo "Downloading ".$i." of ".$this->tot_mails." ...\n";
			//XXX hack because retarded imap_fetchbody() dont allow to fetch the whole message
			$fp = fopen("php://temp", 'w');
			imap_savebody($this->handle, $fp, $i);
			rewind($fp);
			$msg = stream_get_contents($fp);
			fclose($fp);

			if ($callback && mimeParseMail($msg, $callback)) {
				imap_delete($this->handle, $i);
			} else {
				echo "Leaving ".$i." on server\n";
			}
		}

		//deletes all mails marked for deletion by imap_delete()
		imap_expunge($this->handle);
	}

}

?>
