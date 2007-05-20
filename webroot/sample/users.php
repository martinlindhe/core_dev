<?
	require_once('config.php');

	require('design_head.php');

	$menu = array(
			'blogs.php' => 'Blogs',
			'search_users.php' => 'Search users',	//todo: implement
			'last_logged_in.php' => 'Last logged in'	//todo: implement
			);
	createMenu($menu, 'blog_menu');
	
	require('design_foot.php');
?>
