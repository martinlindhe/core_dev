<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	
	echo '<h2>Administrative tasks</h2>';

	echo '<a href="admin_create_user.php">Create a user account</a><br><br>';

	echo '<a href="admin_ip_ranges_overview.php">View IP ranges</a><br><br>';

	echo '<a href="admin_tools_webtrends.php">Create web trends graph</a><br><br>';
	
	echo '<a href="admin_trackerstats.php">Tracker statistics</a><br><br>';
	
	echo '<a href="admin_db.php">Admin db</a><br><br>';
	
	echo '<a href="admin_events.php">View event log</a><br><br>';

	echo '<a href="admin_geoip.php">Regenerate GeoIP lookup table</a><br><br>';

	include('design_foot.php');
?>