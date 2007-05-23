<?
	/*
		Recieve a line of text specified in $_POST['t'] and store to database

		Output: None
	*/

	require_once('config.php');

	if (!$session->id) die;

	if (empty($_POST['t']) || empty($_POST['r']) || !is_numeric($_POST['r'])) die;

	$roomId = $_POST['r'];

	//cut out the first X letters
	$text = substr($_POST['t'], 0, $config['chat']['max_text_length']);

	addChatEntry($roomId, $text);

	echo 'OK';
?>