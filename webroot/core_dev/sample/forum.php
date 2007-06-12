<?
	require_once('config.php');

	if (isset($_GET['id'])) {
		$itemId = $_GET['id'];
	} else {
		$itemId = 0;
	}

	/* Starta/avsluta bevakning */
/*
	if (isset($_SESSION['userId'])) {
		if (isset($_GET['subscribe'])) {
			addSubscription($_GET['subscribe'], SUBSCRIBE_MAIL);
		} else if (isset($_GET['unsubscribe'])) {
			removeSubscription($_GET['unsubscribe'], SUBSCRIBE_MAIL);
		}
	}
*/
	/*
	if ($config['forum']['allow_votes'] && !empty($_POST['vote']) && !empty($_POST['voteId'])) {
		addForumVote($_POST['voteId'], $_POST['vote']);
	}*/

	require('design_head.php');

	$item = getForumItem($itemId);

	if (!$item) {
		//display root level
		$title = getForumFolderDepthHTML($itemId);
		$body = displayRootForumContent();
	} else {
		if (forumItemIsFolder($itemId)) {
			//display content of a folder (parent = root)
			$title = getForumFolderDepthHTML($itemId);
			$body = displayForumContentFlat($itemId);
		} else {
			//display flat discussion overview
			$title = getForumItemDepthHTML($item['itemId']);
			$body = displayDiscussionContentFlat($itemId);
		}
	}

	echo $title. $body;

	require('design_foot.php');
?>