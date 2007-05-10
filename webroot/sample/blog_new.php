<?
	require_once('config.php');

	$session->requireLoggedIn();

	if (isset($_POST['title']) && isset($_POST['body']) && isset($_POST['catid'])) {
		$blogId = addBlog($_POST['catid'], $_POST['title'], $_POST['body']);
		if ($blogId) {
			header('Location: blog_show.php?id='.$blogId);
			die;
		} else {
			die('error saving blog');
		}
	}

	require('design_head.php');

	echo 'Create a new blog:<br/><br/>';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo 'Title:<br/>';
	echo '<input type="text" name="title" size="67" maxlength="40"/><br/>';
	echo '<br/>';

	echo 'Select blog category:<br/>';
	echo getCategoriesSelect(CATEGORY_BLOG, 'catid');
	echo '<br/><br/>';

	echo '<textarea name="body" cols="64" rows="24"></textarea><br/><br/>';
	echo '<input type="submit" class="button" value="Save"/>';
	echo '</form><br/>';

	require('design_foot.php');
?>