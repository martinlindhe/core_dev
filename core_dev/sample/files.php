<?
	require_once('config.php');

	$session->requireLoggedIn();

	require('design_head.php');

	createMenu($profile_menu, 'blog_menu');
	
	$userId = $session->id;
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) $userId = $_GET['id'];
	
	$files->showFiles(FILETYPE_USERFILE, $userId);

	require('design_foot.php');
?>