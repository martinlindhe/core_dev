<?
/*
	email class.
	- läser in email från angiven address och söker igenom dessa efter "mms email", med bilagor
	
	Från början skrivet av Frans Rosén
	Uppdaterat av Martin Lindhe, 2007-04-13
	
	POP 3 command summary: http://www.freesoft.org/CIE/RFC/1725/8.htm
	
	mer exempel & info: http://www.thewebmasters.net/php/POP3.phtml
	
	//todo: gör en funktion som extractar "content-type" datan
	
*/

require('set_tmb.php');

//allowed mail attachment mime types
$config['email']['attachments_allowed_mime_types'] = array('image/jpeg', 'video/3gpp');
$config['email']['text_allowed_mime_types'] = array('text/plain');

$config['email']['upload_dir'] = './user_mms/';

	
class email
{
	var $hdl;
	var $boundary;
	var $errno;
	var $errstr;
	var $sql;
	var $flim = 300000;
	var $llim = array('6' => 5, '7' => 5, '10' => 0);


	//martins variabler:
	var $unread_mails = 0;		//STAT unreadmail totoctets
	var $tot_octets = 0;

	//todo: gör detta konfigurerbart
	var $pop3_server = 'mail.inconet.se';		// 'mail.unicorn.tv';
	var $pop3_port	 = 110;
	var $pop3_timeout = 5;

	function __construct($sql)
	{
		$this->sql = $sql;
	}

	private function open()
	{
		$this->hdl = fsockopen($this->pop3_server, $this->pop3_port, $this->errorno, $this->errstr, $this->pop3_timeout);
	}

	private function close()
	{
		$this->write('QUIT');
		$this->read();		//Response: +OK Bye-bye.

		fclose($this->hdl);
	}

	private function read()
	{
		$var = fgets($this->hdl, 128);
		//echo 'Read: '.$var.'<br/>';
		return $var;
	}

	private function write($line)
	{
		//echo 'Wrote: '.$line.'<br/>';
		fputs($this->hdl, $line."\r\n");
	}

	//Return true or false on +OK or -ERR
	function is_ok($cmd = '')
	{
		if (!$cmd) $cmd = $this->read();
		if (ereg("^\+OK", $cmd)) return true;
		return false;
	}

	private function login($user, $pass)
	{
		//todo: APOP support, http://tools.ietf.org/html/rfc1939#page-15
		//APOP username md5(server hash + ditt lösenord)
		$apop_check = $this->read();
		if (!$this->is_ok($apop_check)) {
			echo 'Server not allowing connections?';
			return false;
		}

		$this->write('USER '.$user);
		if (!$this->is_ok()) {
			//Expected response: +OK User:'martin@unicorn.tv' ok
			echo 'Invalid username?';
			return false;
		}

		$this->write('PASS '.$pass);
		if (!$this->is_ok()) {
			//Response 1: -ERR UserName or Password is incorrect
			//Response 2: +OK logged in.

			echo 'Wrong password';

			$this->close();
			return false;
		}

		return true;
	}

	function pop3_STAT()
	{
		$this->write('STAT');

		//Response: +OK 0 0			first number means 0 unread mail. second means number of "octets" (totalt antal bytes i alla mailen)
		$stat_response = $this->read();
		if (!$this->is_ok($stat_response)) {
			$this->close();
			echo 'STAT error';
			return false;
		}

		$arr = explode(' ', $stat_response);
		$this->unread_mails = $arr[1];
		$this->tot_octets = $arr[2];

		return true;
	}

