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

	//Om denna track site har minst en track point, redirecta direkt till den äldsta
	if (!isset($_GET['show'])) {
		$oldest_track_id = getOldestTrackPointID($db, $siteId);
		if ($oldest_track_id) {
			header('Location: admin_show_trackpoint.php?id='.$oldest_track_id);
			die;
		}
	}

	include('design_head.php');

	echo '<h2>Track site overview</h2>';

	echo 'Overview of track site <b>'.$site['siteName'].'</b>:<br>';
	echo 'Created by '.getUserName($db, $site['creatorId']).' at '.$site['timeCreated'].'<br>';
	if ($site['timeEdited']) echo '<i>Last edited by '.getUserName($db, $site['editorId']).' at '.$site['timeEdited'].'</i><br>';

	echo '<br>';
	echo MakeTrackerBox('Notes', $site['siteNotes']).'<br>';	
	
	echo '<a href="https://adwords.google.com/select/KeywordToolExternal" target="_blank">Google AdWords: Keyword Tool</a><br>';
	echo '<a href="http://searchmarketing.yahoo.com/rc/srch/" target="_blank">Yahoo! Search Marketing</a><br>';
	echo '<a href="http://inventory.overture.com/d/searchinventory/suggestion" target="_blank">Yahoo! (Overture) Keyword Selector Tool</a><br>';
	echo '<a href="http://www.digitalpoint.com/tools/suggestion/" target="_blank">Keyword Suggestion Tool</a><br>';

	include('design_foot.php');
?>