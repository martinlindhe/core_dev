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
		$res = $this->read();
		if (substr($res, 0, 3) != 221) {
			echo "smtp->logout() QUIT response error\n";
			print_r($res);
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
		$announce = $this->read();
		if (substr($announce, 0, 3) != 220) {
			echo "smtp->login() unexpected server response: ".$announce."\n";
			$this->logout();
			return false;
		}

		if (strpos($announce, 'ESMTP') === false) {
			//XXX in this case, send normal "HELO"
			echo "smtp->login() server don't expose ESMTP support: ".$announce."\n";
			$this->logout();
			return false;
		}

		//send "EHLO"
		$this->write('EHLO '.$this->hostname);
		$res = $this->read();
		if (substr($res, 0, 3) != 250) {
			//FIXME fallback on "HELO" if "EHLO" is not supported
			echo "smtp->login() EHLO response error: ".$res."\n";
			$this->logout();
			return false;
		}
		/**
		 * EHLO reply (FIXME parse abilities):
		 *
		 * 250-PIPELINING
		 * 250-SIZE 52428800
		 * 250-VRFY
		 * 250-ETRN
		 * 250-STARTTLS
		 * 250-AUTH DIGEST-MD5 CRAM-MD5 LOGIN PLAIN
		 * 250-AUTH=DIGEST-MD5 CRAM-MD5 LOGIN PLAIN
		 * 250-ENHANCEDSTATUSCODES
		 * 250-8BITMIME
		 * 250 DSN
		 */
		//d($res);

		//send "AUTH LOGIN"
		$this->write('AUTH LOGIN');
		$res = $this->read();
		if (substr($res, 0, 3) != 334) {
			echo "smtp->login() AUTH LOGIN error: ".$res."\n";
			$this->logout();
			return false;
		}

		//send username
		$this->write(base64_encode($this->username));
		$res = $this->read();
		if (substr($res, 0, 3) != 334) {
			echo "smtp->login() username error: ".$res."\n";
			$this->logout();
			return false;
		}

		//send password
		$this->write(base64_encode($this->password));
		$res = $this->read();
		if (substr($res, 0, 3) != 235) {
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
		while ($row = fgets($this->handle, 512)) {
			$str .= $row;
			if (substr($row, 3, 1) == ' ') break;
		}

		if (!empty($config['debug'])) echo "Read: ".$str."\n";
		return $str;
	}

	function write($str)
	{
		global $config;

		if (!empty($config['debug'])) echo "Write: ".$str."\n";
		fputs($this->handle, $str."\r\n");
	}

	/**
	 * Sends a email
	 */
	function send($dst, $subject, $msg)
	{
		if (!$this->login()) return false;

		$this->write('MAIL FROM:<'.$this->username.'>');
		$res = $this->read();
		if (substr($res, 0, 3) != 250) {
			echo "smtp->send() MAIL FROM response error: ".$res."\n";
			return false;
		}

		$this->write('RCPT TO:<'.$dst.'>');
		$res = $this->read();
		if (substr($res, 0, 3) != 250) {
			echo "smtp->send() RCPT TO response error: ".$res."\n";
			return false;
		}

		$this->write('DATA');
		$res = $this->read();
		if (substr($res, 0, 3) != 354) {
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
		$res = $this->read();
		if (substr($res, 0, 3) != 250) {
			echo "smtp->send() mail response error: ".$res."\n";
			return false;
		}
	}

}
?>
