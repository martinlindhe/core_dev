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

	//Will return with $time_from, $time_to set
	include('timeperiod_selector.php');

	include('design_head.php');

	echo '<h2>Browser stats</h2>';
	echo 'Browser statistics for track point <b>'.$trackPoint['siteName'].' - '.$trackPoint['location'].'</b> (#'.$trackPoint['trackerId'].')<br>';
	echo '<br>';

	if ($time_from && $time_to) {
		echo '<b>Showing data collected between '.getFullDate($time_from).' and '.getFullDate($time_to).'</b><br><br>';

		$list = getTrackerBrowserinfoTimeperiod($db, $time_from, $time_to, $trackId);
	} else {
		echo '<b>Showing ALL collected data for this track point\'s lifetime.</b><br><br>';

		$list = getTrackerBrowserinfo($db, $trackId);
	}

	if (!count($list)) {
		echo 'No user agent strings found for this track point.';
		include('design_foot.php');
		die;
	}


	//analyzes the User-Agent data
	$browser_stats = parseBrowserStats($list);



	echo 'Analyzed <b>'.formatNumberSize(count($list)).'</b> user agent strings';
	if ($_SESSION['isSuperAdmin'] && !empty($browser_stats['unknown'])) {
		echo ' (<b>'.formatNumberSize(count($browser_stats['unknown'])).'</b> not fully recognized)';
	}
	echo ':<br><br>';


	echo '<table cellpadding=0 cellspacing=0 border=1>';
	echo '<tr>';
	echo '<th width=250>Browser popularity</th>';
	echo '<th>Frequency</th>';

	$i = 0;
	foreach ($browser_stats['stats'] as $browser_key => $browser_val)
	{
		$i++;
		echo '<tr>';
			echo '<td>';
				echo '<img src="design/plus.png" name="exp'.$i.'" width=12 height=12 align="top" alt="Expand" onClick="toggle_div_w_img(\'div'.$i.'\',\'exp'.$i.'\',\'design/minus.png\')"> ';
				echo $browser_key;
				echo '<div id="div'.$i.'" style="background-color: #EECC88; margin: 2px; display: none;">';

				//sort the array by the version numbers, ascending
				ksort($browser_stats['name'][$browser_key]);

				foreach ($browser_stats['name'][$browser_key] as $version_number => $version_cnt) {
					$version_pct = ($version_cnt / $browser_stats['stats'][$browser_key]['tot_cnt'])*100;
					$version_pct = round($version_pct, 1);
					echo 'Version '.$version_number.' = '.$version_pct.'% ('.formatNumberSize($version_cnt).')<br>';
				}

				echo '</div>';
			echo '</td>';

			echo '<td>'.round($browser_val['tot_pct'],1).'% ('.formatNumberSize($browser_val['tot_cnt']).')</td>';

		echo '</tr>';
	}
	echo '<tr><td colspan=2 align="right"><b>Total: '.formatNumberSize(count($list)).'</b></td></tr>';
	echo '</table><br>';

	echo '<table cellpadding=0 cellspacing=0 border=1>';
	echo '<tr>';
	echo '<th width=250>Operating System popularity</th>';
	echo '<th>Frequency</th>';
	echo '</tr>';
	foreach ($browser_stats['OS'] as $key => $val) {
		echo '<tr>';
		echo '<td>'.$key.'</td>';
		
		$os_pct = round($val / count($list) * 100, 1);
		echo '<td>'.$os_pct.'% ('.formatNumberSize($val).')</td>';
		echo '</tr>';
	}
	echo '<tr><td colspan=2 align="right"><b>Total: '.formatNumberSize(count($list)).'</b></td></tr>';
	echo '</table><br>';

	if ($_SESSION['isSuperAdmin'] && !empty($browser_stats['unknown'])) {
		//List unknown/partially parsed user agents, only for super admins

		for ($i=0; $i<count($browser_stats['unknown']); $i++) {
			if (!isset($unknown_browser_ids[ $browser_stats['unknown'][$i] ])) $unknown_browser_ids[ $browser_stats['unknown'][$i] ] = 0;
			$unknown_browser_ids[ $browser_stats['unknown'][$i] ]++;
		}
		arsort($unknown_browser_ids);

		echo '<table cellpadding=0 cellspacing=0 border=1>';
		echo '<tr>';
		echo '<th>Not fully recognized user agent strings</th>';
		echo '<th>Detected</th>';
		echo '<th>Frequency</th>';
		echo '</tr>';
		foreach ($unknown_browser_ids as $key => $val)
		{
			$browser = GetBrowser($key);
			echo '<tr>';
			$key = dbStripSlashes($key);
			echo '<td>'.htmlentities($key).'</td>';
			echo '<td>'.$browser['name'].' v'.$browser['version'].', '.$browser['OS'].'</td>';
			echo '<td align="right">'.$val.'</td>';
			echo '</tr>';
		}
		echo '<tr><td colspan=3 align="right"><b>Unique: '.count($unknown_browser_ids).', Total: '.count($browser_stats['unknown']).'</b></td></tr>';		
		echo '</table>';
	}

	include('design_foot.php');
?>