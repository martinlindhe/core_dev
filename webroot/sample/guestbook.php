<?
	require_once('config.php');

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$userId = $_GET['id'];
	} else if ($session->id) {
		$userId = $session->id;
	} else $session->requireLoggedIn();

	require('design_head.php');
	
	createMenu($profile_menu, 'blog_menu');

	if ($session->isAdmin || $session->id == $userId) {
		if (!empty($_GET['remove'])) {
			removeGuestbookEntry($db, $_GET['remove']);
		}
	}

	if ($session->id != $userId && !empty($_POST['body'])) {
		addGuestbookEntry($userId, '', $_POST['body']);
	}

	echo 'The guestbook contains '.getGuestbookSize($userId).' messages.<br/><br/>';

	$list = getGuestbook($userId);	//fixme: paging stöd
	for ($i=0; $i<count($list); $i++) {
		echo '<div class="guestbook_entry">';

		echo '<b>'.$list[$i]['timeCreated'].', from '.nameLink($list[$i]['authorId'], $list[$i]['authorName']). '</b><br/>';
		if ($session->id == $userId) {
			if ($list[$i]['entryRead'] == 0) {
				echo '<img src="/gfx/icon_mail.png" alt="Unread">';
			}
		}
		echo stripslashes($list[$i]['body']).'<br/>';

		if ($session->isAdmin || $session->id == $userId) {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$userId.'&remove='.$list[$i]['entryId'].'">Remove</a>';
		}
		echo '</div><br/>';
	}

	if ($session->id) {
		if ($session->id != $userId) {
			echo 'New entry:<br/>';
			echo '<form name="addGuestbook" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$userId.'">';
			echo '<textarea name="body" cols="40" rows="6"></textarea><br/><br/>';
			echo '<input type="submit" class="button" value="Save"/>';
			echo '</form>';
		} else {
			/* Mark all entries as read */
			markGuestbookRead();
		}
	}

	require('design_foot.php');
?>