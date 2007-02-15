<?
	include_once('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	if (!empty($_POST['sitename'])) {
		$siteId = createTrackSite($db, $_POST['sitename'], $_POST['sitenotes']);
		if ($siteId) {
			header('Location: admin_show_tracksite.php?id='.$siteId);
			die;
		}
	}

	include('design_head.php');

	echo '<h2>Create a new track site</h2>';
	
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	echo 'Track site name:<br>';
	echo '<input type="text" name="sitename" size=80><br><br>';
	echo 'Notes:<br>';
	echo '<textarea name="sitenotes" cols=77 rows=10></textarea><br><br>';
	echo '<input type="submit" class="button" value="Create">';
	echo '</form>';

	include('design_foot.php');
?>