<?
	//id = blogId

	include('include_all.php');

	if (!$_SESSION['loggedIn'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$blogId = $_GET['id'];
	$data = getBlog($db, $blogId);
/*	if (!$data || $data['userId'] == $_SESSION['userId']) {
		header('Location: '.$config['start_page']);
		die;
	}
	*/
	if (isset($_POST['reason'])) {
		$queueId = addToModerationQueue($db, $blogId, MODERATION_REPORTED_BLOG);
		addComment($db, COMMENT_MODERATION_QUEUE, $queueId, $_POST['reason']);

		header('Location: blog_show.php?id='.$blogId);
		die;
	}
	

	include('design_head.php');
	include('design_user_head.php');

		$content  = 'Rapporter blogg - <b>'.$data['blogTitle'].'</b><br><br>';

		$content .= '<br><br>';
		$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$blogId.'">';
		$content .= 'Begrunn anmeldelsen:<br>';
		$content .= '<textarea name="reason" cols=64 rows=6></textarea><br><br>';

		$content .= '<input type="submit" class="button" value="'.$config['text']['link_report'].'">';
		$content .= '</form><br><br>';

		$content .= '<a href="blogs_show.php?id='.$blogId.'">Tilbake til blogg</a>';

		echo '<div id="user_fotoalbum_content">';
		echo MakeBox('<a href="photoalbums.php?id='.$show.'">Fotoalbum</a>', $content);
		echo '</div>';

	include('design_photos_foot.php');
	include('design_foot.php');

?>