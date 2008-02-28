<?
	require_once('config.php');
	$session->requireAdmin();

	$itemId = $_GET['id'];
	$item = getForumItem($itemId);
	if ($item) {
		if (isset($_GET['unlock'])) {
			forumUnlockItem($itemId);
		} else {
			forumLockItem($itemId);
		}
	}

	//header('Location: forum.php?id='.$item['parentId']);
	header('Location: forum.php?id='.$itemId);
?>