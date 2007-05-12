<?
	//todo: städa upp, färre switch:ar

	require_once('find_config.php');

	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	$list = getModerationQueue();
		
	foreach ($list as $row) {
		if (!isset($_POST['method_'.$row['queueId']])) continue;

		if ($_POST['method_'.$row['queueId']] == 'accept') {
			/* Accepts forum item and removes it from queue */
			deleteComments(COMMENT_MODERATION_QUEUE, $row['queueId']);
			removeFromModerationQueue($row['queueId']);
			continue;
		}

		switch ($list[$i]['queueType']) {
			case MODERATION_SENSITIVE_GUESTBOOK:
				removeGuestbookEntry($row['itemId']);
				removeFromModerationQueue($row['queueId']);
				break;

			case MODERATION_REPORTED_BLOG:
			case MODERATION_SENSITIVE_BLOG:
				deleteBlog($row['itemId']);
				removeFromModerationQueue($row['queueId']);
				break;
		}
	}

	$list = getModerationQueue();	//fixme: paging support
	if (count($list)) {
		
		echo 'Displaying '.count($list).' object(s) in the moderation queue. Showing oldest items first.<br/><br/>';
		
		echo '<form method="post" action="">';

		foreach ($list as $row) {
			echo '<div class="item">';

			$title = 'STATUS: ';
			switch ($row['queueType']) {
				case MODERATION_SENSITIVE_GUESTBOOK:$title .= 'Auto trigger: Sensitive guestbook'; break;
				case MODERATION_REPORTED_BLOG:			$title .= 'Reported blog'; break;
				case MODERATION_SENSITIVE_BLOG:			$title .= 'Auto trigger: Sensitive blog'; break;
				default: $title .= '<div class="critical">Unknown queueType '.$row['queueType'].', itemId '.$row['itemId'].'</div>';
			}
			echo '<div class="item_head">'.$title.'</div>';
			
			echo 'Triggered by '.getUserName($row['creatorId']).' at '.$row['timeCreated'].'<br/>';

			switch ($row['queueType']) {
				case MODERATION_SENSITIVE_GUESTBOOK:
					$gb = getGuestbookItem($row['itemId']);
					echo '<a href="'.$project.'guestbook.php?id='.$gb['userId'].'#gb'.$row['itemId'].'" target="_blank">Read the entry</a>';
					break;
				
				case MODERATION_REPORTED_BLOG:
				case MODERATION_SENSITIVE_BLOG:
					echo '<a href="'.$project.'blog_show.php?Blog:'.$row['itemId'].'" target="_blank">Read the blog</a>';
					break;
			}

			echo '<table width="100%"><tr><td width="50%">';
			echo '<input type="radio" class="radio" name="method_'.$row['queueId'].'" id="accept_'.$row['queueId'].'" value="accept"/>';
			echo '<label for="accept_'.$row['queueId'].'"> Accept</label>';
			echo '</td>';
			echo '<td>';
			echo '<input type="radio" class="radio" name="method_'.$row['queueId'].'" id="delete_'.$row['queueId'].'" value="delete"/>';
			echo '<label for="delete_'.$row['queueId'].'"> Delete</label>';
			echo '</td></tr></table>';

			if (
					($row['queueType'] == MODERATION_REPORTED_BLOG)
			) {
				$mcnt = getCommentsCount(COMMENT_MODERATION_QUEUE, $row['queueId']);
				if ($mcnt) {
					echo '<a href="admin_moderationqueuecomments.php?id='.$row['queueId'].getProjectPath().'">Motivations ('.$mcnt.')</a>';
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