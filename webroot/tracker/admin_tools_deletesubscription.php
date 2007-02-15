<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$subId = $_GET['id'];
	
	$sub = getSubscription($db, SUBSCRIBE_TRACKSITE, $subId);

	if (!$sub) {
		die;
	}

	if (isset($_GET['confirmed'])) {
		//Radera bevakningen: ownerId=mätpunkten, subscriptionId=$subId
		removeSubscription($db, SUBSCRIBE_TRACKSITE, $sub['ownerId'], $subId);
		
		//Radera associerade mailaddresser: alla med ownerId=$subId och typ=mail
		removeAllSubscriptions($db, SUBSCRIBE_MAIL, $subId); 
		
		//Radera inställningar för bevakningen:
		removeAllSettings($db, SETTING_SUBSCRIPTION, $subId);

		header('Location: admin_subscribe_tracksite.php?id='.$sub['ownerId']);
		die;		
	}	

	include('design_head.php');

	echo '<h2>Track site tools - Delete subscription</h2>';

	echo 'Are you sure you want to delete this subscription?<br><br>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$subId.'&confirmed">Yes</a><br><br>';
	echo '<a href="admin_tools_showsubscription.php?id='.$subId.'">No</a>';

	include('design_foot.php');
?>