<?
	require_once('config.php');

	$session->requireLoggedIn();

	if (isset($_POST['title']) && isset($_POST['body']) && isset($_POST['catid'])) {
		$blogId = addBlog($_POST['catid'], $_POST['title'], $_POST['body']);
		if ($blogId) {
			header('Location: blog_show.php?id='.$blogId);
			die;
		} else {
			JS_Alert('Problem att spare bloggen!');
		}
	}

	require('design_head.php');

	$content  = 'Create a new blog:<br/><br/>';
	$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	$content .= 'Title:<br/>';
	$content .= '<input type="text" name="title" size="67" maxlength="40"/><br/>';
	$content .= '<br/>';

	$content .= 'Select blog category:<br/>';
	$content .= getCategoriesSelect(CATEGORY_BLOGS, 'catid');
	$content .= '<br/><br/>';

	$content .= '<textarea name="body" cols="64" rows="24"></textarea><br/><br/>';
	$content .= '<input type="submit" class="button" value="Save"/>';
	$content .= '</form><br>';

	echo $content;

	require('design_foot.php');
?>