<?
	include_once('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$siteId = $_GET['id'];
	$site = getTrackSite($db, $siteId);
	
	if (!$site) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (!empty($_POST['location'])) {
		$trackId = createTrackPoint($db, $siteId, $_POST['location'], $_POST['notes']);
		header('Location: admin_show_trackpoint.php?id='.$trackId);
		die;
	}
	
	include('design_head.php');

	echo '<h2>New track point</h2>';
	echo 'Add a new track point to track site "'.$site['siteName'].'"<br><br>';
	
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$siteId.'">';
	echo 'Track point location:<br>';
	echo '<input type="text" name="location" size=60><br><br>';
	echo 'Notes:<br>';
	echo '<textarea name="notes" cols=77 rows=10></textarea><br><br>';
	echo '<input type="submit" class="button" value="Create">';
	echo '</form>';

	//echo '<br>Back to <a href="admin_show_tracksite.php?id='.$siteId.'">track site overview</a>';
	
	include('design_foot.php');
?>