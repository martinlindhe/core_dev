<?
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
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

///WIP CODE! not complete yet


//allowed mail attachment mime types
$config['email']['attachments_allowed_mime_types'] = array('image/jpeg', 'image/png', 'video/3gpp');
$config['email']['text_allowed_mime_types'] = array('text/plain');

class pop3
{
	var $handle;
	var $errno, $errstr;

	var $server, $port;
	var $username, $password;

	var $unread_mails = 0;
	var $tot_bytes = 0;

	function __construct($server = '', $username = '', $password = '', $port = 110)
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

	function login($timeout)
	{
		global $config;

		$this->handle = fsockopen($this->server, $this->port, $this->errno, $this->errstr, $timeout);
		if (!$this->handle) {
			if (!empty($config['debug'])) echo "pop3->login() connection failed\n";
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
			if (!empty($config['debug'])) echo "pop3->login() APOP failed, trying normal method\n";
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
		global $config;

		$var = fgets($this->handle, 128);
		if (!empty($config['debug'])) echo "Read: ".$var."\n";

		return $var;
	}

	function write($line)
	{
		global $config;

		if (!empty($config['debug'])) echo "Wrote: ".$line."\n";
		fputs($this->handle, $line."\r\n");
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
	 * Expected response: +OK unread_mails tot_bytes
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

		$this->unread_mails = $arr[1];
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
	 */
	function getMail($callback = '', $timeout = 30)
	{
		if (!$this->login($timeout) || !$this->_STAT()) return false;

		if ($this->unread_mails) {
			echo $this->unread_mails." new mail(s)\n";
		} else {
			echo "No new mail\n";
		}

		for ($i=1; $i <= $this->unread_mails; $i++) {
			$msg_size = $this->_LIST($i);
			if (!$msg_size) continue;

			echo "Downloading ".$i." of ".$this->unread_mails." ... (".$msg_size." bytes)\n";

			$msg = $this->_RETR($i);
			if ($msg) {
				$this->parseMail($msg, $callback);
				//$this->_DELE($i);
			} else {
				echo "Download #".$i." failed!\n";
			}
		}

		$this->_QUIT();
		return true;
	}

	/**
	 * Email parser
	 *
	 * @param $msg raw email text from pop3 server
	 * @param $callback callback function to execute after mail is parsed
	 * @return all attachments, body & header nicely parsed up
	 */
	function parseMail($msg, $callback = '')
	{
		global $config;

		//Separate header from mail body
		$pos = strpos($msg, "\r\n\r\n");
		if ($pos === false) return false;

		$result['header'] = $this->parseHeader(substr($msg, 0, $pos));
		$result['body'] = trim(substr($msg, $pos + strlen("\r\n\r\n")));
		$result['attachments'] = $this->parseAttachments($result['header'], $result['body']);

		if (function_exists($callback)) {
			call_user_func($callback, $result);
		}
		return $result;
	}

	/**
	 * Takes a text string with email header and returns array
	 * FIXME: limitation: multiple keys with same name will just be glued together (Received are one such common header key)
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

			$header[ $curr_key ] = str_replace("\r", ' ', $header[ $curr_key ]);
			$header[ $curr_key ] = str_replace("\n", ' ', $header[ $curr_key ]);
			$header[ $curr_key ] = str_replace("\t", ' ', $header[ $curr_key ]);
			$header[ $curr_key ] = str_replace('  ', ' ', $header[ $curr_key ]);
		}

		return $header;
	}

	function parseAttachments(&$header, &$body)
	{
		global $config;

		$attachment = array();

		//Check content type
		$content = explode(';', $header['Content-Type']);


		//Content-Type: text/plain; charset=ISO-8859-1; format=flowed
		//Content-Type: multipart/mixed; boundary="------------020600010407070807000608"
		switch ($content[0]) {
			case 'text/plain': return $attachment;
		}

		$multipart_id = '';
		foreach ($content as $part)
		{
			$part = trim($part);
			if ($part == 'multipart/mixed' || $part == 'multipart/related') {
				continue;
			}

			$pos = strpos($part, '=');
			if ($pos === false) die("multipart header err\n");
			$key = substr($part, 0, $pos);
			$val = substr($part, $pos+1);

			switch ($key) {
				case 'boundary':
					$multipart_id = '--'.str_replace('"', '', $val);
					//echo "boundary: '".$multipart_id."'\n";
					break;

				default:
					echo "Unknown param: ".$key." = ".$val."\n";
					break;
			}
		}
		if (!$multipart_id) die('didnt find multipart id');

		//echo "Splitting msg using id '".$multipart_id."'\n";

		$part_cnt = 0;
		do {
			$p1 = strpos($body, $multipart_id);
			$p2 = strpos($body, $multipart_id, $p1+strlen($multipart_id));

			if ($p1 === false || $p2 === false) die('error parsing attachment');

			//$current contains a whole block with attachment & attachment header
			$current = substr($body, $p1 + strlen($multipart_id), $p2 - $p1 - strlen($multipart_id));

			$head_pos = strpos($current, "\r\n\r\n");
			if ($head_pos) {
				$a_head = trim(substr($current, 0, $head_pos));
				$a_body = trim(substr($current, $head_pos+2));
			} else {
				die("error: '".$current."'\n");
			}

			$attachment[ $part_cnt ]['head'] = $this->parseHeader($a_head);
			$attachment[ $part_cnt ]['body'] = $a_body;

			$part_cnt++;

			$body = substr($body, $p2);

		} while ($body != $multipart_id.'--');

		//Stores all base64-encoded attachments to disk
		foreach ($attachment as $att)
		{
			if (empty($att['head']['Content-Transfer-Encoding'])) {
				echo 'No Content-Transfer-Encoding header set!<br/>';
				continue;
			}

			//print_r($att['head']);

			$params = explode('; ', $att['head']['Content-Type']);
			$attachment_mime = $params[0];

			$filename = '';
			if (!empty($att['head']['Content-Location'])) $filename = $att['head']['Content-Location'];
			if (!$filename) {
				//Extracta filename från: [Content-Type] => image/jpeg; name="header.jpg"
				//Eller									: [Content-Type] => image/jpeg; name=DSC00071.jpeg

				//fixme fulhack:
				if (isset($params[1]) && substr($params[1], 0, 5) == 'name=') {
					$filename = str_replace('"', '', substr($params[1], 5) );
				}
			}

			switch (trim($att['head']['Content-Transfer-Encoding'])) {
				case '7bit':
					//text/html content
					if (!in_array($attachment_mime, $config['email']['text_allowed_mime_types'])) {
						//echo 'Text mime type unrecongized: '. $attachment_mime.'<br>';
						continue;
					}
					//echo 'Checking text: '.$attachment['body'].'<br>';
					break;

				case 'base64':
					//file attachments like images, videos
					if (!in_array($attachment_mime, $config['email']['attachments_allowed_mime_types'])) {
						echo "Attachment mime type unrecognized: ".$attachment_mime."\n";
						continue;
					}
					//$this->saveFile($filename, $attachment_mime, base64_decode($att['body']), $result['mms_code']);
					break;

				default:
					echo "Unknown transfer encoding: '". $att['head']['Content-Transfer-Encoding']."'\n";
					break;
			}
		}
		return $attachment;
	}
}

?>
