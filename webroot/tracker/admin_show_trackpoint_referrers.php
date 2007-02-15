<?
	include('include_all.php');

	if (!$_SESSION['loggedIn'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
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

	//Will return with $time_from, $time_to set
	include('timeperiod_selector.php');

	include('design_head.php');

	echo '<h2>Referrer details</h2>';
	echo 'Referrer details logged for track point <b>'.$trackPoint['siteName'].' - '.$trackPoint['location'].'</b> (#'.$trackPoint['trackerId'].')<br>';
	echo '<br>';

	if ($time_from && $time_to) {
		echo '<b>Showing data collected between '.getFullDate($time_from).' and '.getFullDate($time_to).'</b><br><br>';
	} else {
		echo '<b>Showing ALL collected data for this track point\'s lifetime.</b><br><br>';
	}

	$list = getTrackerEntriesByReferrers($db, $trackId, $time_from, $time_to);
	
	if (!count($list)) {
		echo 'No referrer strings has been collected for this track point.';
		include('design_foot.php');
		die;
	}

	$search = parseReferrers($list);
	
	//echo '<pre>'; print_r($search);


	echo 'Identified <b>'.$search['engine_cnt'].'</b> search engine queries out of <b>'.$search['referrer_cnt'].'</b> referrer entries:<br><br>';

	if ($search['engine_cnt']) {

		//Display most popular search phrases from search engines
		echo '<table cellpadding=0 cellspacing=0 border=1>';
		echo '<tr>';
		echo '<th width=350>Search phrase popularity</th>';
		echo '<th>Frequency</th>';
		echo '</tr>';
		
		$i=0;
		foreach ($search['queries'] as $key => $val) {
			$i++;
			echo '<tr>';
			echo '<td>'.$key.'</td>';
			echo '<td>';
				echo '<img src="design/plus.png" name="exp'.$i.'" width=12 height=12 align="top" alt="Expand" onClick="toggle_div_w_img(\'div'.$i.'\',\'exp'.$i.'\',\'design/minus.png\')"> ';
				echo round($val['cnt']/$search['engine_cnt']*100,1).'% ('.$val['cnt'].')';
				
				echo '<div id="div'.$i.'" style="background-color: #EECC88; margin: 2px; display: none;">';

				if (!empty($val['google']))		echo $image_google.' '.		round($val['google']/$val['cnt']*100,1).'% ('.$val['google'].')<br>';
				if (!empty($val['msn']))			echo $image_msn.' '.			round($val['msn']/$val['cnt']*100,1).'% ('.$val['msn'].')<br>';
				if (!empty($val['yahoo']))		echo $image_yahoo.' '.		round($val['yahoo']/$val['cnt']*100,1).'% ('.$val['yahoo'].')<br>';
				if (!empty($val['altavista']))echo $image_altavista.' '.round($val['altavista']/$val['cnt']*100,1).'% ('.$val['altavista'].')<br>';
				if (!empty($val['aol']))			echo $image_aol.' '.			round($val['aol']/$val['cnt']*100,1).'% ('.$val['aol'].')<br>';
				if (!empty($val['eniro']))		echo $image_eniro.' '.		round($val['eniro']/$val['cnt']*100,1).'% ('.$val['eniro'].')<br>';
				if (!empty($val['spray']))		echo $image_spray.' '.		round($val['spray']/$val['cnt']*100,1).'% ('.$val['spray'].')<br>';
				if (!empty($val['sesam']))		echo $image_sesam.' '.		round($val['sesam']/$val['cnt']*100,1).'% ('.$val['sesam'].')<br>';
				echo '</div>';
			echo '</td>';
			echo '</tr>';
		}
		echo '<tr><td colspan=2 align="right"><b>Unique: '.count($search['queries']).', Total: '.$search['engine_cnt'].'</b></td></tr>';
		echo '</table>';
		echo '<br>';

		arsort($search['engines']);

		//Display search engine popularity
		echo '<table cellpadding=0 cellspacing=0 border=1>';
		echo '<tr>';
		echo '<th width=200>Search engine popularity</th>';
		echo '<th>Frequency</th>';
		foreach ($search['engines'] as $key => $val) {
			if (!$val) continue;

			echo '<tr>';
			echo '<td>';
			switch ($key) {
				case 'google':		echo $image_google; break;
				case 'msn': 			echo $image_msn; break;
				case 'yahoo':			echo $image_yahoo; break;
				case 'altavista':	echo $image_altavista; break;
				case 'aol':				echo $image_aol; break;
				case 'eniro':			echo $image_eniro; break;
				case 'spray':			echo $image_spray; break;
				case 'sesam':			echo $image_sesam; break;
			}
			echo '</td>';

			$pct = round($val / $search['engine_cnt'] * 100, 1);
			echo '<td>'.$pct.'% ('.$val.')</td>';
			echo '</tr>';
		}
		echo '<tr><td colspan=2 align="right"><b>Total: '.$search['engine_cnt'].'</b></td></tr>';
		echo '</table><br>';

	}

	if ($_SESSION['isSuperAdmin']) {
		echo '<a href="admin_show_trackpoint_all_referrers.php?id='.$trackId.'&'.$time_selected.'">Show all referrer data</a>';
	}

	include('design_foot.php');
?>