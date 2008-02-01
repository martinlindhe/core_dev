<?
	require_once('config.php');
	$session->requireLoggedIn();

	$userId = $session->id;
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$userId = $_GET['id'];
	}

	require('design_head.php');

	createMenu($profile_menu, 'blog_menu');
	
	showGuestbook($userId);

	require('design_foot.php');
?>