<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$siteId = $_GET['id'];

	$site = getTrackSite($db, $siteId);
	if (!$site) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');

	echo '<h2>Track site subscriptions</h2>';
	echo 'Site name: '.$site['siteName'].'<br>';
	echo '<br>';
	
	echo 'Active subscriptions:<br>';

	$list = getSubscribers($db, SUBSCRIBE_TRACKSITE, $siteId);
	if (!$list) {
		echo 'None<br><br>';
	}
	for ($i=0; $i<count($list); $i++) {
		
		$name = getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'name');
		
		echo '<a href="admin_tools_showsubscription.php?id='.$list[$i]['subscriptionId'].'">'.$name.'</a><br>';
	}
	
	echo '<br>';	
	echo '<a href="admin_tools_addsubscription.php?id='.$siteId.'">Create a new subscription</a><br>';
	
	include('design_foot.php');
?>