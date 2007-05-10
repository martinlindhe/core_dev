<?
	require_once('config.php');
	
	$session->requireLoggedIn();

	require('design_head.php');

	$files->showFiles(FILETYPE_USERFILE);

	require('design_foot.php');
?>