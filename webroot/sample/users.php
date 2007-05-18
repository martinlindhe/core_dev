<?
	require_once('config.php');

	require('design_head.php');

	$menu = array(
			'blogs.php' => 'Blogs'
			);
	createMenu($menu, 'blog_menu');
	
	require('design_foot.php');
?>
