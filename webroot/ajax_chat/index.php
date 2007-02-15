<?
	include_once('include_all.php');

	include('design_head.php');
	include('inc/noscript.html');

	$list = getChatRooms($db);
	if (count($list)) {
		echo 'Select chat room to join:<br>';
		for ($i=0; $i<count($list); $i++)
		{
			echo '<a href="chat.php?id='.$list[$i]['roomId'].'">'.$list[$i]['roomName'].'</a>, created '.getDateStringShort($list[$i]['timeCreated']).' by '.getUserName($db, $list[$i]['createdBy']).'<br>';
		}
	} else {
		echo 'There are no chat rooms to join!<br>';
	}

	include('design_foot.php');
?>