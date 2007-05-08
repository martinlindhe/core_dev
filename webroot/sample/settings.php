<?
	require_once('config.php');
	
	$session->requireLoggedIn();

	require('design_head.php');

	wiki('Settings');
	
	$session->editSettings();

	require('design_foot.php');
?>