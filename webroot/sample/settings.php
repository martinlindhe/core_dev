<?
	require_once('config.php');
	
	$session->requireLoggedIn();

	require('design_head.php');

	wiki('Settings');
	echo '<br/>';
	
	$files->showFiles(FILETYPE_USERFILE);

	$session->editSettings();

	require('design_foot.php');
?>