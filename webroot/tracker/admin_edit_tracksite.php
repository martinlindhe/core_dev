<?
	include('include_all.php');

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
	
	if (isset($_POST['note']) && isset($_POST['name'])) {
		setTrackSiteName($db, $siteId, $_POST['name']);
		setTrackSiteNote($db, $siteId, $_POST['note']);
		$site = getTrackSite($db, $siteId);
	}

	include('design_head.php');

	echo '<h2>Edit track site</h2>';
	echo 'Site name: '.$site['siteName'].'<br>';
	echo 'Created by '.getUserName($db, $site['creatorId']).' at '.$site['timeCreated'].'<br>';
	if ($site['timeEdited']) echo '<i>Last edited by '.getUserName($db, $site['editorId']).' at '.$site['timeEdited'].'</i><br>';

	echo '<br>';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$siteId.'">';
	echo 'Track site name:<br>';
	echo '<input type="text" name="name" value="'.$site['siteName'].'" size=80><br><br>';

	echo 'Notes:<br>';
	echo '<textarea name="note" cols=80 rows=10>'.$site['siteNotes'].'</textarea><br><br>';
	echo '<input type="submit" class="button" value="Save changes"><br>';
	echo '</form><br>';

	echo '<a href="admin_show_tracksite.php?id='.$siteId.'&amp;show">Back to overview</a><br><br>';

	if (getTrackPointsCount($db, $siteId)) {
		echo '<br><i>Note: To delete a track site, you first need to delete all it\'s track points</i><br>';
	} else {
		echo '<br><a href="admin_delete_tracksite.php?id='.$siteId.'">Delete this track site</a><br>';
	}

	include('design_foot.php');
?>