<?
	include('include_all.php');

	if (!$_SESSION['loggedIn']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_POST['title']) && isset($_POST['body']) && isset($_POST['catid'])) {
		$blogId = addBlog($db, $_POST['catid'], $_POST['title'], $_POST['body']);
		if ($blogId) {
			header('Location: blog_show.php?id='.$blogId);
			die;
		} else {
			JS_Alert('Problem att spare bloggen!');
		}
	}

	include('design_head.php');
	include('design_user_head.php');

	$content  = 'Lage en ny blogg<br><br>';
	$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	$content .= 'Titel:<br>';
	$content .= '<input type="text" name="title" size=67 maxlength=40><br>';
	$content .= '<br>';

	$content .= 'Velg kategori:<br>';
	$content .= '<select name="catid">';
	$content .= getCategoriesHTML_Options($db, CATEGORY_BLOGS);
	$content .= '</select><br><br>';

	$content .= '<textarea name="body" cols=64 rows=24></textarea><br><br>';
	$content .= '<input type="submit" class="button" value="'.$config['text']['link_save'].'">';
	$content .= '</form><br>';

	$content .= '<a href="javascript:history.go(-1);">'.$config['text']['link_return'].'</a>';

	echo '<div id="user_blog_content">';
	echo MakeBox('<a href="blogs.php">Blogger</a>|Ny blogg', $content);
	echo '</div>';

	include('design_blog_foot.php');
	include('design_foot.php');
?>