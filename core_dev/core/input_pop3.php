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

	function open($timeout)
	{
		$this->handle = fsockopen($this->server, $this->port, $this->errno, $this->errstr, $timeout);
		if (!$this->handle) {
			if (!empty($config['debug'])) echo "Error: pop3->open() failed\n";
			return false;
		}
		return true;
	}

	function login($user, $pass)
	{
		global $config;

		$server_ready = $this->read();
		if (!$this->is_ok($server_ready)) {
			echo "pop3->login() failed! Server not allowing connections?\n";
			return false;
		}

		//Checks for APOP id in connection response
		$pos = strpos($server_ready, '<');
		if ($pos !== false) {
			//APOP support assumed
			$apop_string = trim(substr($server_ready, $pos));
			$this->write('APOP '.$user.' '.md5($apop_string.$pass));
			if ($this->is_ok()) return true;
			if (!empty($config['debug'])) echo "APOP login failed, trying normal method\n";
		}

		$this->write('USER '.$user);
		if (!$this->is_ok()) {
			echo "Error: pop3->login() Wrong username\n";
			$this->_QUIT();
			return false;
		}

		$this->write('PASS '.$pass);
		if (!$this->is_ok()) {
			echo "Error: pop3->login() Wrong password\n";
			$this->_QUIT();
			return false;
		}

		return true;
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
	}

	function _STAT()
	{
		$this->write('STAT');

		//Response: +OK 0 0			first number means 0 unread mail. second means number of bytes for all mail
		$response = $this->read();
		if (!$this->is_ok($response)) {
			$this->QUIT();
			echo "pop3->_STAT(): failed\n";
			return false;
		}

		$arr = explode(' ', $response);
		if (count($arr) != 3) {
			echo "pop3->_STAT(): unexpected response\n";
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
		if (!$this->is_ok($response)) {
			echo "pop3->_LIST(): Failed on ".$_id."\n";
			return false;
		}

		$arr = explode(' ', $response);
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
		if (!$this->is_ok()) return false;		//+OK 39265 octets

		$msg = '';
		do {
			$msg .= $this->read();
		} while (substr($msg, -5) != "\r\n.\r\n");

		$msg = substr($msg, 0, -5);	//remove ending "\r\n.\r\n"

		$this->parseAttachments($msg);
		return true;
	}

	/**
	 * Fetches all mail
	 */
	function getMail($timeout = 30)
	{
		if (!$this->open($timeout)) return false;
		if (!$this->login($this->username, $this->password)) return false;

		if (!$this->_STAT()) return false;

		if ($this->unread_mails) {
			echo $this->unread_mails." new mail(s)\n";
		} else {
			echo "No new mail\n";
		}

		for ($i=1; $i <= $this->unread_mails; $i++) {
			$msg_size = $this->_LIST($i);
			if (!$msg_size) continue;

			echo "Downloading #".$i."... (".$msg_size." bytes)\n";

			$check = $this->_RETR($i);
			//if ($check) $this->_DELE($i);
		}

		$this->_QUIT();
		return true;
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

		//echo '<pre>';print_r($header);

		return $header;
	}

	/**
	 * Takes a raw email (including headers) as parameter, returns all attachments, body & header nicely parsed up
	 * also automatically extracts file attachments and handles them as file uploads
	 * allowed file types as attachments are $config['email']['attachments_allowed_mime_types']
	 */
	function parseAttachments($msg)
	{
		global $config;

		$msg = str_replace("\r\n", "\n", $msg);

		//1. Klipp ut headern
		$pos = strpos($msg, "\n\n");
		if ($pos === false) return;

		$header = substr($msg, 0, $pos);
		//Parse each header element into an array
		$result['header'] = $this->parseHeader($header);

		//Cut out the rest of the message
		$msg = trim(substr($msg, $pos + strlen("\n\n")));

		//Check content type
		$check = explode(';', $result['header']['Content-Type']);
		//print_r($check);

		$multipart_id = '';

		foreach ($check as $part)
		{
			$part = trim($part);
			if ($part == 'multipart/mixed' || $part == 'multipart/related') {

			} else {
				$pos = strpos($part, '=');
				if ($pos === false) die("multipart header err\n");
				$key = substr($part, 0, $pos);
				$val = substr($part, $pos+1);

				switch ($key) {
					case 'boundary':
						//echo 'boundary: '.$val;
						$multipart_id = '--'.str_replace('"', '', $val);
						break;

					default:
						//echo 'unknown param: '.$key.' = '.$val.'<br>';
						break;
				}
			}
		}

		if (!$multipart_id) die('didnt find multipart id');

		//echo 'Splitting msg using id '.$multipart_id.'<br>';

		$part_cnt = 0;
		do {
			$pos1 = strpos($msg, $multipart_id);
			$pos2 = strpos($msg, $multipart_id, $pos1+strlen($multipart_id));

			//echo 'p1: '.$pos1.', p2: '.$pos2.'<br/>';

			if ($pos1 === false || $pos2 === false) die('error parsing attachment');

			//Current innehåller ett helt block med en attachment, inklusive attachment header
			//$current = substr($msg, $pos1 + strlen($multipart_id) + strlen("\n"), $pos2 - strlen($multipart_id));
			$current = substr($msg, $pos1 + strlen($multipart_id), $pos2 - $pos1 - strlen($multipart_id));

			//echo 'x'.$current.'x'; die;

			$head_pos = strpos($current, "\n\n");
			if ($head_pos) {
				$head = trim(substr($current, 0, $head_pos));
				//echo 'head: '.$head.'<br><br>';

				$body = trim(substr($current, $head_pos+2));
				//echo 'body: Y'.$body.'Y<br>';
			} else {
				die('error: "'.$current.'"');
			}

			//echo $body;
			//todo: klipp upp attachment headern

			$result['attachment'][ $part_cnt ]['head'] = $this->parseHeader($head);
			$result['attachment'][ $part_cnt ]['body'] = $body;

			$part_cnt++;

			$msg = substr($msg, $pos2);

			//echo 'x'.$msg.'x<br><br>';

		} while ($msg != $multipart_id.'--');

		/* Stores all base64-encoded attachments to disk */
		foreach ($result['attachment'] as $attachment)
		{

			if (empty($attachment['head']['Content-Transfer-Encoding'])) {
				echo 'No Content-Transfer-Encoding header set!<br/>';
				continue;
			}

			//print_r($attachment['head']);

			//Check attachment content type
			//echo 'Attachment type: '. $attachment['head']['Content-Type'].'<br>';

			$params = explode('; ', $attachment['head']['Content-Type']);
			//print_r($params);
			$attachment_mime = $params[0];

			$filename = '';
			if (!empty($attachment['head']['Content-Location'])) $filename = $attachment['head']['Content-Location'];
			if (!$filename) {
				//Extracta filename från: [Content-Type] => image/jpeg; name="header.jpg"
				//Eller									: [Content-Type] => image/jpeg; name=DSC00071.jpeg

				//fixme fulhack:
				if (isset($params[1]) && substr($params[1], 0, 5) == 'name=') {
					$filename = str_replace('"', '', substr($params[1], 5) );
				}
			}

			switch ($attachment['head']['Content-Transfer-Encoding']) {
				case '7bit':
				/* text/html content */
					if (!in_array($attachment_mime, $config['email']['text_allowed_mime_types'])) {
						//echo 'Text mime type unrecongized: '. $attachment_mime.'<br>';
						continue;
					}
					//echo 'Checking text: '.$attachment['body'].'<br>';
					break;

				case 'base64':
					/* file attachments like images, videos */
					if (!in_array($attachment_mime, $config['email']['attachments_allowed_mime_types'])) {
						echo "Attachment mime type unrecognized: ".$attachment_mime."\n";
						continue;
					}
					$this->saveFile($filename, $attachment_mime, base64_decode($attachment['body']), $result['mms_code']);
					break;

				default:
					echo "Unknown transfer encoding: ". $attachment['head']['Content-Transfer-Encoding']."\n";
					break;
			}
		}

		return $result;
	}
}

?>
