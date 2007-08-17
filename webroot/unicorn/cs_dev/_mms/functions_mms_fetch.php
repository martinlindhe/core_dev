<?
/*
	email class.
	- läser in email från angiven address och söker igenom dessa efter "mms email", med bilagor
	- parsar mime parts efter attachments
	- sparar ut base64-enkodade attachments
	- stödjer APOP login, med normal USER/PASS fallback
	
	Baserat på kod av Frans Rosén
	Omskrivet av Martin Lindhe, 2007-04-13

	Important: POP3 standard uses "octets", it is equal to "bytes", an octet is a 8-bit value

	References
	----	
	POP 3 command summary: http://www.freesoft.org/CIE/RFC/1725/8.htm
	More examples & info: http://www.thewebmasters.net/php/POP3.phtml
	APOP details: http://tools.ietf.org/html/rfc1939#page-15

	//todo: gör en funktion som extractar "content-type" datan
	//todo: lägg en file lock i constructorn å lås upp den i destructorn, så inte 2 script kan köras parallellt
*/

//allowed mail attachment mime types
$config['email']['attachments_allowed_mime_types'] = array('image/jpeg', 'image/png', 'video/3gpp');
$config['email']['text_allowed_mime_types'] = array('text/plain');
$config['email']['log_activity'] = true;
$config['email']['logfile'] = '/home/martin/mms.log';

$config['email']['pop3_server'] = 'mail.citysurf.tv';
$config['email']['pop3_port']		= 110;
$config['email']['pop3_timeout'] = 5;

define('WEBROOT', '/home/martin/www/');

$config['email']['debug'] = false;				//echos POP3 commands and responses if enabled

class email
{
	var $handle;
	var $errno;
	var $errstr;

	var $unread_mails = 0;		//STAT unreadmail totbytes
	var $tot_bytes = 0;

	function __construct()
	{
	}

	/* Writes log entries to log file */
	function logAct($text, $echo_text = true)
	{
		global $config;

		if ($config['email']['debug'] || $echo_text) echo $text.'<br/>';
		if (!$config['email']['log_activity']) return;

		$out_text = date('Y-m-d H:i:s').': '.$text."\n";
		file_put_contents($config['email']['logfile'], $out_text, FILE_APPEND);
	}

	function open()
	{
		global $config;

		$this->handle = fsockopen($config['email']['pop3_server'], $config['email']['pop3_port'], $this->errorno, $this->errstr, $config['email']['pop3_timeout']);
	}

	function close()
	{
		$this->write('QUIT');
		$this->read();		//Response: +OK Bye-bye.

		fclose($this->handle);
	}

	function read()
	{
		global $config;

		$var = fgets($this->handle, 128);
		if ($config['email']['debug']) $this->logAct('Read: '.trim(htmlentities($var)));
		return $var;
	}

	function write($line)
	{
		global $config;

		if ($config['email']['debug']) $this->logAct('Wrote: '.htmlentities($line));
		fputs($this->handle, $line."\r\n");
	}

	//Return true or false on +OK or -ERR
	function is_ok($cmd = '')
	{
		if (!$cmd) $cmd = $this->read();
		if (ereg("^\+OK", $cmd)) return true;
		return false;
	}

	function login($user, $pass)
	{
		$server_ready = $this->read();
		if (!$this->is_ok($server_ready)) {
			$this->logAct('$email->login() failed! Server not allowing connections?');
			return false;
		}
		
		//Checks for APOP id in connection response
		$pos = strpos($server_ready, '<');
		if ($pos !== false) {
			//APOP support assumed
			$apop_string = trim(substr($server_ready, $pos));
			$this->write('APOP '.$user.' '.md5($apop_string.$pass));
			if ($this->is_ok()) {
				return true;
			}

			$this->logAct('APOP login failed, trying normal method');
		}

		$this->write('USER '.$user);
		if (!$this->is_ok()) {
			//Expected response: +OK User:'martin@unicorn.tv' ok
			$this->logAct('$email->login(): Wrong username');
			$this->close();
			return false;
		}

		$this->write('PASS '.$pass);
		if (!$this->is_ok()) {
			//Response 1: -ERR UserName or Password is incorrect
			//Response 2: +OK logged in.

			$this->logAct('$email->login(): Wrong password');
			$this->close();
			return false;
		}

		return true;
	}

