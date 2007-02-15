<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id']) || empty($_GET['from']) || !is_numeric($_GET['from']) || empty($_GET['to']) || !is_numeric($_GET['to'])) {
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

	echo 'Data details for track point #'.$trackPoint['trackerId'].' - '.$trackPoint['location'].'<br>';
	echo 'Created by '.getUserName($db, $trackPoint['creatorId']).' at '.getDateStringShort($trackPoint['timeCreated']).'<br><br>';

	echo 'Notes: '.$trackPoint['trackerNotes'].'<br>';
	
	$list = getTrackerEntriesTimeperiod($db, $trackId, $time_from, $time_to, true, 'desc');
	
	$unique_count = getUniqueIPCountFromTrackerEntriesTimeperiod($db, $trackId, $time_from, $time_to, true);
	$average_entries = round(count($list) / $unique_count, 1);

	echo '<h3>Data collected between <b>'.getDateStringShort($time_from).'</b> and <b>'.getDateStringShort($time_to).'</b>.</h3>';
	echo 'Displaying <b>'.count($list).'</b> entries from <b>'.$unique_count.'</b> unique IP\'s ('.$average_entries.' entries per IP).<br><br>';

	echo '<table cellpadding=0 cellspacing=0 border=1>';
	echo '<tr>';
	echo '<th>Browser &amp; Time</th>';
	echo '<th>IP</th>';
	echo '<th>IP Owner</th>';
	echo '<th>Location &amp; Referrer</th>';
	echo '</tr>';

	for ($i=0; $i<count($list); $i++) {
		echo '<tr>';
		echo '<td>'.FormatBrowserInfo($db, $list[$i]['userAgent']).' '.getDateStringShort($list[$i]['timeCreated']).'</td>';
		echo '<td>'.getGeoIPCountryFlag($list[$i]['IP']).' <a href="#" onClick="return anon_popup(\'admin_popup_ip_details.php?ip='.$list[$i]['IP'].'\')">'.GeoIP_to_IPv4($list[$i]['IP']).'</a></td>';
		
		$whois = getWhoisData($list[$i]['IP']);
		echo '<td><a href="#" onClick="return anon_popup(\'admin_popup_ip_owner.php?start='.$whois['geoIP_start'].'&amp;end='.$whois['geoIP_end'].'\')">'.$whois['name'].'</a></td>';

		$list[$i]['location'] = url_safedecode($list[$i]['location']);
		$list[$i]['referrer'] = url_safedecode($list[$i]['referrer']);

		echo '<td><b>Loc:</b> '.create_tooltip($list[$i]['location'], 35).'<br>';
		$ref = create_tooltip($list[$i]['referrer'], 35);
		if ($ref) echo '<b>Ref:</b> '.$ref;
		echo '</td>';

		echo '</tr>';
	}
	echo '<tr><td colspan=4 align="right"><b>Total: '.count($list).'</b></td></tr>';
	echo '</table>';

	include('design_foot.php');
?>