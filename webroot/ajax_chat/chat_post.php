<?
	/*
		Recieve a line of text specified in $_POST['t'] and store to database
		
		Output: None
	*/

	include_once('include_all.php');

	if (!$_SESSION['loggedIn']) die;

	if (empty($_POST['t']) || empty($_POST['r']) || !is_numeric($_POST['r'])) die;

	$roomId = $_POST['r'];
	
	//cut out the first X letters	
	$text = mb_substr($_POST['t'], 0, $config['chat']['max_text_length']);

	addChatEntry($db, $roomId, $text);
?>