	function pop3_STAT()
	{
		$this->write('STAT');

		//Response: +OK 0 0			first number means 0 unread mail. second means number of bytes for all mail
		$response = $this->read();
		if (!$this->is_ok($response)) {
			$this->close();
			$this->logAct('pop3_STAT(): STAT error');
			return false;
		}

		$arr = explode(' ', $response);
		$this->unread_mails = $arr[1];
		$this->tot_bytes = $arr[2];

		return true;
	}

	/* Ask the server for size of a specific message */
	function pop3_LIST($_id)
	{
		if (!is_numeric($_id)) return false;

		$this->write('LIST '.$_id);
		
		//Response: +OK 1 1234		first number = $_id, sencond number is bytes in current mail
		$response = $this->read();
		if (!$this->is_ok($response)) {
			$this->logAct('pop3_LIST(): Failed to LIST '.$_id);
			return false;
		}

		$arr = explode(' ', $response);
		return intval($arr[2]);	//returns number of bytes in current mail
	}

	/* Tells the server to delete a mail */
	function pop3_DELE($_id)
	{
		if (!is_numeric($_id)) return false;

		$this->write('DELE '.$_id);
		if (!$this->is_ok()) {
			$this->logAct('pop3_DELE() Failed to DELE '.$_id);
			return false;
		}
		return true;
	}

