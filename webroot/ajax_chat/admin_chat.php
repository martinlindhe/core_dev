<?
	include('include_all.php');
	
	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');

	echo '<h2>Chat administration</h2><br>';
	echo 'From here you can create, modify, delete and lock chat rooms.<br><br>';
	
	echo 'Current chat rooms:<br>';
	$list = getChatRooms($db);
	for ($i=0; $i<count($list); $i++)
	{
		echo '<a href="admin_editchat.php?id='.$list[$i]['roomId'].'">'.$list[$i]['roomName'].'</a>, created '.getDateStringShort($list[$i]['timeCreated']).' by '.getUserName($db, $list[$i]['createdBy']).'<br>';
	}
	echo '<br>';
	
	echo '<a href="admin_newchat.php">New chat room</a>';

	include('design_foot.php');
?>