<?
	require_once('config.php');
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

	require('design_head.php');

	createMenu($profile_menu, 'blog_menu');

	echo '<h1>Abuse</h1>';
	echo 'If you want to block this user. Click here - fixme<br/><br/>';

	reportDialog(MODERATION_USER, $_GET['id']);

	require('design_foot.php');
?>