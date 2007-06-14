<?
	require_once('config.php');
	
	$session->requireAdmin();

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$itemId = $_GET['id'];
	$item = getForumItem($itemId);

	if (!$item) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_POST['subject']) && isset($_POST['body'])) {
		forumUpdateItem($itemId, $_POST['subject'], $_POST['body']);
		header('Location: forum.php?id='.$itemId);
		die;
	}

	if (isset($_GET['confirmed'])) {
		//Radera meddelande/folder och allt under den

		if ($session->isAdmin) {
			deleteForumItemRecursive($itemId);
			header('Location: forum.php?id='.$item['parentId']);
			die;
		}
	}

	require('design_head.php');

	echo showForumPost($item, '', false);
	echo 'Are you sure you want to delete this forum post?<br><br>';
	echo '<table width="100%"><tr>';
	echo '<td width="50%" align="center"><a href="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'&confirmed">Yes</a></td>';
	echo '<td align="center"><a href="javascript:history.go(-1);">No</a></td>';
	echo '</tr></table>';

	$title = getForumFolderDepthHTML($itemId);
	echo $title. $content;

	require('design_foot.php');

?>