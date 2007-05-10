<?
	//todo: städa upp, färre switch:ar

	require_once('find_config.php');

	$session->requireAdmin();

	require($project.'design_head.php');

	if (isset($_POST['datasent'])) {
		$list = getModerationQueue();
		
		for ($i=0; $i<count($list); $i++) {
			if (isset($_POST['method_'.$list[$i]['queueId']])) {
				$method = $_POST['method_'.$list[$i]['queueId']];
				
				if ($method == 'accept') {
					/* Accepts forum item and removes it from queue */
					deleteComments(COMMENT_MODERATION_QUEUE, $list[$i]['queueId']);
					removeFromModerationQueue($list[$i]['queueId']);
				} else {
					switch ($list[$i]['queueType']) {
						case MODERATION_REPORTED_BLOG:
						case MODERATION_SENSITIVE_BLOG:
							deleteBlog($list[$i]['itemId']);
							removeFromModerationQueue($list[$i]['queueId']);
							break;
					}
				}
			}
		}
	}

	$list = getModerationQueue();
	if (count($list)) {
		
		echo count($list).' objekt<br><br>';
		
		echo '<form method="post" action="">';
		echo '<input type="hidden" name="datasent" value="1">';

		for ($i=0; $i<count($list); $i++) {
			echo '<table width="100%" border=0 cellspacing=0 cellpadding=1 bgcolor="#000000" height="*"><tr><td>';
			echo '<table width="100%" cellpadding=2 cellspacing=0 border=0 bgcolor="#FFFFFF">';
			echo '<tr><td colspan=3>';

			$title = 'STATUS: ';
			switch ($list[$i]['queueType']) {
				case MODERATION_REPORTED_BLOG:			$title .= 'Rapporterad blogg'; break;
				case MODERATION_SENSITIVE_BLOG:			$title .= 'Autotriggat k&auml;nslig blogg'; break;
				default: $title .= '<span class="objectCritical">Ok&auml;nd queueType '.$list[$i]['queueType'].', itemId '.$list[$i]['itemId'].'</span>';
			}
			echo '<b>'.$title.'</b><br/>';

			switch ($list[$i]['queueType']) {
				case MODERATION_REPORTED_BLOG:
				case MODERATION_SENSITIVE_BLOG:
					echo '<a href="'.$project.'blog_show.php?Blog:'.$list[$i]['itemId'].'" target="_blank">Read the blog</a>';
					break;
			}
			echo '</td></tr>';
			echo '<tr>';
			echo '<td width="40%"><input type="radio" class="radio" name="method_'.$list[$i]['queueId'].'" value="accept"> Accept</td>';
			
			echo '<td><input type="radio" class="radio" name="method_'.$list[$i]['queueId'].'" value="delete"> Delete';
			echo '</td>';
			
			
			echo '<td width="25%">';
				if (
						($list[$i]['queueType'] == MODERATION_REPORTED_BLOG)
							) {
					$mcnt = getCommentsCount(COMMENT_MODERATION_QUEUE, $list[$i]['queueId']);
					if ($mcnt) {
						echo '<a href="admin_moderationqueuecomments.php?id='.$list[$i]['queueId'].getProjectPath().'">Motiveringar ('.$mcnt.') &raquo;</a>';
					} else {
						echo 'Motiveringar (0)';
					}
				} else {
					echo '&nbsp;';
				}
			echo '</td>';
			echo '</tr>';
			echo '</table>';
			echo '</td></tr></table>';
			echo '<br>';
		}
		echo '<input type="submit" class="button" value="Commit changes">';
		echo '</form>';
	} else {
		echo 'The moderation queue is empty!<br>';
	}

	require($project.'design_foot.php');
?>