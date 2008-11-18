<?php
/**
 * $Id$
 *
 * Sends mail through a SMTP email server
 *
 * References
 * http://tools.ietf.org/html/rfc5321
 * http://tools.ietf.org/html/rfc821
 * STARTTLS extension: http://www.ietf.org/rfc/rfc2487.txt
 *
 * http://cr.yp.to/smtp.html
 *
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * TODO: support legacy "HELO" servers (need one to test against)
 */

class smtp
{
	var $handle;
	var $errno, $errstr;

	var $server, $port;
	var $username, $password;

	var $hostname = 'localhost.localdomain';	///< our hostname, sent with HELO requests (XXX be dynamic)

	var $status = 0;		///< the last status code returned from the server
	var $lastreply = '';	///< the last reply from the server with status code stripped out
	var $ability;			///< server abilities (response from EHLO)

	function __construct($server = '', $username = '', $password = '', $port = 25)
	{
		$this->server = $server;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
	}

	function __destruct()
	{
		if ($this->handle) $this->_QUIT();
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
			echo "smtp->login() [".$this->status."]: ".$this->lastreply."\n";
			$this->_QUIT();
			return false;
		}

		if (strpos($this->lastreply, 'ESMTP') === false) {
			//TODO in this case, send normal "HELO"
			echo "smtp->login() server don't expose ESMTP support: ".$this->lastreply."\n";
			$this->_QUIT();
			return false;
		}

		if (!$this->_EHLO()) return false;
		if (!$this->_STARTTLS()) return false;
		if (!$this->_AUTH()) return false;
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
		if (substr($str, -2) == "\r\n") $str = substr($str, 0, -2);
		$this->lastreply = $str;
		if (!empty($config['debug'])) echo "Read [".$code."]: ".$str."\n";
	}

	function write($str)
	{
		global $config;

		if (!empty($config['debug'])) echo "Write: ".$str."\n";
		fputs($this->handle, $str."\r\n");
		$this->read();	//read response
	}

	function _QUIT()
	{
		if (!$this->handle) return;
		$this->write('QUIT');
		if ($this->status != 221) {
			echo "smtp->_QUIT() [".$this->status."]: ".$this->lastreply."\n";
		}
		fclose($this->handle);
		$this->handle = false;
	}

	function _EHLO()
	{
		$this->write('EHLO '.$this->hostname);
		if ($this->status != 250) {
			//FIXME fallback on "HELO" if "EHLO" is not supported
			echo "smtp->_EHLO() [".$this->status."]: ".$this->lastreply."\n";
			return false;
		}

		$arr = explode("\r\n", $this->lastreply);
		$servername = array_shift($arr);
		$this->ability = array();

		foreach ($arr as $line) {
			if (substr($line, 0, 5) == "AUTH=") $line = "AUTH ".substr($line, 5);
			$t = explode(' ', $line);
			$name = array_shift($t);
			$val = implode(' ', $t);
			if (is_numeric($val)) $val = intval($val);
			if (strpos($val, ' ')) {
				$vals = explode(' ', $val);
				foreach ($vals as $n) {
					$this->ability[$name][$n] = true;
				}
			} else {
				$this->ability[$name] = $val;
			}
		}
		return true;
	}

	function _STARTTLS()
	{
		if (isset($this->ability['STARTTLS'])) {
			$this->write('STARTTLS');
			if ($this->status != 220) {
				echo "smtp->_STARTTLS() [".$this->status."]: ".$this->lastreply."\n";
				return false;
			}
			if (!stream_socket_enable_crypto($this->handle, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
				echo "smtp->_STARTTLS() enable crypto failed\n";
				return false;
			}
			$this->_EHLO();	//must resend EHLO after STARTTLS
		}
		return true;
	}

	function _AUTH()
	{
		if (isset($this->ability['AUTH']['DIGEST-MD5'])) {
			//FIXME implement
		}
		if (isset($this->ability['AUTH']['PLAIN'])) {
			//FIXME implement
		}

		if (isset($this->ability['AUTH']['CRAM-MD5'])) {
			$this->write('AUTH CRAM-MD5');
			if ($this->status != 334) {
				echo "smtp->_AUTH() CRAM-MD5 [".$this->status."]: ".$this->lastreply."\n";
				return false;
			}
			$digest = hash_hmac('md5', base64_decode($this->lastreply), $this->password);
			$this->write(base64_encode($this->username.' '.$digest));
			if ($this->status != 235) {
				echo "smtp->_AUTH() CRAM-MD5 challenge [".$this->status."]: ".$this->lastreply."\n";
				return false;
			}
			return true;
		}

		if (isset($this->ability['AUTH']['LOGIN'])) {
			$this->write('AUTH LOGIN');
			if ($this->status != 334) {
				echo "smtp->_AUTH() LOGIN [".$this->status."]: ".$this->lastreply."\n";
				return false;
			}

			//send username
			$this->write(base64_encode($this->username));
			if ($this->status != 334) {
				echo "smtp->_AUTH() LOGIN username [".$this->status."]: ".$this->lastreply."\n";
				return false;
			}

			//send password
			$this->write(base64_encode($this->password));
			if ($this->status != 235) {
				echo "smtp->_AUTH() LOGIN password [".$this->status."]: ".$this->lastreply."\n";
				return false;
			}
			return true;
		}

		return false;
	}

	function _MAIL_FROM($f)
	{
		$this->write('MAIL FROM:<'.$f.'>');
		if ($this->status != 250) {
			echo "smtp->_MAIL_FROM() [".$this->status."]: ".$this->lastreply."\n";
			return false;
		}
		return true;
	}

	function _RCPT_TO($t)
	{
		$this->write('RCPT TO:<'.$t.'>');
		if ($this->status != 250) {
			echo "smtp->_RCPT_TO() [".$this->status."]: ".$this->lastreply."\n";
			return false;
		}
		return true;
	}

	/**
	 * Sends a text email
	 */
	function mail($dst, $subject, $msg)
	{
		if (!$this->login()) return false;
		if (!$this->_MAIL_FROM($this->username)) return false;
		if (!$this->_RCPT_TO($dst)) return false;

		$this->write('DATA');
		if ($this->status != 354) {
			echo "smtp->send() DATA [".$this->status."]: ".$this->lastreply."\n";
			return false;
		}

		//send message
		$header =
		"From: ".$this->username."\r\n".
		"To: ".$dst."\r\n".
		"Subject: ".$subject."\r\n".
		"Date: ".date('r')."\r\n".
		//"MIME-Version: 1.0\r\n".
		//"Content-Type: text/plain; charset=ISO-8859-1\r\n".
		"X-Mailer: core_dev\r\n\r\n";	//XXX version string

		$this->write($header.$msg."\r\n.");
		if ($this->status != 250) {
			echo "smtp->send() mail [".$this->status."]: ".$this->lastreply."\n";
			return false;
		}
	}

}
?>
