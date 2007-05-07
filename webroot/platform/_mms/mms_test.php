<?
	require_once('../_mobil/config.php');

	echo '<pre>';
	
	require('functions_mms_fetch.php');

	$email = new email($sql);
	$email->getMail('cs@inconet.se', '1111'); die;

/*
	$text = file_get_contents('mms4_fejk.txt');
	$msg = $email->parseAttachments($text);
	print_r($msg);
*/
?>