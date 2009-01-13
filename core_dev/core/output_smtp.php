<?php
/**
 * $Id$
 *
 * Sends mail through a SMTP email server
 *
 * References
 * http://tools.ietf.org/html/rfc5321
 * http://tools.ietf.org/html/rfc821
 *
 * Extension references
 * STARTTLS         http://www.ietf.org/rfc/rfc2487.txt
 * AUTH             http://www.rfc-editor.org/rfc/rfc2554.txt
 * AUTH CRAM-MD5    http://www.ietf.org/rfc/rfc2195.txt
 * AUTH DIGEST-MD5  http://www.ietf.org/rfc/rfc2831.txt
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * VERIFY: different AUTH methods against different SMTP servers
 * VERIFY: do all AUTH methods work with non-latin1 letters in username & password?
 * VERIFY: do HELO work on a non-ESMTP capable server?
 * FIXME: figure out why "AUTH DIGEST-MD5" needs an extra NOOP at the end
 * TODO: move out AUTH implementations to separate file (can be reused by IMAP etc)
 */

require_once('input_mime.php');	//for parseAuthRequest()

class smtp
{
	var $handle = false, $debug = false;

	var $server, $port;
	var $username, $password;

	var $hostname = 'localhost.localdomain';	///< our hostname, sent with HELO requests (XXX be dynamic)
	var $servername = '';

	var $status = 0;		///< the last status code returned from the server
	var $lastreply = '';	///< the last reply from the server with status code stripped out
	var $esmtp = false;		///< is server ESMTP capable?
	var $ability;			///< server abilities (response from EHLO)

	function __construct($server = '', $username = '', $password = '', $port = 25)
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

	function login($timeout = 30)
	{
		$this->handle = fsockopen($this->server, $this->port, $errno, $errstr, $timeout);
		if (!$this->handle) {
			if ($this->debug) echo "smtp->login() connection failed: ".$errno.": ".$errstr."\n";
			return false;
		}
		$this->read();
		if ($this->status != 220) {
			echo "smtp->login() [".$this->status."]: ".$this->lastreply."\n";
			$this->_QUIT();
			return false;
		}

		if (strpos($this->lastreply, 'ESMTP')) $this->esmtp = true;
		if (!$this->_EHLO()) return false;
		if (!$this->_STARTTLS()) return false;
		if (!$this->_AUTH()) return false;
		return true;
	}

	function read()
	{
		$str = '';
		$code = '';
		while ($row = fgets($this->handle, 512)) {
			if ($this->debug && $code && substr($row, 0, 3) != $code) {
				echo "smtp->read() ERROR status changed from ".$code." to ".substr($row, 0, 3)."\n";
			}
			$code = substr($row, 0, 3);
			$str .= substr($row, 4);
			if (substr($row, 3, 1) == ' ') break;
		}
		$this->status = intval($code);
		if (substr($str, -2) == "\r\n") $str = substr($str, 0, -2);
		$this->lastreply = $str;
		if ($this->debug) echo "Read [".$code."]: ".$str."\n";
	}

