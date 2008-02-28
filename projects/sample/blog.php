<?
	require_once('config.php');
	require('design_head.php');

	createMenu($profile_menu, 'blog_menu');

	showBlog();

	require('design_foot.php');
?>