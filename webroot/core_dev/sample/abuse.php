<?
	require_once('config.php');

	require('design_head.php');

	createMenu($profile_menu, 'blog_menu');

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	abuseReport($_GET['id']);

	require('design_foot.php');
?>