	/* Tells the server to delete a mail */
	function pop3_DELE($_id)
	{
		if (!is_numeric($_id)) return false;

		$this->write('DELE '.$_id);
		if (!$this->is_ok()) {
			echo 'Failed to DELE '.$_id.'<br/>';
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
		return $header;
	}	
	
	/*	Identifierar mms koder i formatet:
			BLOG 123456
			PRES 123456
			GALL 123456
	*/
	function findMMSCode($text)
	{
		global $sql;

		//echo 'looking for mms code: '.$text.'<br>';

		$text = strtoupper(trim(str_replace('  ', ' ', $text)));

		if (strlen($text) < 5 || strlen($text) > 20) return false;

		$arr = explode(' ', $text);
		if (count($arr) < 2) return false;

		$blog_aliases = array('BLOG', 'BLOGG');
		$pres_aliases = array('PRES', 'PRESS', 'PRESENTATION');
		$gall_aliases = array('GALL', 'GALLERI', 'GALLERY', 'GALERI');

		$mms_code['code'] = $arr[1];
		
		$q = 'SELECT owner_id FROM s_obj WHERE content_type="mmskey" AND content="'.secureINS($mms_code['code']).'" LIMIT 1';
		$mms_code['user'] = $sql->queryResult($q);
		if (!$mms_code['user']) return false;
		
		echo 'identified mms from user '.$mms_code['user'].'...<br>';
		
		if (in_array($arr[0], $blog_aliases)) {
			$mms_code['cmd'] = 'BLOG';
			return $mms_code;
		}

		if (in_array($arr[0], $pres_aliases)) {
			$mms_code['cmd'] = 'PRES';
			return $mms_code;
		}

		if (in_array($arr[0], $gall_aliases)) {
			$mms_code['cmd'] = 'GALL';
			return $mms_code;
		}

		return false;
	}
	
	/* Takes a email as parameter, returns all attachments, body & header nicely parsed up */
	//also automatically extracts file attachments and handles them as file uploads
	//allowed file types as attachments are $config['email']['attachments_allowed_mime_types']
	function parseAttachments($msg)
	{
		global $config;

		$msg = str_replace("\r\n", "\n", $msg);
		
		//echo $msg; die;

		//1. Klipp ut headern
		$pos = strpos($msg, "\n\n");
		if ($pos === false) return;

		$header = substr($msg, 0, $pos);
		//Parse each header element into an array
		$result['header'] = $this->parseHeader($header);
		
		$result['mms_code'] = $this->findMMSCode($result['header']['Subject']);
		if (!$result['mms_code']) {
			//todo: log failure
			echo $result['header']['From'].': No MMS code identified (title: '.$result['header']['Subject'].'), skipping mail<br/>';
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
			if ($part == 'multipart/mixed') {

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

		echo 'Splitting msg using id '.$multipart_id.'<br>';

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
			//Check attachment content type
			//echo 'Attachment type: '. $attachment['head']['Content-Type'].'<br>';
			
			$params = explode('; ', $attachment['head']['Content-Type']);
			//print_r($params);
			$attachment_mime = $params[0];

			$filename = '';
			if (!empty($attachment['head']['Content-Location'])) $filename = $attachment['head']['Content-Location'];
			if (!$filename) {
				//Extracta filename från: [Content-Type] => image/jpeg; name="header.jpg"
				//fixme fulhack:
				if (substr($params[1], 0, 5) == 'name=') {
					$filename = str_replace('"', '', substr($params[1], 5) );
				}
			}

			echo 'filename: '.$filename.'<br><br>';

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
						//echo 'Attachment mime type unrecognized: '. $attachment_mime.'<br>';
						continue;
					}
					$this->saveFile($filename, $attachment_mime, base64_decode($attachment['body']), $result['mms_code']);
					break;

				default:
					echo 'Unknown transfer encoding: '. $attachment['head']['Content-Transfer-Encoding'];
					break;
			}
		}

		return $result;
	}
	

