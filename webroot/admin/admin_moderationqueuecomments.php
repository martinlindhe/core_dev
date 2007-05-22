<?
	require_once('find_config.php');

	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die;
	$queueId = $_GET['id'];

	$item = getModerationQueueItem($queueId);
	switch ($item['queueType']) {
		case MODERATION_REPORTED_BLOG:
			echo '<a href="'.$project.'blog.php?Blog:'.$item['itemId'].'" target="_blank">Read the blog</a>';
			break;

		default:
			echo('Unknown type');
	}

	$list = getComments(COMMENT_MODERATION_QUEUE, $queueId);
	for ($i=0; $i<count($list); $i++) {
		echo '<div class="comment">';
		if ($list[$i]['userId'] == 0) {
			echo 'Anonymous reporter';
		} else {
			echo 'Reported by '.nameLink($list[$i]['userId'], $list[$i]['userName']);
		}
		echo ', '.$list[$i]['timeCreated'].': <br>';
		echo $list[$i]['commentText'].'</div><br>';
	}

	require($project.'design_foot.php');
?>