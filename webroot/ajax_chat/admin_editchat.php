<?
	require_once('config.php');

	if (!$session->isAdmin) die;

	if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die;
	$roomId = $_GET['id'];
	
	if (isset($_GET['delete'])) {
		require('design_head.php');
		
		if (isset($_GET['confirmed'])) {
			deleteChatRoom($roomId);
			echo 'Chat room & chat buffer has been deleted!';

		} else {
			echo 'Are you sure you want to delete this chat room? All of the chat buffer will also be deleted.<br><br>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$roomId.'&delete&confirmed">Yes</a><br><br>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$roomId.'">No</a>';
		}

		require('design_foot.php');
		die;
	}

	$emptied = false;
	if (isset($_GET['empty'])) {
		emptyChatRoomBuffer($roomId);
		$emptied = true;
	}

	if (!empty($_POST['roomname'])) {
		setChatRoomName($roomId, $_POST['roomname']);
	}

	$room = getChatRoom($roomId);
	if (!$room) die;

	require('design_head.php');

	echo '<h2>Chat administration - edit chat room</h2><br>';
	echo 'Here you can rename, lock and delete a chat room.<br>';
	echo 'Chat room was created by '.$room['createdBy'].' at '.$room['timeCreated'].'<br><br>';
	
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$roomId.'">';
	echo 'New name: <input type="text" name="roomname" maxlength=20 value="'.$room['roomName'].'"><br><br>';
	echo '<input type="submit" class="button" value="Rename room">';
	echo '</form>';
	echo '<br>';
	
	echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$roomId.'&delete">Delete chat room</a><br><br>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$roomId.'&empty">Empty chat room buffer</a><br><br>';
	
	require('design_foot.php');
	
	if ($emptied) JS_Alert('Chat room buffer has been emptied!');
?>