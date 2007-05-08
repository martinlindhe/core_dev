<?
	require_once('config.php');

	$session->requireLoggedIn();

	$show = $session->id;
	if (isset($_GET['id']) && is_numeric($_GET['id'])) $show = $_GET['id'];

	require('design_head.php');

		$list = getBlogsByCategory($show);
		$content = '<div style="float:right; width:150px;">';
			$c2  = '<a href="blog_new.php">New blog</a><br/><br/>';
			$c2 .= '<a href="blog_categories.php">New blog category</a>';
			$content .= $c2;
		$content .= '</div>';

		$content .= 'User #'.$show.' - blogs:<br/>';

		$shown_category = false;
		for ($i=0; $i<count($list); $i++) {
			if ($list[$i]['categoryId'] != $shown_category) {
				$catName = $list[$i]['categoryName'];
				if (!$catName) $catName = 'Uncategorized';
				$content .= '<br><b>'.$catName.'</b><br>';
				$shown_category = $list[$i]['categoryId'];
			}
			$content .= $list[$i]['timeCreated'].' - <a href="blog_show.php?id='.$list[$i]['blogId'].'">'.$list[$i]['blogTitle'].'</a><br/>';
		}
		$content .= '<br>';
		
		$content .= 'Newest blogs:<br>';
		$list = getLatestBlogs(5);
		for ($i=0; $i<count($list); $i++) {
			$content .= $list[$i]['timeCreated'].' - <a href="blog_show.php?id='.$list[$i]['blogId'].'">'.$list[$i]['blogTitle'].'</a> ';
			$content .= 'av '.nameLink($list[$i]['userId'], $list[$i]['userName']).'<br>';
		}

		echo '<div id="user_blog_content">';
		echo $content;
		echo '</div>';

	require('design_foot.php');
?>