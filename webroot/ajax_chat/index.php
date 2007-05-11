<?
	require_once('config.php');

	require('design_head.php');

	$list = getChatRooms();
	if (count($list)) {
		echo 'Select chat room to join:<br/>';
		for ($i=0; $i<count($list); $i++)
		{
			echo '<a href="chat.php?id='.$list[$i]['roomId'].'">'.$list[$i]['roomName'].'</a>, created '.$list[$i]['timeCreated'].' by id '.$list[$i]['createdBy'].'<br/>';
		}
	} else {
		echo 'There are no chat rooms to join!<br/>';
	}

	require('design_foot.php');
?>