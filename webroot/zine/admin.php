<?
	include_once('include_all.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');
	
	/*
		todo:

		admin_user_delete.php

		user_admin.php
		user_delete.php

	*/

	$content  = '<b>Kontrollpanelen</b><br><br>';

	$content .= '<a href="admin_news.php">Nyheter</a><br>';


	//$content .= '<a href="admin_notes.php">Anteckningar</a><br>';
	$content .= '<a href="admin_admins.php">Administrat&ouml;rer</a><br>';
	//$content .= '<a href="admin_favorite_games.php">Redigera favoritspel</a><br>';
	$content .= '<a href="admin_moderatedwords.php">Redigera stoppord</a><br>';
	$content .= '<a href="admin_moderationqueue.php">Modereringsk&ouml;</a> ('.getModerationQueueCount($db).' objekt)<br>';
	$content .= '<a href="admin_newadmin.php">Skapa nytt adminkonto</a><br>';
	$content .= '<br><br>';

	$content .= '<a href="admin_events.php">Event log</a> ('.getLogEntriesCount($db, LOGLEVEL_ALL).' entries)<br>';
	$content .= '<a href="admin_phpinfo.php" target="_blank">PHP info</a><br>';

	if ($config['debug']) {
		$content .= '<hr>';
		$content .= '<a href="admin_userdatafields.php">Userdata f&auml;lt</a><br>';
		$content .= '<a href="admin_db.php">Databasen</a><br>';
		$content .= '<a href="admin_properties.php">Session properties</a><br>';

		$content .= '<hr>';
		$content .= '<a href="admin_bug_reports.php">Bug reports</a><br>';
		$content .= '<a href="admin_assigned_tasks.php">Assigned tasks</a><br>';
		$content .= '<a href="admin_current_work.php">Current work</a><br>';
		$content .= '<a href="admin_edit_todo_lists.php">Edit todo lists</a><br>';

		$content .= '<hr>';
		$content .= '<a href="admin_showtimedsubscriptions.php">Show timed subscriptions</a><br>';
		$content .= '<a href="admin_edittimedsubscriptions.php">Edit timed subscriptions</a><br>';

		$content .= '<hr>';
		$content .= '<a href="admin_geoip.php">Re-generate functions_geoip_cc.php in C:\</a><br>';
		$content .= '<a href="admin_geoip_cities.php">Admin GeoIP cities</a><br>';
		$content .= '<a href="admin_ip.php">IP query</a><br>';
		$content .= '<a href="admin_portcheck.php">Portcheck</a><br>';
	}

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|&Ouml;versikt', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>