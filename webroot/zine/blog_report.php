<?
	//id = blogId

	require_once('config.php');

	$session->requireLoggedIn();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$blogId = $_GET['id'];
	$data = getBlog($blogId);

	if (isset($_POST['reason'])) {
		$queueId = addToModerationQueue($db, $blogId, MODERATION_REPORTED_BLOG);
		addComment($db, COMMENT_MODERATION_QUEUE, $queueId, $_POST['reason']);

		header('Location: blog_show.php?id='.$blogId);
		die;
	}

	include('design_head.php');

		$content  = 'Report blog - <b>'.$data['blogTitle'].'</b><br><br>';

		$content .= '<br><br>';
		$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$blogId.'">';
		$content .= 'Why do you want to report this:<br>';
		$content .= '<textarea name="reason" cols="64" rows="6"></textarea><br/><br/>';

		$content .= '<input type="submit" class="button" value="Report">';
		$content .= '</form><br><br>';

		$content .= '<a href="blogs_show.php?id='.$blogId.'">Back to blog overview</a>';

		echo $content;

	include('design_foot.php');

?>