<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');

	echo '<h2>Web trends</h2>';
	echo 'Graphs of current web trends<br><br>';

	//for ($month = 1; $month <= 12; $month++) {
	//Väljer tidsperioden förra månaden
	$year = date('Y');
	$month = date('n') - 1;	//Last month
	$time_from = mktime(0, 0, 0, $month, 1, $year);
	$days_in_month = date('t', $time_from);
	$time_to   = mktime(23, 59, 59, $month, $days_in_month, $year);

	{ //Genererar & visar senaste web browser trend graph (förra månadens)
		$filename = 'webtrends/browsers_'.date('Y.m', $time_from);
		if (!file_exists($filename.'.png')) {
			//echo 'Generating web browser trends graphs for '.formatShortMonth($time_from).'...<br>';
			writeWebBrowserTrendsImage($db, $filename, $time_from, $time_to);
		}
		echo '<img src="'.$filename.'.png" alt="Web browser trends for '.formatShortMonth($time_from).'"> ';
	}
	
	{ //Genererar & visar senaste search engine trend graph (förra månaden)
		$filename = 'webtrends/searchengines_'.date('Y.m', $time_from);
		if (!file_exists($filename.'.png')) {
			//echo 'Generating search engine trends graphs for '.formatShortMonth($time_from).'...<br>';
			writeSearchEngineTrendsImage($db, $filename, $time_from, $time_to);
		}
		echo '<img src="'.$filename.'.png" alt="Search engine trends for '.formatShortMonth($time_from).'"><br>';
	}
	

	echo '<br>';
	
	echo '<a href="admin_webtrends_archive.php">Webtrends archive</a>';

	include('design_foot.php');
?>