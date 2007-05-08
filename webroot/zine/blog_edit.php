<?
	require_once('config.php');

	$session->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$blogId = $_GET['id'];
	$blog = getBlog($blogId);
	if (!$blog || $blog['userId'] != $session->id) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_POST['catid']) && isset($_POST['title']) && isset($_POST['body'])) {
		updateBlog($blogId, $_POST['catid'], $_POST['title'], $_POST['body']);
		$do_alert = true;
	}

	require('design_head.php');

	echo '<a href="blog_show.php?id='.$blogId.'">Show blog</a><br/><br/>';
	echo '<a href="blog_delete.php?id='.$blogId.'">Delete blog</a>';

	$blog = getBlog($blogId);

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$blogId.'">';
	echo 'Blog title:<br><input type="text" name="title" value="'.$blog['blogTitle'].'" size="40" maxlength="40"><br/>';
	echo 'Published at: '. $blog['timeCreated'].'. Written by '.nameLink($blog['userId'], $blog['userName']).'<br/>';
	if ($blog['timeUpdated']) {
		echo '<b>Last updated: '. $blog['timeUpdated'].'</b><br/>';
	}
	echo '<br/>';

	$body = trim($blog['blogBody']);
	//convert | to &amp-version since it's used as a special character:
	$body = str_replace('|', '&#124;', $body);	//	|		vertical bar
	$body = $body."\n";	//always start with an empty line when getting focus

	echo 'Category:<br/>';
	echo getCategoriesSelect(CATEGORY_BLOGS, 'catid', $blog['categoryId']);
	echo '<br/><br/>';

	echo '<textarea name="body" cols="65" rows="25">'.$body.'</textarea><br/><br/>';
	echo '<input type="submit" class="button" value="Save changes"/><br/>';
	echo '</form><br/>';

	require('design_foot.php');
?>