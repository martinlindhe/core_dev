<?
	require_once('/home/martin/mobil.citysurf.tv/config.php');

	echo '<pre>';
	
	require('functions_mms_fetch.php');

	$email = new email();
	$email->getMail('mms', 'mmsprocess556'); die;

	/*
	$text = file_get_contents('mms4.txt');
	$msg = $email->parseAttachments($text);
	print_r($msg);
	*/

?>