<?
	require_once('../_mobil/config.php');

	echo '<pre>';
	
	require('functions_mms_fetch.php');

	$email = new email();
	$email->getMail('cs@inconet.se', '1111'); die;

	/*
	$text = file_get_contents('mms4.txt');
	$msg = $email->parseAttachments($text);
	print_r($msg);
	*/

?>