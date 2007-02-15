<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	
	echo '<h2>Tracker statistics</h2>';

	echo 'Total server load:<br><br>';
	
	
	
	$today_start	= mktime(0, 0, 0, date('n'), date('j'), date('Y'));
	$today_end		= time();
	$cnt = getTotalTrackerEntries($db, $today_start, $today_end);
	$cnt_persec = round($cnt / (3600*24), 2);
	echo 'Today, so far: '.$cnt.' views, '.$cnt_persec.' views/sec<br>';
	echo '('.getFullDate($today_start). ' - '.getFullDate($today_end).')<br><br>';


	$yesterday_start	= mktime(0, 0, 0, date('n'), date('j')-1, date('Y'));
	$yesterday_end		= $yesterday_start + (3600*24)-1;
	$cnt = getTotalTrackerEntries($db, $yesterday_start, $yesterday_end);
	$cnt_persec = round($cnt / (3600*24), 2);
	echo 'Yesterday: '.$cnt.' views, '.$cnt_persec.' views/sec<br>';
	echo '('.getFullDate($yesterday_start). ' - '.getFullDate($yesterday_end).')<br><br>';


	$current_weekday = date('N');	//1=monday, 7=sunday
	$lastweek_start	= mktime(0, 0, 0, date('n'), date('j')-(6+$current_weekday), date('Y'));
	$lastweek_end		= $lastweek_start + (3600*24*7)-1;
	$cnt = getTotalTrackerEntries($db, $lastweek_start, $lastweek_end);
	$cnt_persec = round($cnt / (3600*24*7), 2);

	echo 'Last week: '.$cnt.' views, '.$cnt_persec.' views/sec<br>';
	echo '('.getFullDate($lastweek_start). ' - '.getFullDate($lastweek_end).')<br>';

	echo '<br>';
	echo 'Server time is <b>'.getFullDate(time()).' ('.date_default_timezone_get().')</b>.';

	include('design_foot.php');
?>