	function parseFiles($mail)
	{
		global $t;
		$complete = false;
		$found = false;
		$needle = "\r\n";
		$this->sql->queryInsert('INSERT INTO s_aadata SET data_s = "'.$mail.'"');
		$mail = explode($needle, $mail);
		$start_collect = false;
		$collected = array();
		$active_file = -1;
		$from = '';
#print_r($mail);
#die();
		$text = '';
		$subj = '';
		$subj2 = '';
		$subj3 = '';
#print_r($mail);
		for ($i = 0; $i < count($mail); $i++) {
			$line = $mail[$i];
			if (substr($line, 0, 4) == '/9j/' || (@$mail[$i-1] && substr($mail[$i-1], 0, 31) == 'Content-Disposition: attachment')) {
				$start_collect = true;
				$active_file++;
				#$i = $i - 2;
			}
			if (substr($line, 0, 5) == 'From:') {
				$f = trim(substr($line, 5));
				#if(preg_match("/(.*?)/is", $line, $from_arr)) {
					#foreach($from_arr as $f) {
						if(!empty($f) && strpos($f, '@') !== false) {
							$from = str_replace('>', '', str_replace('<', '', $f));
						}
					#}
				#}
			}
			if (substr($line, 0, 8) == 'Subject:') {
				$subj = trim(substr($line, 8));
				if(substr($subj, 0, 5) == 'SPAM-' && strpos($subj, ':') !== false) {
					$subj = explode(':', $subj);
					unset($subj[0]);
					$subj = trim(implode(':', $subj));
				}
			}
			if (strtolower(substr($line, 0, 25)) == 'content-type: text/plain;') {
				$act = '';
				for ($ei = 0; $ei <= 10; $ei++) {
					$ex_line = $mail[$i+$ei];
					if (substr($ex_line, 0, 8) != 'Content-' && substr($ex_line, 0, 7) != 'charset') {
						if(substr($ex_line, 0, 7) == '------=') {
							break;
						}
						$act .= trim($ex_line);
					}
				}
				if (!empty($subj2)) {
					$subj3 = $act;
				} else $subj2 = $act;

			}

			if ($start_collect) {
				if(substr($line, 0, 4) == '----') $start_collect = false; else $collected[$active_file][] = $line;
			}
		}
		if (($subj || $subj2 || $subj3) && !$found) {		

			if (!empty($subj) && count($subj) >= 2) {
				$check = $this->sql->queryLine("SELECT u.id_id, u.level_id, a.last_date, a.last_times FROM {$t}user u LEFT JOIN {$t}userphotomms_limit a ON a.id_id = u.id_id WHERE u.u_alias = '".secureINS($subj[0])."' AND u.status_id = '1'");
				if (!empty($check) && count($check)) {
					if ($check[1] >= 6) {
						//todo martin: bort med $this->user !!!
						if($this->user->getinfo($check[0], 'mmsenabled') && strtolower($subj[1]) == strtolower($this->user->getinfo($check[0], 'mmskey'))) {
							$complete = true;
							$id_id = $check[0];
							$text = $subj;
							unset($text[0]);
							unset($text[1]);
							$text = implode(' ', $text);
						} # else wrong key
					} # else not gotit valid
				} # else wrong user
			} # else wrong format
			if (!$complete) {
				if (!empty($subj2) && count($subj2) >= 2) {
					$check = $this->sql->queryLine("SELECT u.id_id, u.level_id, a.last_date, a.last_times FROM {$t}user u LEFT JOIN {$t}userphotomms_limit a ON a.id_id = u.id_id WHERE u.u_alias = '".secureINS($subj2[0])."' AND u.status_id = '1'");
					if (!empty($check) && count($check)) {
						if ($check[1] >= 6) {
							//todo martin: bort med $this->user!!!
							if ($this->user->getinfo($check[0], 'mmsenabled') && strtolower($subj2[1]) == strtolower($this->user->getinfo($check[0], 'mmskey'))) {
								$complete = true;
								$id_id = $check[0];
								$text = $subj2;
								unset($text[0]);
								unset($text[1]);
								$text = implode(' ', $text);
							} # else wrong key
						} # else not gotit valid
					} # else wrong user
				} # else wrong format
			}
			if (!$complete) {
				if (!empty($subj3) && count($subj3) >= 2) {
					$check = $this->sql->queryLine("SELECT u.id_id, u.level_id, a.last_date, a.last_times FROM {$t}user u LEFT JOIN {$t}userphotomms_limit a ON a.id_id = u.id_id WHERE u.u_alias = '".secureINS($subj3[0])."' AND u.status_id = '1'");
					if (!empty($check) && count($check)) {
						if ($check[1] >= 6) {
							//todo martin: bort med $this->user!!!
							if ($this->user->getinfo($check[0], 'mmsenabled') && strtolower($subj3[1]) == strtolower($this->user->getinfo($check[0], 'mmskey'))) {
								$complete = true;
								$id_id = $check[0];
								$text = $subj3;
								unset($text[0]);
								unset($text[1]);
								$text = implode(' ', $text);
								} # else wrong key
						} # else not gotit valid
					} # else wrong user
				} # else wrong format
			}
			$found = true;
		}
		if ($complete) {
			if (!empty($check[2]) && $check[2] == date("Y-m-d") && @$this->llim[$check[1]] && $check[3] >= @$this->llim[$check[1]]) {
				//martin todo: bort med $this->user!!!
				$this->user->spy($check[0], 'MSG', 'MSG', array('Du har skickat maximalt antal MMS idag. Pröva igen imorgon.'));
				return false;
			} else {
				if (!empty($check[2])) {
					if ($check[2] == date("Y-m-d"))
						$this->sql->queryUpdate("UPDATE {$t}userphotomms_limit SET last_times = last_times + 1 WHERE id_id = '".$check[0]."' LIMIT 1");
					else
						$this->sql->queryUpdate("UPDATE {$t}userphotomms_limit SET last_date = NOW(), last_times = 1 WHERE id_id = '".$check[0]."' LIMIT 1");
				} else {
					$this->sql->queryInsert("INSERT INTO {$t}userphotomms_limit SET last_date = NOW(), last_times = 1, id_id = '".$check[0]."'");
				}
				$files = array();
				$total = 0;
				foreach ($collected as $c) {
					#if(!empty($c)) {
						$c = implode('', $c);
						if(substr($c, 0, 6) != '<smil>') {
							$n = count(preg_split("`.`", $c)) - 1;
							$files[] = array($c, $n);
							$total += $n;
						}
					#}
				}
				if ($total <= $this->flim)
					return array($files, $from, $id_id, $text, $total);
				else return false;
			}
		} else return false;
	}

