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
	
	if (isset($_GET['confirmed'])) {
		clearTrackPoint($db, $trackId);
		header('Location: admin_show_tracksite.php?id='.$trackPoint['siteId']);
		die;
	}

	include('design_head.php');

	echo '<h2>Clear track point data</h2>';
	echo 'Really clear all track point entries from track point <b>'.$trackPoint['siteName'].' - '.$trackPoint['location'].'</b> (#'.$trackPoint['trackerId'].')?<br>';
	echo '<br>';

	echo '<b>'.getTrackerEntriesCnt($db, $trackId).'</b> collected track entries which will be deleted!<br><br>';

	echo 'Are you sure you want to clear this track point?<br><br>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?confirmed">Yes</a><br><br>';
	echo '<a href="admin_show_trackpoint.php?id='.$trackId.'">No</a>';

	include('design_foot.php');
?>