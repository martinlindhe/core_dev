<?
	require_once('config.php');

	$session->requireLoggedIn();

	$userId = $session->id;
	$username = $session->username;
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$userId = $_GET['id'];
		$username = getUserName($userId);
	}

	require('design_head.php');

	createMenu($profile_menu, 'blog_menu');

	echo 'messages!!<br/><br/>';
	
	if ($userId != $session->id) {

		if (!empty($_POST['msg'])) {
			sendMessage($userId, $_POST['msg']);
			echo 'The message has been sent!';
			require('design_foot.php');
			die;
		}

		echo 'Send a private message to '.$username.'<br/>';
		echo '<form method="post" action="">';
		echo '<textarea name="msg" rows="8" cols="40"></textarea><br/>';
		echo '<input type="submit" class="button" value="Send"/>';
		echo '</form>';
		
	} else {
		echo 'my messages - INBOX<br/>';
		$list = getMessages(MESSAGE_GROUP_INBOX);
		d($list);

		echo '<hr/>';
		echo 'my messages - OUTBOX<br/>';
		$list = getMessages(MESSAGE_GROUP_OUTBOX);
		d($list);
	}

	require('design_foot.php');
?>