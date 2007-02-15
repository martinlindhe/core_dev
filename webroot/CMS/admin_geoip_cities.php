<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	$ip1 = '';
	$ip2 = '';
	$city = '';
	$country = 0;
	if (!empty($_POST['ip1'])) $ip1 = $_POST['ip1'];
	if (!empty($_POST['ip2'])) $ip2 = $_POST['ip2'];
	if (!empty($_POST['city'])) $city = $_POST['city'];
	if (!empty($_POST['country'])) $country = $_POST['country'];

	$geoip1 = IPv4_to_GeoIP($ip1);
	$geoip2 = IPv4_to_GeoIP($ip2);

	include('design_head.php');
	include('design_user_head.php');
	
		$content = '';
	
		if ($geoip1 && $geoip1 >= $geoip2) {
			$content .= 'Error! IP start is higher than or equal to IP end';
		} else if ($geoip1 && $geoip2 && $city) {
			addGeoIPCityRange($db, $geoip1, $geoip2, $city, $country);
			JS_Alert('Location saved!');
		}
		
		$content .= getInfoField($db, 'page_admin_geoip_cities');
		
		$list = getGeoIPCities($db);
		for ($i=0; $i<count($list); $i++) {
			$content .= GeoIP_to_IPv4($list[$i]['start']).' - ';
			$content .= GeoIP_to_IPv4($list[$i]['end']).' : ';
			$content .= utf8_encode($list[$i]['cityName']).'<br>';
		}
		$content .= '<br>';
		
		$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
		$content .= 'inetnum: <input type="text" name="ip1" value="'.$ip1.'"> - ';
		$content .= '<input type="text" name="ip2" value="'.$ip2.'"><br>';
		$content .= 'Place of location:<br>';
		$content .= '<input type="text" name="city" value="'.$city.'" size=40><br><br>';
		
		$content .= '<select name="country">';
		$content .= '<option> Choose a country (optional)';
		foreach ($GEOIP_COUNTRY_NAMES as $country_id => $country_name) {
			$content .= '<option value="'.$country_id.'">'.$country_name."<br>";
		}
		$content .= '</select>';
		$content .= '<br><br>';
	
		$content .= '<input type="submit" class="button" value="Add location name">';
		$content .= '</form>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|GeoIP cities', $content);
		echo '</div>';

	include('design_admin_foot.php');	
	include('design_foot.php');
?>