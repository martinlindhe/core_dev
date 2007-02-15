<?
	//todo: städa upp, färre switch:ar

	include_once('include_all.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

	if (isset($_POST['datasent'])) {
		$list = getModerationQueue($db);
		
		for ($i=0; $i<count($list); $i++) {
			if (isset($_POST['method_'.$list[$i]['queueId']])) {
				$method = $_POST['method_'.$list[$i]['queueId']];
				
				if ($method == 'accept') {
					/* Accepts forum item and removes it from queue */
					deleteComments($db, COMMENT_MODERATION_QUEUE, $list[$i]['queueId']);
					removeFromModerationQueue($db, $list[$i]['queueId']);
				} else {
					switch ($list[$i]['queueType']) {
						case MODERATION_REPORTED_POST:
						case MODERATION_OBJECTIONABLE_POST:
						case MODERATION_SENSITIVE_POST:
							/* Delete forum item (recursive) + delete from queue */
							deleteForumItemRecursive($db, $list[$i]['itemId']);
							//deleteForumItem($db, $list[$i]['itemId']);
							removeFromModerationQueue($db, $list[$i]['queueId']);
							break;
							
						case MODERATION_SENSITIVE_GUESTBOOK:
							/* Delete guestbook entry */
							removeGuestbookEntry($db, $list[$i]['itemId']);
							removeFromModerationQueue($db, $list[$i]['queueId']);
							break;
							
						case MODERATION_REPORTED_PHOTO:
							deleteFile($db, $list[$i]['itemId']);
							removeFromModerationQueue($db, $list[$i]['queueId']);
							break;
						
						case MODERATION_REPORTED_BLOG:
						case MODERATION_SENSITIVE_BLOG:
							deleteBlog($db, $list[$i]['itemId']);
							removeFromModerationQueue($db, $list[$i]['queueId']);
							break;
					}
				}
			}
		}
	}

	$content = '';

	$list = getModerationQueue($db);
	if (count($list)) {
		
		$content .= count($list).' objekt<br><br>';
		
		$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
		$content .= '<input type="hidden" name="datasent" value="1">';

		for ($i=0; $i<count($list); $i++) {
			$content .= '<table width="100%" border=0 cellspacing=0 cellpadding=1 bgcolor="#000000" height="*"><tr><td>';
			$content .= '<table width="100%" cellpadding=2 cellspacing=0 border=0 bgcolor="#FFFFFF">';
			$content .= '<tr><td colspan=3>';

			$title = 'STATUS: ';
			switch ($list[$i]['queueType']) {
				case MODERATION_REPORTED_POST:			$title .= 'Rapporterat inl&auml;gg'; break;
				case MODERATION_OBJECTIONABLE_POST:	$title .= 'Autotriggat st&ouml;tande'; break;
				case MODERATION_SENSITIVE_POST:			$title .= 'Autotriggat k&auml;nsligt inneh&aring;ll'; break;
				case MODERATION_REPORTED_USER:			$title .= 'Rapporterad anv&auml;ndare'; break;
				case MODERATION_SENSITIVE_GUESTBOOK: $title .= 'Autotriggat k&auml;nsligt g&auml;stboksinl&auml;gg'; break;
				case MODERATION_REPORTED_PHOTO:			$title .= 'Rapporterad bild'; break;
				case MODERATION_REPORTED_BLOG:			$title .= 'Rapporterad blogg'; break;
				case MODERATION_SENSITIVE_BLOG:			$title .= 'Autotriggat k&auml;nslig blogg'; break;
				default: $title .= '<span class="objectCritical">Ok&auml;nd queueType '.$list[$i]['queueType'].', itemId '.$list[$i]['itemId'].'</span>';
			}
			$content .= '<b>'.$title.'</b><br>';

			switch ($list[$i]['queueType']) {
				case MODERATION_REPORTED_USER:
					$content .= nameLink($list[$i]['itemId'], getUserName($db, $list[$i]['itemId'])).'<br>';
					break;
				
				case MODERATION_REPORTED_POST:
				case MODERATION_OBJECTIONABLE_POST:
				case MODERATION_SENSITIVE_POST:
					$forumPost = getForumItem($db, $list[$i]['itemId']);
					$content .= showForumPost($db, $forumPost, 'Anm&auml;lt inl&auml;gg', false);
					break;

				case MODERATION_SENSITIVE_GUESTBOOK:
					$entry = getGuestbookItem($db, $list[$i]['itemId']);
					$content .= nameLink($entry['authorId'], $entry['authorName']).' skrev f&ouml;ljande i '.nameLink($entry['userId'], $entry['userName']).' g&auml;stbok '.getRelativeTimeLong($entry['timestamp']).':<br>';
					$content .= $entry['body'];
					break;
					
				case MODERATION_REPORTED_PHOTO:
					$content .= '<img src="file.php?id='.$list[$i]['itemId'].'&width='.$config['thumbnail_width'].'">';
					break;
					
				case MODERATION_REPORTED_BLOG:
				case MODERATION_SENSITIVE_BLOG:
					$content .= '<a href="blog_show.php?id='.$list[$i]['itemId'].'" target="_blank">L&auml;s bloggen</a>';
					break;
			}
			$content .= '</td></tr>';
			$content .= '<tr>';
			$content .= '<td width="40%"><input type="radio" class="radio" name="method_'.$list[$i]['queueId'].'" value="accept"> Acceptera</td>';
			
			$content .= '<td><input type="radio" class="radio" name="method_'.$list[$i]['queueId'].'" value="delete"> '.$config['text']['link_remove'];
				if ($list[$i]['queueType'] == MODERATION_REPORTED_POST) {
					$content .= ' (+ '.getForumMessageCount($db, $list[$i]['itemId']).' svar)';
				}
			$content .= '</td>';
			
			
			$content .= '<td width="25%">';
				if (	($list[$i]['queueType'] == MODERATION_REPORTED_POST) ||
							($list[$i]['queueType'] == MODERATION_REPORTED_USER) ||
							($list[$i]['queueType'] == MODERATION_REPORTED_PHOTO) ||
							($list[$i]['queueType'] == MODERATION_REPORTED_BLOG)
							) {
					$mcnt = getCommentsCount($db, COMMENT_MODERATION_QUEUE, $list[$i]['queueId']);
					if ($mcnt) {
						$content .= '<a href="admin_moderationqueuecomments.php?id='.$list[$i]['queueId'].'">Motiveringar ('.$mcnt.') &raquo;</a>';
					} else {
						$content .= 'Motiveringar (0)';
					}
				} else {
					$content .= '&nbsp;';
				}
			$content .= '</td>';
			$content .= '</tr>';
			$content .= '</table>';
			$content .= '</td></tr></table>';
			$content .= '<br>';
		}
		$content .= '<input type="submit" class="button" value="Uppdatera">';
		$content .= '</form>';
	} else {
		$content .= 'K&ouml;n &auml;r tom.<br>';
	}

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Modereringsk&ouml;', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>