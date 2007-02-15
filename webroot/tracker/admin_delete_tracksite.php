<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$siteId = $_GET['id'];

	$site = getTrackSite($db, $siteId);

	//dont allow deletion of non-empty track sites
	if (!$site || getTrackPointsCount($db, $siteId)) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (isset($_GET['confirmed'])) {
		deleteTrackSite($db, $siteId);
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	
	echo '<h2>Delete track site</h2>';
	echo 'Delete track site <b>'.$site['siteName'].'</b>?<br><br>';

	echo 'Are you sure you want to delete this track site?<br><br>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$siteId.'&confirmed">Yes</a><br><br>';
	echo '<a href="admin_show_tracksite.php?id='.$siteId.'">No</a>';

	include('design_foot.php');
?>