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
			JS_Alert('Problem att spara bloggen!');
		}
	}

	include('design_head.php');

	echo 'Skapa en ny blogg<br><br>';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo 'Titel:<br>';
	echo '<input type="text" name="title" size=67><br>';
	echo '<br>';

	echo 'V&auml;lj kategori:<br>';
	echo '<select name="catid">'.getCategoriesHTML_Options($db, CATEGORY_BLOGS).'</select>';
	echo '<br><br>';

	echo '<textarea name="body" cols=64 rows=24></textarea><br><br>';
	echo '<input type="submit" class="button" value="'.$config['text']['link_save'].'">';
	echo '</form><br>';

	include('design_foot.php');
?>