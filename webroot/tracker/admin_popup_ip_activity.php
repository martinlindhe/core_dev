<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['ip']) || !is_numeric($_GET['ip'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$geoip = $_GET['ip'];
	$ipv4 = GeoIP_to_IPv4($geoip);
	
	include('design_popup_head.php');

	echo '<h1>Activity details for '.getDNSCacheHostname($geoip).'</h1>';
	echo 'IP address is <b>'.$ipv4.'</b> '.getGeoIPCountryFlag($geoip).' (DNS cache is '.makeTimePeriodShort(getDNSCacheAge($geoip)).' old).<br>';
	echo '<br>';

	$list = getTrackerEntriesByIP($db, $geoip);
	
	echo '<table cellpadding=0 cellspacing=0 border=1>';
	echo '<tr>';

	echo '<th>Track point</th>';
	echo '<th>Browser &amp; Time</th>';
	echo '<th>Location &amp; Referrer</th>';

	echo '</tr>';

	for ($i=0; $i<count($list); $i++) {
		echo '<tr>';
		echo '<td>'.$list[$i]['trackerId'].'</td>';
		echo '<td>'.FormatBrowserInfo($db, $list[$i]['userAgent']).' '.$list[$i]['timeCreated'].'</td>';

		echo '<td><b>Loc:</b> '.create_tooltip($list[$i]['location'], 60).'<br>';
		$ref = create_tooltip($list[$i]['referrer'], 60);
		if ($ref) echo '<b>Ref:</b> '.$ref;
		echo '</td>';

		echo '</tr>';
	}
	echo '<tr><td colspan=3 align="right"><b>Total: '.count($list).'</b></td></tr>';
	echo '</table>';
	echo '<br>';

	include('design_popup_foot.php');
?>