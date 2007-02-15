<? include('include_all.php');

	if (isset($_GET['id'])) {
		$itemId = $_GET['id'];
	} else {
		$itemId = 0;
	}

	//Räkna läsning av itemId
	if (!isset($_COOKIE['forum_'.$itemId])) {
		updateForumReadCounter($db, $itemId);
		setcookie('forum_'.$itemId, 1, time()+((3600*24)*30)); //forget after 30 days
	}

	/* Starta/avsluta bevakning */
	if (isset($_SESSION['userId'])) {
		if (isset($_GET['subscribe'])) {
			/* Starta bevakning här */
			addSubscription($db, $_GET['subscribe'], SUBSCRIBE_MAIL);
		} else if (isset($_GET['unsubscribe'])) {
			/* Avsluta bevakning här */
			removeSubscription($db, $_GET['unsubscribe'], SUBSCRIBE_MAIL);
		}
	}

	setUserStatus($db, 'L&auml;ser forumet');

	if ($config['forum']['allow_votes'] && !empty($_POST['vote']) && !empty($_POST['voteId'])) {
		addForumVote($db, $_POST['voteId'], $_POST['vote']);
	}

	if (!isset($_SESSION['prevLoginTime']) || !$_SESSION['prevLoginTime']) {
		$_SESSION['prevLoginTime'] = time();
	}

	include('design_head.php');

	$item = getForumItem($db, $itemId);

	include('design_forum_head.php');

		if (!$item) {
			//display root level
			$title = getForumFolderDepthHTML($db, $itemId);
			$body = displayRootForumContent($db);
		} else {
			if (forumItemIsFolder($db, $itemId)) {
				//display content of a folder (parent = root)
				$title = getForumFolderDepthHTML($db, $itemId);
				$body = displayForumContentFlat($db, $itemId);
			} else {
				//display flat discussion overview
				$title = getForumItemDepthHTML($db, $item['itemId']);
				$body = displayDiscussionContentFlat($db, $itemId);
			}
		}

		echo '<div id="user_forum_content">';
		echo MakeBox($title, $body, 500);
		echo '</div>';

	include('design_forum_foot.php');
	include('design_foot.php');
?>