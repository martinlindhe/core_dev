<?
	include('include_all.php');

	if (!$_SESSION['loggedIn'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$blogId = $_GET['id'];
	$blog = getBlog($db, $blogId);
	if (!$blog) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	/* Normal user tries to edit someone elses blog */
	if (!$_SESSION['isAdmin'] && ($blog['userId'] != $_SESSION['userId'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_POST['catid']) && isset($_POST['title']) && isset($_POST['body'])) {
		updateBlog($db, $blogId, $_POST['catid'], $_POST['title'], $_POST['body']);
		$do_alert = true;
	}

	include('design_head.php');

	$blog = getBlog($db, $blogId);

	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$blogId.'">';
	echo 'Titel:<br><input type="text" name="title" value="'.$blog['blogTitle'].'" size=60><br>';
	echo 'Publicerad: '. $blog['timeCreated'].' av '.$blog['userName'].'<br>';
	if ($blog['timeUpdated']) {
		echo '<b>Senast redigerad: '. $blog['timeUpdated'].'</b><br>';
	}
	echo '<br>';

	if (get_magic_quotes_gpc()) {
		$blog['blogBody'] = stripslashes($blog['blogBody']);
	}

	$body = trim($blog['blogBody']);
	//convert | to &amp-version since it's used as a special character:
	$body = str_replace('|', '&#124;', $body);	//	|		vertical bar
	$body = $body."\n";	//always start with an empty line when getting focus


	echo 'Kategori:<br>';
	echo '<select name="catid">'.getCategoriesHTML_Options($db, CATEGORY_BLOGS, $blog['categoryId']).'</select>';
	echo '<br><br>';

	echo '<textarea name="body" cols=65 rows=25>'.$body.'</textarea><br><br>';
	echo '<input type="submit" class="button" value="Spara &auml;ndringar"><br>';
	echo '</form><br>';

	if ($_SESSION['loggedIn']) {
		echo '<br><br><a href="blog_delete.php?id='.$blogId.'">Radera bloggen</a>';
	}

	include('design_foot.php');
	
	if (!empty($do_alert)) {
		JS_Alert('Blog have been updated!');
	}
?>