<?
	require_once('config.php');

	$session->requireAdmin();

	require('design_head.php');

	echo '<h2>Chat administration</h2><br>';
	echo 'From here you can create, modify, delete and lock chat rooms.<br><br>';

	echo 'Current chat rooms:<br>';
	$list = getChatRooms();
	for ($i=0; $i<count($list); $i++)
	{
		echo '<a href="admin_editchat.php?id='.$list[$i]['roomId'].'">'.$list[$i]['roomName'].'</a>, created '.$list[$i]['timeCreated'].' by id '.$list[$i]['createdBy'].'<br>';
	}
	echo '<br>';

	echo '<a href="admin_newchat.php">New chat room</a>';

	require('design_foot.php');
?>