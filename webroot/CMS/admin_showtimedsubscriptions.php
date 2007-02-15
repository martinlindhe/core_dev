<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

	$content = '<b>Administer Timed Subscriptions - Show subscriptions</b><br><br>';
	
	$list = getTimedSubscriptions($db);
	//print_r($list);
	for ($i=0; $i<count($list); $i++) {
		$user = $list[$i]['userId'];
		if ($user) $user = getUserName($db, $user);
		else $user = 'Unregistered';
		
		$ip_v4 = GeoIP_to_IPv4($list[$i]['userIP']);
		$content .= 'Reminder type '.$list[$i]['remindType'].' created by '.$user.' from <a href="admin_ip.php?ip='.$ip_v4.'">'.$ip_v4.'</a> at '.getRelativeTimeLong($list[$i]['timeCreated']).':<br>';
		$content .= 'Remind '.$list[$i]['remindDest'].' of '.$list[$i]['remindMsg'].' starting from '.getRelativeTimeLong($list[$i]['timeStart']).'<br><br>';
		
	}

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Show timed subscriptions', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>