	function write($str)
	{
		if ($this->debug) echo "Write: ".$str."\n";
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

	function _HELO()
	{
		$this->write('HELO '.$this->hostname);
		if ($this->status != 250) {
			echo "smtp->_HELO() [".$this->status."]: ".$this->lastreply."\n";
			return false;
		}
		$this->servername = $this->lastreply;
		return true;
	}

	function _EHLO()
	{
		$this->ability = array();
		if (!$this->esmtp) return $this->_HELO();

		$this->write('EHLO '.$this->hostname);
		if ($this->status != 250) {
			echo "smtp->_EHLO() [".$this->status."]: ".$this->lastreply."\n";
			return false;
		}

		$arr = explode("\r\n", $this->lastreply);
		$this->servername = array_shift($arr);

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
			//XXX: this implementation might be buggy in regards to charset (non latin1 letters in username / password)
			$this->write('AUTH DIGEST-MD5');
			if ($this->status != 334) {
				echo "smtp->_AUTH() DIGEST-MD5 [".$this->status."]: ".$this->lastreply."\n";
				return false;
			}
			//echo "challenge: ".base64_decode($this->lastreply)."\n";

			$chal = parseAuthRequest(base64_decode($this->lastreply));
			if (empty($chal['qop'])) $chal['qop'] = 'auth';	//default

			if ($chal['algorithm'] != 'md5-sess') {
				echo "smtp->_AUTH() DIGEST-MD5 unknown algorithm: ".$chal['algorithm']."\n";
				return false;
			}

			//RFC 2831 @ 2.1.2.1
			$nc = '00000001';		//number of times same nonce has been used
			$cnonce = md5(mt_rand(0, 9999999999999).microtime());
			$digest_uri = 'smtp/'.$this->servername;

			$a1 = md5($this->username.':'.$chal['realm'].':'.$this->password, true).
				':'.$chal['nonce'].':'.$cnonce.(!empty($chal['authzid']) ? ':'.$chal['authzid'] : '');

			$a2 = 'AUTHENTICATE:'.$digest_uri.($chal['qop'] != 'auth' ? ':00000000000000000000000000000000' : '');

			$response = md5( md5($a1).':'.$chal['nonce'].':'.$nc.':'.$cnonce.':'.$chal['qop'].':'.md5($a2) );

			$cmd =
			'charset='.$chal['charset'].','.
			'username="'.$this->username.'",'.
			'realm="'.$chal['realm'].'",'.
			'nonce="'.$chal['nonce'].'",'.
			'nc='.$nc.','.
			'cnonce="'.$cnonce.'",'.
			'digest-uri="'.$digest_uri.'",'.
			'response='.$response.','.
			'qop='.$chal['qop'];

			$this->write(base64_encode($cmd));
			if ($this->status != 334) {
				echo "smtp->_AUTH() DIGEST-MD5 challenge [".$this->status."]: ".$this->lastreply."\n";
				return false;
			}
			//XXX validate server response, RFC 2831 @ 2.1.3
			//echo "response: ".base64_decode($this->lastreply)."\n";

			$this->write('NOOP');	//FIXME figure out why we need to send 1 more command to get the 235 status
			if ($this->status != 235) {
				echo "smtp->_AUTH() DIGEST-MD5 auth failed [".$this->status."]: ".$this->lastreply."\n";
				return false;
			}
			return true;
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

			$this->write(base64_encode($this->username));
			if ($this->status != 334) {
				echo "smtp->_AUTH() LOGIN username [".$this->status."]: ".$this->lastreply."\n";
				return false;
			}

			$this->write(base64_encode($this->password));
			if ($this->status != 235) {
				echo "smtp->_AUTH() LOGIN password [".$this->status."]: ".$this->lastreply."\n";
				return false;
			}
			return true;
		}

		//Defaults to "AUTH PLAIN" if the server is not ESMTP-capable (FIXME: verify with non-capable server)
		$this->write('AUTH PLAIN');
		if ($this->status == 503) return true; //authentication not enabled
		if ($this->status != 334) {
			echo "smtp->_AUTH() PLAIN [".$this->status."]: ".$this->lastreply."\n";
			return false;
		}
		$cmd = base64_encode(chr(0).$this->username.chr(0).$this->password);
		$this->write($cmd);
		if ($this->status != 235) {
			echo "smtp->_AUTH() PLAIN error [".$this->status."]: ".$this->lastreply."\n";
			return false;
		}
		return true;
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

	function _DATA($d)
	{
		$this->write('DATA');
		if ($this->status != 354) {
			echo "smtp->_DATA() [".$this->status."]: ".$this->lastreply."\n";
			return false;
		}

		$this->write($d."\r\n.");
		if ($this->status != 250) {
			echo "smtp->_DATA() [".$this->status."]: ".$this->lastreply."\n";
			return false;
		}
		return true;
	}
}

?>
