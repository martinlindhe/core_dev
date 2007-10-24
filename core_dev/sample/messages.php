<?
	require_once('config.php');

	$session->requireLoggedIn();

	$userId = $session->id;
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$userId = $_GET['id'];
	}

	require('design_head.php');

	createMenu($profile_menu, 'blog_menu');

	if ($userId != $session->id) {

		if (isset($_POST['msg_subj']) && !empty($_POST['msg'])) {
			sendMessage($userId, $_POST['msg_subj'], $_POST['msg']);
			echo 'The message has been sent!';
			require('design_foot.php');
			die;
		}

		echo 'Send a private message to '.nameLink($userId).'<br/>';
		echo '<form method="post" action="">';
		echo 'Subject: <input type="text" name="msg_subj"/><br/>';
		echo '<textarea name="msg" rows="8" cols="40"></textarea><br/>';
		echo '<input type="submit" class="button" value="Send"/>';
		echo '</form>';
		
	} else {
		showMessages();
	}

	require('design_foot.php');
?>