<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
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

	echo '<h2>Location details</h2>';
	echo 'Location details for track point <b>'.$trackPoint['siteName'].' - '.$trackPoint['location'].'</b> (#'.$trackPoint['trackerId'].')<br>';
	echo '<br>';

	if ($time_from && $time_to) {
		echo '<b>Showing data collected between '.getFullDate($time_from).' and '.getFullDate($time_to).'</b><br><br>';
	} else {
		echo '<b>Showing ALL collected data for this track point\'s lifetime.</b><br><br>';
	}

	$list = getTrackerEntriesByLocation($db, $trackId, $time_from, $time_to);

	echo 'Showing <b>'.count($list).'</b> unique entries:<br><br>';

	echo '<table cellpadding=0 cellspacing=0 border=1>';
	echo '<tr>';
	echo '<th>Location</th>';
	echo '<th width=80>Frequency</th>';
	echo '</tr>';
	
	$location_total_count = 0;

	for ($i=0; $i<count($list); $i++) {
		$location_total_count += $list[$i]['cnt'];
		
		echo '<tr>';
		echo '<td>'.create_tooltip($list[$i]['location'], 80).'</td>';
		echo '<td align="right">'.$list[$i]['cnt'].'</td>';
		echo '</tr>';
	}
	echo '<tr><td colspan=2 align="right"><b>Total: '.$location_total_count.'</b></td></tr>';
	echo '</table>';

	include('design_foot.php');
?>