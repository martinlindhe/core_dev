<?
	include('include_all.php');
	
	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	if (!empty($_POST['roomname'])) {
		$roomId = newChatRoom($db, $_POST['roomname']);
		if ($roomId) {
			header('Location: admin_editchat.php?id='.$roomId);
			die;
		}
	}

	include('design_head.php');

	echo '<h2>Chat administration - new chat room</h2><br>';
	echo 'Here you can create a new chat room.<br>';
	
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo '<input type="text" name="roomname" maxlength=20><br><br>';
	echo '<input type="submit" class="button" value="Create room">';
	echo '</form>';
	
	include('design_foot.php');
?>