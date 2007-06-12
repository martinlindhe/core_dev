<?
	require_once('find_config.php');

	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');
	
	if (empty($config['moderation']['enabled'])) {
		echo 'Moderation feature is not enabled';
		require($project.'design_foot.php');
		die;
	}

	$tot_cnt = getModerationQueueCount();
	
	$pager = makePager($tot_cnt, 5);

	$changed_list = false;
	
	$list = getModerationQueue($pager['limit']);
	foreach ($list as $row) {
		if (!isset($_POST['method_'.$row['queueId']])) continue;
		$changed_list = true;

		if ($_POST['method_'.$row['queueId']] == 'accept') {
			/* Accepts forum item and removes it from queue */
			deleteComments(COMMENT_MODERATION_QUEUE, $row['queueId']);
			removeFromModerationQueue($row['queueId']);
			continue;
		}

		switch ($row['queueType']) {
			case MODERATION_GUESTBOOK:
				removeGuestbookEntry($row['itemId']);
				removeFromModerationQueue($row['queueId']);
				break;

			case MODERATION_BLOG:
				deleteBlog($row['itemId']);
				removeFromModerationQueue($row['queueId']);
				break;
		}
	}

	if (!empty($_GET['comments'])) {

		$list = getComments(COMMENT_MODERATION_QUEUE, $_GET['comments']);
		foreach ($list as $row) {
			echo '<div class="comment">';
			if ($row['userId'] == 0) {
				echo 'Anonymous reporter';
			} else {
				echo 'Reported by '.nameLink($row['userId'], $row['userName']);
			}
			echo ', '.$row['timeCreated'].': <br>';
			echo $row['commentText'].'</div><br>';
		}

		require($project.'design_foot.php');
		die;
	}

	if ($changed_list) $list = getModerationQueue($pager['limit']);

	echo $pager['head'];

	if (count($list)) {

		echo 'Displaying '.count($list).' object(s) in the moderation queue. Showing oldest items first.<br/><br/>';
		
		echo '<form method="post" action="">';

		foreach ($list as $row) {
			echo '<div class="item">';

			switch ($row['queueType']) {
				case MODERATION_GUESTBOOK:$title = 'Guestbook entry'; break;
				case MODERATION_BLOG:			$title = 'Blog'; break;
				default: $title = '<div class="critical">Unknown queueType '.$row['queueType'].', itemId '.$row['itemId'].'</div>';
			}
			echo '<div class="item_head">'.$title;
			if ($row['autoTriggered']) echo ' (auto-triggered)';
			echo '</div>';
			
			echo 'By '.nameLink($row['creatorId'], $row['creatorName']).' at '.$row['timeCreated'].'<br/>';

			switch ($row['queueType']) {
				case MODERATION_GUESTBOOK:
					$gb = getGuestbookItem($row['itemId']);
					echo '<a href="'.$project.'guestbook.php?id='.$gb['userId'].getProjectPath().'#gb'.$row['itemId'].'" target="_blank">Read the entry</a>';
					break;

				case MODERATION_BLOG:
					echo '<a href="'.$project.'blog.php?Blog:'.$row['itemId'].getProjectPath().'" target="_blank">Read the blog</a>';
					break;
			}

			echo '<table summary="" width="100%"><tr><td width="50%">';
			echo '<input type="radio" class="radio" name="method_'.$row['queueId'].'" id="accept_'.$row['queueId'].'" value="accept"/>';
			echo '<label for="accept_'.$row['queueId'].'"> Accept</label>';
			echo '</td>';
			echo '<td>';
			echo '<input type="radio" class="radio" name="method_'.$row['queueId'].'" id="delete_'.$row['queueId'].'" value="delete"/>';
			echo '<label for="delete_'.$row['queueId'].'"> Delete</label>';
			echo '</td></tr></table>';

			if (!$row['autoTriggered']) {
				$mcnt = getCommentsCount(COMMENT_MODERATION_QUEUE, $row['queueId']);
				if ($mcnt) {
					echo '<a href="?comments='.$row['queueId'].getProjectPath().'">Motivations ('.$mcnt.')</a>';
				} else {
					echo 'Motivations (0)';
				}
			}

			echo '</div>'; //class="item"
			echo '<br/>';
		}
		echo '<input type="submit" class="button" value="Commit changes"/>';
		echo '</form>';
	} else {
		echo 'The moderation queue is empty!<br/>';
	}

	require($project.'design_foot.php');
?>