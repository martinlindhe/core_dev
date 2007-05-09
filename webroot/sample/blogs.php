<?
	require_once('config.php');

	$show = $session->id;
	if (isset($_GET['id']) && is_numeric($_GET['id'])) $show = $_GET['id'];

	require('design_head.php');
	
	wiki('Blog portal');

	$list = getBlogsByCategory($show);

	echo 'User #'.$show.' - blogs:<br/>';

	$shown_category = false;
	for ($i=0; $i<count($list); $i++) {
		if ($list[$i]['categoryId'] != $shown_category) {
			$catName = $list[$i]['categoryName'];
			if (!$catName) $catName = 'Uncategorized';
			echo '<br><b>'.$catName.'</b><br>';
			$shown_category = $list[$i]['categoryId'];
		}
		echo $list[$i]['timeCreated'].' - <a href="blog_show.php?Blog:'.$list[$i]['blogId'].'">'.$list[$i]['blogTitle'].'</a><br/>';
	}
	echo '<br/>';
		
	echo 'Newest blogs:<br/>';
	$list = getLatestBlogs(5);
	for ($i=0; $i<count($list); $i++) {
		echo '<a href="blog_show.php?Blog:'.$list[$i]['blogId'].'">'.$list[$i]['blogTitle'].'</a> - '.$list[$i]['timeCreated'];
		echo ' by '.nameLink($list[$i]['userId'], $list[$i]['userName']).'<br>';
	}

	if ($session->id) {
		echo '<a href="blog_new.php">New blog</a><br/>';
	}
	
	if ($session->isAdmin) {
		echo '<a href="blog_archive.php?y=2006&m=7">Blog categories</a><br/>';
		echo '<a href="blog_categories.php">Blog categories</a><br/>';
	}

	require('design_foot.php');
?>