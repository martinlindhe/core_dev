<?
	require_once('config.php');
	
	$session->requireLoggedIn();

	require('design_head.php');

	wiki('Personal files');

	$files->showFiles(FILETYPE_USERFILE);

	require('design_foot.php');
?>