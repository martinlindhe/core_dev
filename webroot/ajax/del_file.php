<?
	header('Content-type: text/xml');
	echo '<?xml version="1.0" ?>';

	if (empty($_GET['i']) || !is_numeric($_GET['i'])) die('<bad/>');
	$id = $_GET['i'];
	
	//todo: this path is not good!
	include('../janina/config.php');
	if (!$session->id) die('<bad/>');

	$files->deleteFile($id);
	
	echo '<ok/>';
?>