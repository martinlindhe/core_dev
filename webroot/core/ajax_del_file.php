<?
	/* ajax_del_file.php - deletes a file */

	require_once('find_config.php');

	header('Content-type: text/xml');
	echo '<?xml version="1.0" ?>';

	if (!$session->id || empty($_GET['i']) || !is_numeric($_GET['i'])) die('<bad/>');

	$files->deleteFile($_GET['i']);
	
	echo '<ok/>';
?>