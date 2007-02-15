<?
	include('include_all.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (empty($_GET['start']) || !is_numeric($_GET['start']) || empty($_GET['end']) || !is_numeric($_GET['end'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$geoip_start = $_GET['start'];
	$geoip_end = $_GET['end'];
	
	$whois = getExactWHOISData($geoip_start, $geoip_end);
	if (!$whois) {
		header('Location: '.$config['start_page']);
		die;
	}

	$list = getUniqueIPFromRange($db, $geoip_start, $geoip_end);

	$geoip_range = $geoip_end - $geoip_start + 1;
	$geoip_range_pct = round((count($list) / $geoip_range) * 100, 2);
	
	include('design_popup_head.php');

	echo '<h2>IP address range owner details</h2>';
	echo 'Address range: <b>'.GeoIP_to_IPv4($geoip_start).' - '.GeoIP_to_IPv4($geoip_end).'</b><br>';

	echo 'Address space: <b>'.$geoip_range.'</b> IP\'s, ';
	echo '<b>'.count($list).'</b> tracked ('.$geoip_range_pct.'%)<br>';
	echo '<br>';

	echo '<table cellpadding=0 cellspacing=0 border=1>';
	echo '<tr>';
	echo '<th>Hostname</th>';
	echo '<th>IP</th>';
	echo '<th>Frequency</th>';
	echo '</tr>';

	$tot = 0;
	for ($i=0; $i<count($list); $i++) {
		echo '<tr>';
		echo '<td>'.getGeoIPCountryFlag($list[$i]['IP']).' <a href="admin_popup_ip_details.php?ip='.$list[$i]['IP'].'">'.getDNSCacheHostname($list[$i]['IP']).'</a></td>';
		echo '<td>'.GeoIP_to_IPv4($list[$i]['IP']).'</td>';
		echo '<td align="right">'.$list[$i]['cnt'].'</td>';
		echo '</tr>';
		$tot += $list[$i]['cnt'];
	}
	echo '<tr><td colspan=3 align="right"><b>Unique: '.count($list).', Total: '.$tot.'</b></td></tr>';
	echo '</table>';
	
	include('design_popup_foot.php');
?>