<?
	echo '<center>';
	echo '<div id="forum_holder">';	//start user page holder

		if (!isset($itemId)) $itemId = 0;
		if (!isset($item)) $item = '';

			$c2 = '';

			if ($_SESSION['loggedIn']) {
				$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="user.php">Min profil</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum.php">Forum</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum_search.php">S&oslash;k i forum</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum_latest.php">Siste innlegg</a><br>';

			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="blogs.php">Blogger</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="photoalbums.php">Fotoalbum</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="user_relations.php">Mine venner</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="user_guestbook.php">Gjestebok</a><br>';

			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="users_online.php">Brukere online</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="users_search.php">S&oslash;k etter bruker</a><br>';

			/*
			if ($_SESSION['loggedIn'] && $itemId != 0) {
				//Start/stop forum subscriptions
				if (!isSubscribed($db, $itemId, SUBSCRIBE_MAIL)) {
					$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum.php?id='.$itemId.'&subscribe='.$itemId.'">Overv&aring;ke</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				} else if (isSubscribedHere($db, $itemId, SUBSCRIBE_MAIL)) {
					$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum.php?id='.$itemId.'&unsubscribe='.$itemId.'">Sluta overv&aring;ke</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				}
			}*/

			if ($_SESSION['isAdmin'] && $itemId != 0 && !$item['locked']) {
				$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum_edit.php?id='.$itemId.'">'.$config['text']['link_edit'].'</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				$c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum_delete.php?id='.$itemId.'">'.$config['text']['link_remove'].'</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			}

			if (!$item) {
				//display root level
				if ($_SESSION['isAdmin']) $c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum_new.php?id='.$itemId.'">Ny kategori</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			} else {
				if (forumItemIsFolder($db, $itemId)) {
					//display content of a folder (parent = root)

					if ($item['parentId'] == 0) {
						if ($_SESSION['isAdmin']) $c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum_new.php?id='.$itemId.'">Nytt forum</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					} else {
						if ($_SESSION['loggedIn']) $c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum_new.php?id='.$itemId.'">Ny tr&aring;d</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					}
				} else if (!$item['locked']) {
					if ($_SESSION['loggedIn']) $c2 .= '<img src="icons/link_arrow.png" width=11 height=11> <a href="forum_new.php?id='.$itemId.'">Nytt innlegg</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				}
			}

		echo '<table width="100%" cellpadding=0 cellspacing=0 border=0>';
		echo '<tr><td align="center"><a href="forum.php"><img src="esp_design/soip-logo.gif" width=496 height=30></a></td></tr>';
		echo '<tr><td align="center">'.$c2.'</td></tr>';
		echo '</table><br>';

?>