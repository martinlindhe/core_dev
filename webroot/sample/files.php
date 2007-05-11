<?
	require_once('config.php');
	
	$session->requireLoggedIn();

	require('design_head.php');

	$menu = array(
		'files.php' => 'My files',
		'blog.php' => 'My blogs',
		'settings.php' => 'Settings');

	createMenu($menu, 'blog_menu');

	$files->showFiles(FILETYPE_USERFILE);

	require('design_foot.php');
?>