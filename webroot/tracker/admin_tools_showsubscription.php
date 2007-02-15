<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$subId = $_GET['id'];

	include('design_head.php');

	echo '<h2>Track site tools - Show subscription</h2>';


	echo '<b>Settings:</b><br>';
	$data = getSubscriptionSettings($db, SUBSCRIBE_TRACKSITE, $subId);
	for ($i=0; $i<count($data); $i++) {
		echo $data[$i]['settingName'].': ';
		echo $data[$i]['settingValue'].'<br>';
	}
	echo '<br>';

	$freq = getSetting($db, SETTING_SUBSCRIPTION, $subId, 'interval');

	
	echo '<b>Subscription history - '.$freq.'</b><br>';
	$list = getSubscriptionHistory($db, SUBSCRIBE_TRACKSITE, $subId);
	for ($i=0; $i<count($list); $i++) {
		echo $list[$i]['timeCreated'].'<br>';
		echo '<div>';
		echo 'Time period: '.$list[$i]['periodStart'].' - '.$list[$i]['periodEnd'].'<br>';
		echo 'Recipients: '.$list[$i]['recipients'].'<br>';
		echo 'Body: '.$list[$i]['message'];
		echo '</div>';
	}
	echo '<br>';

	echo '<b>Current subscribers:</b><br>';
	$list = getSubscribers($db, SUBSCRIBE_MAIL, $subId);
	for ($i=0; $i<count($list); $i++) {
		echo $list[$i]['recipient'].'<br>';
	}
	echo '<br>';	

	echo '<a href="admin_tools_editsubscribers.php?id='.$subId.'">Manage subscribers</a><br>';
	echo '<br>';
	echo '<a href="admin_tools_deletesubscription.php?id='.$subId.'">Delete subscription</a>';

	include('design_foot.php');
?>