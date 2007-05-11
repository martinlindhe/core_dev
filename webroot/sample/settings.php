<?
	require_once('config.php');
	
	$session->requireLoggedIn();

	require('design_head.php');

	createMenu($profile_menu, 'blog_menu');

	wiki('Settings');
	
	$session->editSettings();

	require('design_foot.php');
?>