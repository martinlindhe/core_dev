<?php
/**
 * $Id$
 *
 * Fetches mails from a POP3 email server
 * Important: POP3 standard uses "octets", it is equal to "bytes", an octet is a 8-bit value
 *
 * References
 * RFC 1939: http://www.ietf.org/rfc/rfc1939.txt
 *
 * APOP details: http://tools.ietf.org/html/rfc1939#page-15
 * More examples & info: http://www.thewebmasters.net/php/POP3.phtml
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

require_once('input_mime.php');

class pop3
{
	var $handle = false, $debug = false;

	var $server, $port;
	var $username, $password;

	var $tot_mails = 0;
	var $tot_bytes = 0;

	function __construct($server = '', $username = '', $password = '', $port = 110)
	{
		global $config;
		if (!empty($config['debug'])) $this->debug = true;
		$this->server = $server;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
	}

	function __destruct()
	{
		if ($this->handle) $this->_QUIT();
	}

	function login($timeout)
	{
		$this->handle = fsockopen($this->server, $this->port, $errno, $errstr, $timeout);
		if (!$this->handle) {
			if ($this->debug) echo "pop3->login() connection failed: ".$errno.": ".$errstr."\n";
			return false;
		}

		$res = $this->read();
		if (!$this->is_ok($res)) {
			echo "pop3->login() response error! Server not allowing connections?\n";
			return false;
		}

		//Checks for APOP id in connection response
		$pos = strpos($res, '<');
		if ($pos !== false) {
			$apop_hash = trim(substr($res, $pos));
			$this->write('APOP '.$this->username.' '.md5($apop_hash.$this->password));
			if ($this->is_ok()) return true;
			if ($this->debug) echo "pop3->login() APOP failed, trying normal method\n";
		}

		$this->write('USER '.$this->username);
		if (!$this->is_ok()) {
			echo "pop3->login() wrong username\n";
			$this->_QUIT();
			return false;
		}

		$this->write('PASS '.$this->password);
		if ($this->is_ok()) return true;
		echo "pop3->login() wrong password\n";
		$this->_QUIT();
		return false;
	}

	function read()
	{
		$var = fgets($this->handle, 128);
		if ($this->debug) echo "Read: ".$var."\n";
		return $var;
	}

	function write($str)
	{
		if ($this->debug) echo "Wrote: ".$str."\n";
		fputs($this->handle, $str."\r\n");
	}

	/**
	 * @return true if server response is "+OK"
	 */
	function is_ok($cmd = '')
	{
		if (!$cmd) $cmd = $this->read();
		if (ereg("^\+OK", $cmd)) return true;
		return false;
	}

	/**
	 * Sends QUIT command and closes the connection
	 */
	function _QUIT()
	{
		$this->write('QUIT');
		$this->read();		//Response: +OK Bye-bye.
		fclose($this->handle);
		$this->handle = false;
	}

	/**
	 * Asks the server about inbox status
	 * Expected response: +OK tot_mails tot_bytes
	 *
	 * @return true on success
	 */
	function _STAT()
	{
		$this->write('STAT');

		$res = $this->read();
		$arr = explode(' ', $res);
		if (!$this->is_ok($res) || count($arr) != 3) {
			$this->QUIT();
			echo "pop3->_STAT(): failed\n";
			return false;
		}

		$this->tot_mails = $arr[1];
		$this->tot_bytes = $arr[2];
		return true;
	}

	/**
	 * Ask the server for size of specified message
	 *
	 * @return number of bytes in current mail
	 */
	function _LIST($_id = 0)
	{
		if (!is_numeric($_id)) return false;

		$this->write('LIST '.$_id);

		$response = $this->read();	//Response: +OK id size
		$arr = explode(' ', $response);

		if (!$this->is_ok($response) || $arr[1] != $_id) {
			echo "pop3->_LIST(): Failed on ".$_id."\n";
			return false;
		}

		return intval($arr[2]);
	}

	/**
	 * Tells the server to delete a mail
	 */
	function _DELE($_id)
	{
		if (!is_numeric($_id)) return false;

		$this->write('DELE '.$_id);
		if (!$this->is_ok()) {
			echo "pop3->_DELE() Failed on ".$_id."\n";
			return false;
		}
		return true;
	}

	/**
	 * Retrieves specified mail from the server
	 */
	function _RETR($_id)
	{
		if (!is_numeric($_id)) return false;

		$this->write('RETR '.$_id);
		if (!$this->is_ok()) return false;

		$msg = '';
		do {
			$msg .= $this->read();
		} while (substr($msg, -5) != "\r\n.\r\n");

		return substr($msg, 0, -5);	//remove ending "\r\n.\r\n"
	}

	/**
	 * Fetches all mail
	 *
	 * @param $callback callback function to pass each mail to, if it returns true then delete mail from server
	 * @param $timeout timeout in seconds for server connection
	 */
	function getMail($callback = '', $timeout = 30)
	{
		if (!$this->login($timeout) || !$this->_STAT()) return false;

		if ($this->debug) {
			if ($this->tot_mails) {
				echo $this->tot_mails." mail(s)\n";
			} else {
				echo "No mail\n";
			}
		}

		for ($i=1; $i <= $this->tot_mails; $i++) {
			$msg_size = $this->_LIST($i);
			if (!$msg_size) continue;

			if ($this->debug) echo "Downloading ".$i." of ".$this->tot_mails." ... (".$msg_size." bytes)\n";

			$msg = $this->_RETR($i);
			if ($msg) {
				if ($callback && mimeParseMail($msg, $callback)) {
					$this->_DELE($i);
				} else {
					echo "Leaving ".$i." on server\n";
				}
			} else {
				echo "Download #".$i." failed!\n";
			}
		}

		$this->_QUIT();
		return true;
	}

}

?>
