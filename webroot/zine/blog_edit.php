<?
	include('include_all.php');

	if (!$_SESSION['loggedIn'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$blogId = $_GET['id'];
	$blog = getBlog($db, $blogId);
	if (!$blog || $blog['userId'] != $_SESSION['userId']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_POST['catid']) && isset($_POST['title']) && isset($_POST['body'])) {
		updateBlog($db, $blogId, $_POST['catid'], $_POST['title'], $_POST['body']);
		$do_alert = true;
	}

	include('design_head.php');
	include('design_user_head.php');	

		$content = '<div style="float:right; width:150px;">';
			$c2 = '<a href="blog_show.php?id='.$blogId.'">'.$config['blog']['text']['show_blog'].'</a><br><br>';
			$c2 .= '<a href="blog_delete.php?id='.$blogId.'">'.$config['blog']['text']['delete_blog'].'</a>';
			$content .= MakeBox('|'.$config['text']['link_select'], $c2);
		$content .= '</div>';

		$blog = getBlog($db, $blogId);
		$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$blogId.'">';
		$content .= $config['text']['title'].':<br><input type="text" name="title" value="'.$blog['blogTitle'].'" size=40 maxlength=40><br>';
		$content .= $config['text']['published'].': '. getRelativeTimeLong($blog['timeCreated']).' av '.nameLink($blog['userId'], $blog['userName']).'<br>';
		if ($blog['timeUpdated']) {
			$content .= '<b>'.$config['text']['updated'].': '. getRelativeTimeLong($blog['timeUpdated']).'</b><br>';
		}
		$content .= '<br>';
		
		$body = trim($blog['blogBody']);
		//convert | to &amp-version since it's used as a special character:
		$body = str_replace('|', '&#124;', $body);	//	|		vertical bar
		$body = $body."\n";	//always start with an empty line when getting focus

		$content .= $config['text']['category'].':<br>';
		$content .= '<select name="catid">';
		$content .= getCategoriesHTML_Options($db, CATEGORY_BLOGS, $blog['categoryId']);
		$content .= '</select><br><br>';

		$content .= '<textarea name="body" cols=65 rows=25>'.$body.'</textarea><br><br>';
		$content .= '<input type="submit" class="button" value="'.$config['text']['link_save_changes'].'"><br>';
		$content .= '</form><br>';

		//$content .= showFileAttachments($db, $blogId, FILETYPE_BLOG);

		echo '<div id="user_blog_content">';
		echo MakeBox('<a href="blogs.php">'.$config['blog']['text']['blogs'].'</a>|'.$config['text']['link_edit'], $content);
		echo '</div>';

	include('design_blog_foot.php');
	include('design_foot.php');
	
	if (!empty($do_alert)) {
		JS_Alert('Blog have been updated!');
	}
?>