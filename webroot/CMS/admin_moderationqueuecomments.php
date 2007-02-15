<?
	include_once('include_all.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

	if (isset($_GET['id'])) {
		$queueId = $_GET['id'];
	} else {
		echo 'Inget id!';
		die;
	}

	$list = getComments($db, COMMENT_MODERATION_QUEUE, $queueId);
	$item = getModerationQueueItem($db, $queueId);

	$content = '';

	if ($item['queueType'] == MODERATION_REPORTED_USER) {
		$content .= 'Anm&auml;ld anv&auml;ndare:<br>';
		$content .= nameLink($item['itemId'], getUserName($db, $item['itemId'])).'<br><br>';
	} else if ($item['queueType'] == MODERATION_REPORTED_POST) {
		$content .= 'Anm&auml;lt inl&auml;gg:<br>';
		
		$forumPost = getForumItem($db, $item['itemId']);
		$content .= showForumPost($db, $forumPost, 'Anm&auml;lt inl&auml;gg', false);
	} else if ($item['queueType'] == MODERATION_REPORTED_PHOTO) {
		$content .= '<img src="file.php?id='.$item['itemId'].'&width='.$config['thumbnail_width'].'">';
	} else if ($item['queueType'] == MODERATION_REPORTED_BLOG) {
		$content .= '<a href="blog_show.php?id='.$item['itemId'].'" target="_blank">L&auml;s bloggen</a>';
	} else {
		$content .= 'Unknown type';
	}

	for ($i=0; $i<count($list); $i++) {
		$content .= '<div class="comment">';
		if ($list[$i]['userId'] == 0) {
			$content .= 'Anonym anm&auml;lare';
		} else {
			$content .= 'Anm&auml;lare '.nameLink($list[$i]['userId'], $list[$i]['userName']);
		}
		$content .= ', '.getRelativeTimeLong($list[$i]['commentTime']).': <br>';
		$content .= $list[$i]['commentText'].'</div><br>';
	}

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Motiveringar till modereringsobjekt', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>