<?
/*
	i - file id
	a - angle to rotate

*/

	//todo: this path is not good!
	include('../adblock/config.php');

	header('Content-type: text/xml');
	echo '<?xml version="1.0" ?>';

	if (!$session->id || empty($_GET['i']) || !is_numeric($_GET['i']) || empty($_GET['a']) || !is_numeric($_GET['a'])) die('<bad/>');

	$_angle = $_GET['a'];

	echo '<ok/>';
?>