	function pop3_RETR($_id)
	{
		if (!is_numeric($_id)) return false;

		set_time_limit(30);

		//Retrieve email
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
	
	/* takes a text string with email header and returns array */
	//current limitation: multiple keys with same name will just be glued together (Received are one such common header key)
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

	/* Takes a raw email (including headers) as parameter, returns all attachments, body & header nicely parsed up
		also automatically extracts file attachments and handles them as file uploads
		allowed file types as attachments are $config['email']['attachments_allowed_mime_types']
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
		
		$result['mms_code'] = findMMSCode($result['header']['Subject']);
		if (!$result['mms_code']) {
			$this->logAct(htmlentities($result['header']['From']).': Invalid MMS code (title: '.$result['header']['Subject'].'), skipping mail');
			return false;
		}

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
				if ($pos === false) die('multipart header err');
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
						$this->logAct('Attachment mime type unrecognized: '. $attachment_mime);
						continue;
					}
					$this->saveFile($filename, $attachment_mime, base64_decode($attachment['body']), $result['mms_code']);
					break;

				default:
					$this->logAct('Unknown transfer encoding: '. $attachment['head']['Content-Transfer-Encoding']);
					break;
			}
		}

		return $result;
	}

	/* fetches all mail from a pop3 inbox */
	function getMail($user, $pass)
	{
		set_time_limit(30);

		$this->open();
		if (!$this->handle || $this->errno) return;
		
		if ($this->login($user, $pass) === false) return;

		$mail = array();
		$ret = array();
		
		if (!$this->pop3_STAT()) return;

		if (!$this->unread_mails) {
			$this->logAct('no new mail');
		} else {
			$this->logAct($this->unread_mails.' new mail(s)');
		}
		
		for ($i=1; $i <= $this->unread_mails; $i++)
		{
			$msg_size = $this->pop3_LIST($i);
			if (!$msg_size) continue;

			$this->logAct('Downloading #'.$i.'... ('.$msg_size.' bytes)');

			$check = $this->pop3_RETR($i);
			if ($check) $this->pop3_DELE($i);
			sleep(2);
		}

		$this->close();
	}

	function saveFile($filename, $mime_type, $data, $mms_code)
	{
		global $config, $sql, $t;
		
		if ($mime_type == 'image/jpeg') {
			$filename = str_ireplace('.jpeg', '.jpg', $filename);
		}

		$filesize = strlen($data);

		$priv = 0;
		switch ($mms_code['cmd'])
		{
			case 'GALL':
				$tmp = md5(microtime());
				$file_ext = explode('.', $filename);
				$file_ext = stripslashes(strtolower($file_ext[count($file_ext)-1]));

				$pht_cmt = 'MMS - '.strip_tags($filename);

				$q = "INSERT INTO s_userphoto SET picd = '".PD."', old_filename = '$filename', user_id = ".$mms_code['user'].", pht_date = NOW(), status_id='1', hidden_id = '$priv', pht_name = '".$file_ext."', pht_size = '".$filesize."', pht_cmt = '$pht_cmt'";
				$insert_id = $sql->queryInsert($q);

				$out_filename = WEBROOT.USER_GALLERY.PD.'/'.$insert_id.'.'.$file_ext;
				$out_thumbname = WEBROOT.USER_GALLERY.PD.'/'.$insert_id.'-tmb.'.$file_ext;
				$this->logAct('Writing to file '.$out_filename.' ...');
				file_put_contents($out_filename, $data);

				//skapa thumb
				make_thumb($out_filename, $out_thumbname, 100, 89);
				break;

			default:
				$this->logAct('Unhandled MMS command: '. $mms_code['cmd']);
				echo 'Unhandled MMS command: '. $mms_code['cmd'].'<br/>';
				return;
		}

		$this->logAct('Written '.$filename.' ('.$mime_type.') to disk.');

	}
}

function make_thumb($src, $dst, $dstWW = 90, $quality = 91)
{
	if (!file_exists($src) || (is_dir($dst) && $dst != '')) return false;

	$info = getimagesize($src);
	switch ($info[2]) {
		case IMAGETYPE_GIF: $im_src = imagecreatefromgif($src); break;
		case IMAGETYPE_JPEG: $im_src = imagecreatefromjpeg($src); break;
		case IMAGETYPE_PNG: $im_src = imagecreatefrompng($src); break;
	}
	if ($info[0] >= $dstWW) {
		$thumb_width = $dstWW;
		$thumb_height = ($info[1] * ($dstWW / $info[0]));
	} else {
		$thumb_width = ($info[0] * ($dstWW / $info[1]));
		$thumb_height = $dstWW;
	}

	$img_thumb = imagecreatetruecolor($thumb_width, $thumb_height);
	imagecopyresampled($img_thumb, $im_src, 0, 0, 0, 0, $thumb_width, $thumb_height, $info[0], $info[1]);

	imagejpeg($img_thumb, $dst, $quality);

	imagedestroy($img_thumb);
	imagedestroy($im_src);
	return true;
}

/*	Identifierar mms koder i formatet:
		BLOG 12345
		PRES 12345
		GALL 12345
*/
function findMMSCode($text)
{
	global $sql, $email;

	//echo 'looking for mms code: '.$text.'<br>';

	$text = strtoupper(trim(str_replace('  ', ' ', $text)));
	$text = str_replace('SPAML: ', '', $text);		//remove spam assassin-tagged mail subject

	if (strlen($text) < 5 || strlen($text) > 20) return false;

	$arr = explode(' ', $text);
	if (count($arr) < 2) return false;

	$mms_code['code'] = $arr[1];

	$blog_aliases = array('BLOG', 'BLOGG');
	$pres_aliases = array('PRES', 'PRESS', 'PRESENTATION');
	$gall_aliases = array('GALL', 'GALLERI', 'GALLERY', 'GALERI');

	if (in_array($arr[0], $blog_aliases)) $mms_code['cmd'] = 'BLOG';
	if (in_array($arr[0], $pres_aliases)) $mms_code['cmd'] = 'PRES';
	if (in_array($arr[0], $gall_aliases)) $mms_code['cmd'] = 'GALL';

	$q = 'SELECT owner_id FROM s_obj WHERE content_type="mmskey" AND content="'.secureINS($mms_code['code']).'" LIMIT 1';
	$mms_code['user'] = $sql->queryResult($q);
	if (!$mms_code['user']) return false;
	
	$email->logAct('Identified MMS type '.$mms_code['cmd'].' from user '.$mms_code['user'].'...');
	return $mms_code;
}
?>