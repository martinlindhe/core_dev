<?
	include_once('include_all.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	$itemId = $_GET['id'];
	$item = getForumItem($db, $itemId);
	if ($item) {
		if (isset($_GET['unlock'])) {
			forumUnlockItem($db, $itemId);
		} else {
			forumLockItem($db, $itemId);
		}
	}

	//header('Location: forum.php?id='.$item['parentId']);
	header('Location: forum.php?id='.$itemId);
?>