<?
	require_once('config.php');

	require('design_head.php');

	createMenu($user_menu, 'blog_menu');

	$menu = array($_SERVER['PHP_SELF'] => 'Blogs');

	if ($session->id) {
		$menu = array_merge($menu, array('blog_new.php' => 'New blog'));
	}

	if ($session->isAdmin) {
		$menu = array_merge($menu, array('blog_archive.php?y=2006&amp;m=7' => 'Blog archive'));
		$menu = array_merge($menu, array('blog_categories.php' => 'Blog categories'));
	}

	echo createMenu($menu, 'blog_menu');

	echo 'Newest blogs:<br/>';
	$list = getLatestBlogs(5);
	for ($i=0; $i<count($list); $i++) {
		echo '<a href="blog.php?Blog:'.$list[$i]['blogId'].'">'.$list[$i]['blogTitle'].'</a> - '.$list[$i]['timeCreated'];
		echo ' by '.nameLink($list[$i]['userId'], $list[$i]['userName']).'<br/>';
	}

	require('design_foot.php');
?>