<?php
/**
 * $Id$
 *
 * Sends mail through a SMTP email server
 *
 * References
 * http://tools.ietf.org/html/rfc5321
 * http://tools.ietf.org/html/rfc821
 * http://cr.yp.to/smtp.html
 * http://www.vanemery.com/Protocols/SMTP/smtp.html
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * TODO: TLS (encryption) support
 */

class smtp
{
	var $handle;
	var $errno, $errstr;

	var $server, $port;
	var $username, $password;

	var $hostname = 'localhost.localdomain';	//our hostname, sent with HELO requests (XXX be dynamic)

	var $status = 0;		///< the last status code returned from the server
	var $lastreply = '';	///< the last reply from the server with status code stripped out

	function __construct($server = '', $username = '', $password = '', $port = 25)
	{
		$this->server = $server;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
	}

	function __destruct()
	{
		if ($this->handle) $this->logout();
	}

	function logout()
	{
		if (!$this->handle) return;
		$this->write('QUIT');
		if ($this->status != 221) {
			echo "smtp->logout() QUIT response error: ".$this->lastreply."\n";
		}
		fclose($this->handle);
		$this->handle = false;
	}

	function login($timeout = 30)
	{
		global $config;

		$this->handle = fsockopen($this->server, $this->port, $this->errno, $this->errstr, $timeout);
		if (!$this->handle) {
			if (!empty($config['debug'])) echo "smtp->login() connection failed\n";
			return false;
		}
		$this->read();
		if ($this->status != 220) {
			echo "smtp->login() unexpected server response: ".$this->lastreply."\n";
			$this->logout();
			return false;
		}

		if (strpos($this->lastreply, 'ESMTP') === false) {
			//XXX in this case, send normal "HELO"
			echo "smtp->login() server don't expose ESMTP support: ".$this->lastreply."\n";
			$this->logout();
			return false;
		}

		//send "EHLO"
		$this->write('EHLO '.$this->hostname);
		if ($this->status != 250) {
			//FIXME fallback on "HELO" if "EHLO" is not supported
			echo "smtp->login() EHLO response error: ".$res."\n";
			$this->logout();
			return false;
		}
		/**
		 * EHLO reply (FIXME parse abilities):
		 *
		 * PIPELINING
		 * SIZE 52428800
		 * VRFY
		 * ETRN
		 * STARTTLS
		 * AUTH DIGEST-MD5 CRAM-MD5 LOGIN PLAIN
		 * AUTH=DIGEST-MD5 CRAM-MD5 LOGIN PLAIN
		 * ENHANCEDSTATUSCODES
		 * 8BITMIME
		 * DSN
		 */

		//send "AUTH LOGIN"
		$this->write('AUTH LOGIN');
		if ($this->status != 334) {
			echo "smtp->login() AUTH LOGIN error: ".$res."\n";
			$this->logout();
			return false;
		}

		//send username
		$this->write(base64_encode($this->username));
		if ($this->status != 334) {
			echo "smtp->login() username error: ".$res."\n";
			$this->logout();
			return false;
		}

		//send password
		$this->write(base64_encode($this->password));
		if ($this->status != 235) {
			echo "smtp->login() password error: ".$res."\n";
			$this->logout();
			return false;
		}

		return true;
	}

	function read()
	{
		global $config;

		$str = '';
		$code = '';
		while ($row = fgets($this->handle, 512)) {
			if ($code && substr($row, 0, 3) != $code) {
				//XXX debugging, should never happen!
				echo "smtp->read() ERROR status changed from ".$code." to ".substr($row, 0, 3)."\n";
			}
			$code = substr($row, 0, 3);
			$str .= substr($row, 4);
			if (substr($row, 3, 1) == ' ') break;
		}
		$this->status = intval($code);
		$this->lastreply = $str;

		if (!empty($config['debug'])) echo "Read: ".$str."\n";
	}

	function write($str)
	{
		global $config;

		if (!empty($config['debug'])) echo "Write: ".$str."\n";
		fputs($this->handle, $str."\r\n");

		$this->read();	//read response
	}

	/**
	 * Sends a email
	 */
	function send($dst, $subject, $msg)
	{
		if (!$this->login()) return false;

		$this->write('MAIL FROM:<'.$this->username.'>');
		if ($this->status != 250) {
			echo "smtp->send() MAIL FROM response error: ".$res."\n";
			return false;
		}

		$this->write('RCPT TO:<'.$dst.'>');
		if ($this->status != 250) {
			echo "smtp->send() RCPT TO response error: ".$res."\n";
			return false;
		}

		$this->write('DATA');
		if ($this->status != 354) {
			echo "smtp->send() DATA response error: ".$res."\n";
			return false;
		}

		//send message
		$header =
			"Date: ".date('r')."\r\n".
			"From: ".$this->username."\r\n".
			"To: ".$dst."\r\n".
			"Subject: ".$subject."\r\n\r\n";

		$this->write($header.$msg."\r\n.\r\n");
		if ($this->status != 250) {
			echo "smtp->send() mail response error: ".$res."\n";
			return false;
		}
	}

}
?>
