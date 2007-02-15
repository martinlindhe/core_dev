<?
	//takes an ip address as parameter
	if (!empty($_GET['ip'])) $ip = $_GET['ip'];
	else $ip = $_SERVER['REMOTE_ADDR'];

	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

	$content = '';

	if ($ip) {	
		$geoip = IPv4_to_GeoIP($ip);

		$content .= '<h1>'.$ip.' ('.gethostbyaddr($ip).')';
		if ($ip == $_SERVER['REMOTE_ADDR']) $content .= ' - YOUR IP';
		$content .= '</h1>';
		$content .= 'GeoIP format: '.$geoip.'<br>';
		$content .= 'IP belongs to this country: '.getGeoIPCountry($db, $geoip).' '.getGeoIPCountryFlag($db, $geoip).'<br>';
		$content .= 'IP belongs to this city: '.getGeoIPCityName($db, $geoip).'<br>';
		$content .= '<br><br>';
		
		$cnt = getLogEntriesCountByGeoIP($db, $geoip);
		$content .= '<h2><a href="admin_events.php?ip='.$geoip.'">IP was found in '.$cnt.' event log entries</a></h2><br>';

		$guilty_names = getUsernamesFromGeoIP($db, $geoip);
		if ($guilty_names) {
			$content .= 'Usernames from this IP: '.$guilty_names.'<br><br>';
		}


		$content .= '<a href="http://www.dnsstuff.com/tools/whois.ch?ip='.$ip.'" target="_blank">whois</a><br>';
		$content .= '<a href="http://www.dnsstuff.com/tools/tracert.ch?ip='.$ip.'" target="_blank">traceroute</a><br>';
		$content .= '<a href="http://visualroute.visualware.com/" target="_blank">visual route (java traceroute)</a><br>';
		$content .= '<a href="http://www.dnsstuff.com/tools/ping.ch?ip='.$ip.'" target="_blank">ping</a><br>';
		$content .= '<br>';

		$content .= '<a href="http://www.dnsstuff.com/tools/city.ch?ip='.$ip.'" target="_blank">City from IP lookup</a><br>';
		$content .= '<a href="http://www.senderbase.org/search?searchString='.$ip.'" target="_blank">senderbase blacklist lookup</a><br>';
		$content .= '<a href="http://openrbl.org/lookup?i='.$ip.'" target="_blank">open RLB ip lookup</a><br>';
		$content .= '<br><br>';

		$content .= '<a href="admin_geoip_cities.php">admin geoip cities</a><br>';
		$content .= '<br><br>';
	}

	$content .= 'Your IP is '.$_SERVER['REMOTE_ADDR'].' (<a href="http://www.showmyip.com/" target="_blank">showmyip.com</a>)<br><br>';
	$content .= '<form method="get" action="'.$_SERVER['PHP_SELF'].'">';
	$content .= '<b>Enter a IP number to query:</b><br>';
	$content .= '<input type="text" name="ip" value="'.$ip.'"><br><br>';
	$content .= '<input type="submit" class="button" value="Query IP">';
	$content .= '</form>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Query IP information', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>