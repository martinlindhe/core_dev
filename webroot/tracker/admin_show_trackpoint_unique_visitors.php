<?
	include('include_all.php');

	if (!$_SESSION['isAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$trackId = $_GET['id'];
	$trackPoint = getTrackPoint($db, $trackId);

	if (!$trackPoint) {
		header('Location: '.$config['start_page']);
		die;
	}

	$siteId = $trackPoint['siteId'];

	//Will return with $time_from, $time_to set
	include('timeperiod_selector.php');

	include('design_head.php');

	echo '<h2>Unique visitors</h2>';
	echo 'Unique visitors logged for track point <b>'.$trackPoint['siteName'].' - '.$trackPoint['location'].'</b> (#'.$trackPoint['trackerId'].')<br>';
	echo '<br>';

	if ($time_from && $time_to) {
		echo '<b>Showing data collected between '.getFullDate($time_from).' and '.getFullDate($time_to).'</b><br><br>';
	} else {
		echo '<b>Showing ALL collected data for this track point\'s lifetime.</b><br><br>';
	}

	$list = getUniqueIPFromTrackerEntries($db, $trackId, $time_from, $time_to);
	
	$total_count = count($list);

	echo 'Showing <b>'.$total_count.'</b> unique IP\'s for track point #'.$trackId.':<br><br>';

	echo '<table cellpadding=0 cellspacing=0 border=1>';
	echo '<tr>';
	echo '<th>Hostname</th>';
	echo '<th>IP Owner</th>';
	echo '<th>Private</th>';
	echo '<th>Frequency</th>';
	echo '</tr>';

	$total_hits = 0;

	for ($i=0; $i<count($list); $i++) {
		$total_hits += $list[$i]['cnt'];

		echo '<tr>';		
		$ipv4 = GeoIP_to_IPv4($list[$i]['IP']);
		echo '<td>'.getGeoIPCountryFlag($list[$i]['IP']).' <a href="#" onClick="return anon_popup(\'admin_popup_ip_details.php?ip='.$list[$i]['IP'].'\')">'.getDNSCacheHostname($list[$i]['IP']).'</a></td>';

		$whois = getWhoisData($list[$i]['IP']);

		$name = $whois['name'];
		if (!$name) $name = '<span class="objectCritical">No name</span>';
		
		echo '<td><a href="#" onClick="return anon_popup(\'admin_popup_ip_owner.php?start='.$whois['geoIP_start'].'&amp;end='.$whois['geoIP_end'].'\')">'.$name.'</a></td>';
		echo '<td>';
			if ($whois['privateRange']) echo 'YES';
			else echo '&nbsp;';
		echo '</td>';
		echo '<td align="right">'.$list[$i]['cnt'].'</td>';
		echo '</tr>';
	}
	echo '<tr><td colspan=4 align="right"><b>Unique: '.count($list).', Total: '.$total_hits.'</b></td></tr>';
	echo '</table>';

	include('design_foot.php');
?>