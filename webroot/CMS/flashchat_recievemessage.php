<?
	/* tar emot nya meddelanden & returnerar aktuell chatlogg */

	//fixme: use flock eller nåt liknande

	session_name('ABsessID');
	@session_start();		//Warning: session_start() [function.session-start]: Cannot send session cookie - headers already sent by (output started at E:\webroot\adblock\flashchat_recievemessage.php:1) in E:\webroot\adblock\flashchat_recievemessage.php on line 3
	header('Content-type: application/x-www-form-urlencoded');

 	/* When a php-script is accessed directly from a flash file, HTTP_USER_AGENT="Shockwave Flash" but if run in a browser, it sends browser ID */

	$v = '';
	if (!empty($_GET['v'])) $v = trim(strip_tags($_GET['v']));

  $userName = 'Guest';
  if (!empty($_SESSION['userName'])) $userName = $_SESSION['userName'];

	$logTime = date('H:i');
	//fixme: <font color=#f0aa80> text </font> - renderar ibland, ibland inte wtf
	$currentLine = ''.$logTime.' [<b>'.$userName.'</b>] '.$v."\n";
	
	
	
	$logfile = 'e:/flashchat-buffer.txt';

	if (file_exists($logfile)) {
		$data = file($logfile);
	} else {
		$data = array();
	}


	$data[] = $currentLine;	//append current line at end of array

	
	//echo '<pre>'; print_r($data); die;
	
	//listar dom 10 _SISTA_ entrysarna i $data här:
	$chat = '';
	for ($i=count($data)-10; $i<count($data); $i++) {
		$chat .= $data[$i];
	}
	
	if ($v) {
		$fp = fopen($logfile, 'w');
		fwrite($fp, $chat);
		fclose($fp);
	}

	echo 'c='.urlencode(utf8_encode($chat)).'&q=1';
?>