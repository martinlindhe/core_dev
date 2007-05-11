<?
	require_once('config.php');

	require('design_head.php');

	createMenu($profile_menu, 'blog_menu');

	if (empty($_GET)) {
		showUserBlogs();
	} else {
		showBlog();
	}

	require('design_foot.php');
?>