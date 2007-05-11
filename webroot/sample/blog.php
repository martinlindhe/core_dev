<?
	require_once('config.php');

	require('design_head.php');

	$menu = array(
		'files.php' => 'My files',
		'blog.php' => 'My blogs',
		'settings.php' => 'Settings');

	createMenu($menu, 'blog_menu');

	if (empty($_GET)) {
		showUserBlogs();
	} else {
		showBlog();
	}

	require('design_foot.php');
?>