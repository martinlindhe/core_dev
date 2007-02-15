<?
	/* Returns the current chat buffer to the flash-chat */

	session_name('ABsessID');
	@session_start();		//Warning: session_start() [function.session-start]: Cannot send session cookie - headers already sent by (output started at E:\webroot\adblock\flashchat_recievemessage.php:1) in E:\webroot\adblock\flashchat_recievemessage.php on line 3
	header('Content-type: application/x-www-form-urlencoded');


  $userName = 'Guest';
  if (!empty($_SESSION['userName'])) $userName = $_SESSION['userName'];

	$logfile = 'e:/flashchat-buffer.txt';

	if (!file_exists($logfile)) {
		//fixme: returnera nåt
		die;
	}

	$data = file($logfile);
	//echo '<pre>'; print_r($data); die;

	//listar dom 10 _SISTA_ entrysarna i $data här:
	$chat = '';
	for ($i=count($data)-10; $i<count($data); $i++) {
		$chat .= $data[$i];
	}
	
	echo 'c='.urlencode(utf8_encode($chat)).'&q=2';
?>