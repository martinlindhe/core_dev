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
	
	echo createMenu($forum_menu, 'blog_menu');

	$item = getForumItem($itemId);

	if (!$item) {
		//display root level
		echo getForumFolderDepthHTML($itemId);
		echo displayRootForumContent();
	} else {
		if (forumItemIsFolder($itemId)) {
			//display content of a folder (parent = root)
			echo getForumFolderDepthHTML($itemId);
			echo displayForumContentFlat($itemId);
			
			echo '<a href="forum_new.php?id='.$itemId.'">New discussion</a>';
		} else {
			//display flat discussion overview
			echo getForumItemDepthHTML($item['itemId']);
			echo displayDiscussionContentFlat($itemId);
		}
	}

	require('design_foot.php');
?>