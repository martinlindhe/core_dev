<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$subId = $_GET['id'];

	if (!empty($_GET['remove'])) {
		removeSubscription($db, SUBSCRIBE_MAIL, $subId, $_GET['remove']);
	}
	
	if (!empty($_POST['mail'])) {
		addSubscription($db, SUBSCRIBE_MAIL, $subId, $_POST['mail']);
	}

	include('design_head.php');

	echo '<h2>Track site tools - Add/edit subscribers</h2>';
	echo '<br>';
	
	echo 'List of subscribers:<br>';
	
	$list = getSubscribers($db, SUBSCRIBE_MAIL, $subId);
	for ($i=0; $i<count($list); $i++) {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$subId.'&amp;remove='.$list[$i]['subscriptionId'].'"><img src="design/delete.png" alt="Remove"></a> ';
		echo $list[$i]['recipient'].'<br>';
	}
	
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$subId.'">';
	echo '<input type="text" name="mail" size=40> ';
	echo '<input type="submit" class="button" value="Add">';
	echo '</form>';
	
	echo '<br>';
	echo '<a href="admin_tools_showsubscription.php?id='.$subId.'">Return to subscription overview</a>';

	include('design_foot.php');
?>