	/* fetches all mail from a pop3 inbox */
	function getMail($user, $pass)
	{
		set_time_limit(30);

		$this->open();
		if ($this->errno) return;

		if ($this->login($user, $pass) === false) return;
		
		$mail = array();
		$ret = array();
		
		if (!$this->pop3_STAT()) return;

		echo $this->unread_mails.' unread mails!<br/>';

		for ($i=1; $i <= $this->unread_mails; $i++)
		{
			sleep(2);
			echo 'Downloading #'.$i.'...<br/>';

			$active = $this->pop3_RETR($i);
	
			$this->pop3_DELE($i);
		}

		$this->close();
	}
	
	//file_put_contents($filename, base64_decode($attachment['body']));
	function saveFile($filename, $mime_type, $data, $mms_code)
	{
		global $config, $t;

		echo 'Writing '.$filename.' ('.$mime_type.') to disk...<br>';

		$filesize = strlen($data);
		$q = 'INSERT INTO tblMMSRecieved SET userId='.$mms_code['user'].', fileName="'.secureINS($filename).'", mimeType="'.secureINS($mime_type).'", fileSize='.$filesize.',timeRecieved=NOW()';
		$insert_id = $this->sql->queryInsert($q);
		if (!$insert_id) return false;

		file_put_contents($config['email']['upload_dir'].$insert_id, $data);

		$priv = 0;
		switch ($mms_code['cmd'])
		{
			case 'BLOG':
				$this->sql->queryInsert("INSERT INTO {$t}userblog SET blog_idx = NOW(), user_id = ".$mms_code['user'].", hidden_id = '$priv', blog_cmt = '".'<p align="center"><img src="'.$config['email']['upload_dir'].$insert_id.'" /></p>'."', blog_title = 'MMS', blog_date = NOW()");
				break;

			case 'GALL':
				$tmp = md5(microtime());
				$this->sql->queryInsert("INSERT INTO {$t}userphoto SET picd = '".PD."', user_id = ".$mms_code['user'].", pht_date = NOW(), hidden_id = '$priv', hidden_value = '".$tmp."', pht_name = '".secureINS($filename)."', pht_size = '".$filesize."', pht_cmt = 'MMS'");

			default:
				echo 'KAOS';
				break;
		}
	}

}
?>