<?
	require_once('config.php');
	
	$session->requireLoggedIn();

	require('design_head.php');

	createMenu($profile_menu, 'blog_menu');

	require('design_foot.php');
?>