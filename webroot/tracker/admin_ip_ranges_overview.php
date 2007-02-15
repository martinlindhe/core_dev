<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	
	echo '<h2>IP ranges overview</h2>';

	echo 'Select a country to display IP ranges associated with that country.<br>';
	echo 'Please note that the country to IP association is done by the GeoIP database and might not be 100% accurate.<br><br>';
	
	$list = getWHOISCacheCountryRanges();
	
	for ($i=0; $i<count($list); $i++) {
		echo showGeoIPCountryFlag($list[$i]['ci']).' <a href="admin_ip_ranges.php?ci='.$list[$i]['ci'].'">'.GeoIP_ci_to_Country($list[$i]['ci']).'</a><br>';
	}

	include('design_foot.php');
?>