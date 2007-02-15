<?
	include('include_all.php');

	if (!$_SESSION['loggedIn'] || !$_SESSION['isAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$itemId = $_GET['id'];
	$item = getForumItem($db, $itemId);

	if (!$item) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_POST['subject']) && isset($_POST['body'])) {
		forumUpdateItem($db, $itemId, $_POST['subject'], $_POST['body']);
		header('Location: forum.php?id='.$itemId);
		die;
	}

	if (isset($_GET['confirmed'])) {
		//Radera meddelande/folder och allt under den

		/*
		if (
			(forumItemIsMessage($db, $itemId)    && userAccess($db, 'forum_global_can_delete_all_messages')) ||
			(forumItemIsDiscussion($db, $itemId) && userAccess($db, 'forum_global_can_delete_all_discussions')) ||
			(forumItemIsFolder($db, $itemId)     && userAccess($db, 'forum_global_can_delete_all_folders'))
			) {
		*/
		if ($_SESSION['isAdmin']) {
			deleteForumItemRecursive($db, $itemId);
			header('Location: forum.php?id='.$item['parentId']);
			die;
		}
	}

	include('design_head.php');
	include('design_forum_head.php');

	$content = showForumPost($db, $item, '', false);
	$content .= $config['forum']['text']['delete_confirm'].'<br><br>';
	$content .= '<table width="100%"><tr>';
	$content .= '<td width="50%" align="center"><a href="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'&confirmed">'.$config['text']['prompt_yes'].'</a></td>';
	$content .= '<td align="center"><a href="javascript:history.go(-1);">'.$config['text']['prompt_no'].'</a></td>';
	$content .= '</tr></table>';

		echo '<div id="user_forum_content">';
		$title = getForumFolderDepthHTML($db, $itemId);
		echo MakeBox($title.'|'.$config['text']['link_remove'], $content, 500);
		echo '</div>';

	include('design_forum_foot.php');
	include('design_foot.php');

?>