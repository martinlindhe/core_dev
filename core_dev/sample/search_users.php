<?
	require_once('config.php');
	require('design_head.php');

	createMenu($user_menu, 'blog_menu');

	echo Users::search();

	require('design_foot.php');
?>
