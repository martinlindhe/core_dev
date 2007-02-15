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

	if (isset($_GET['confirmed'])) {
		deleteTrackPoint($db, $trackId);
		header('Location: admin_show_tracksite.php?id='.$trackPoint['siteId']);
		die;
	}

	include('design_head.php');

	echo '<h2>Delete track point</h2>';
	echo 'Delete track point <b>'.$trackPoint['siteName'].' - '.$trackPoint['location'].'</b> (#'.$trackPoint['trackerId'].')?<br>';
	echo '<br>';

	echo 'This track point has <b>'.getTrackerEntriesCnt($db, $trackId).'</b> collected track entries which will also be deleted!<br><br>';

	echo 'Are you sure you want to delete this track point?<br><br>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$trackId.'&amp;confirmed">Yes</a><br><br>';
	echo '<a href="admin_show_trackpoint.php?id='.$trackId.'">No</a>';

	include('design_foot.php');
?>