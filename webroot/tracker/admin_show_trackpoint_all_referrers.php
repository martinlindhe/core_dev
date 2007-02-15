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
	$trackSite = getTrackSite($db, $siteId);

	if (!$trackSite) {
		header('Location: '.$config['start_page']);
		die;
	}

	//Will return with $time_from, $time_to set
	include('timeperiod_selector.php');

	include('design_head.php');

	echo '<h2>Raw referrer data</h2>';
	echo 'All referrer data logged for track point <b>'.$trackPoint['siteName'].' - '.$trackPoint['location'].'</b> (#'.$trackPoint['trackerId'].')<br>';
	echo '<br>';

	$list = getTrackerEntriesByReferrers($db, $trackId, $time_from, $time_to);
	
	if (!count($list)) {
		echo 'No referrer strings has been collected for this track point.';
		include('design_foot.php');
		die;
	}

	echo '<b>Skipping internal referrers (entries from http://'.$trackSite['siteName'].', http://www.'.$trackSite['siteName'].').</b><br><br>';

	//Display all referrer data
	echo '<table cellpadding=0 cellspacing=0 border=1>';
	echo '<tr>';
	echo '<th>Where</th>';
	echo '<th>All referrer data</th>';
	echo '<th>Frequency</th>';
	echo '</tr>';

	$referrer_entry_count = 0;
	$referrer_total_count = 0;
	
	parseReferrers($list);	//populates $list with 'search_engine' data for each entry

	
	for ($i=0; $i<count($list); $i++)
	{
		if (!$list[$i]['search_engine']) {
			//Skippar alla referrers som är samma domän som track site
			
			$check = 'http://'.$trackSite['siteName'];
			if (substr($list[$i]['referrer'], 0, strlen($check)) == $check) {
				continue;
			}

			$check = 'http://www.'.$trackSite['siteName'];
			if (substr($list[$i]['referrer'], 0, strlen($check)) == $check) {
				continue;
			}
		}

		echo '<tr>';

		echo '<td align="center">';
		
		$safe_query = '';
		if (!empty($list[$i]['search_query'])) {
			$safe_query = htmlspecialchars($list[$i]['search_query'], ENT_COMPAT, 'UTF-8');
		}

		switch ($list[$i]['search_engine']) {
			case 'Google':		echo '<img src="design/search_google.png" width=37 height=14 alt="Google" title="Google query: '.$safe_query.'" align="top">'; break;
			case 'MSN': 			echo '<img src="design/search_msn.png" width=37 height=14 alt="MSN" title="MSN query: '.$safe_query.'" align="top">'; break;
			case 'Yahoo':			echo '<img src="design/search_yahoo.png" width=37 height=14 alt="Yahoo" title="Yahoo query: '.$safe_query.'" align="top">'; break;
			case 'Altavista':	echo '<img src="design/search_altavista.png" width=37 height=14 alt="Altavista" title="Altavista query: '.$safe_query.'" align="top">'; break;
			case 'AOL':				echo '<img src="design/search_aol.png" width=37 height=14 alt="AOL" title="AOL query: '.$safe_query.'" align="top">'; break;
			case 'Eniro':			echo '<img src="design/search_eniro.png" width=37 height=14 alt="Eniro" title="Eniro query: '.$safe_query.'" align="top">'; break;
			case 'Spray':			echo '<img src="design/search_spray.png" width=37 height=14 alt="Spray" title="Spray query: '.$safe_query.'" align="top">'; break;
			case 'Sesam':			echo '<img src="design/search_sesam.png" width=37 height=14 alt="Sesam" title="Sesam query: '.$safe_query.'" align="top">'; break;

			default:
				//Display a "???" icon for possible unidentified browser referrals
				$url = parse_url($list[$i]['referrer']);

				if (!empty($url['query']) &&
					(
						strpos($list[$i]['referrer'], 'google')!==FALSE ||
						strpos($list[$i]['referrer'], 'search.msn')!==FALSE ||
						strpos($list[$i]['referrer'], 'search.yahoo')!==FALSE ||
						strpos($list[$i]['referrer'], 'altavista')!==FALSE ||
						strpos($list[$i]['referrer'], 'aol')!==FALSE ||
						strpos($list[$i]['referrer'], 'eniro.se')!==FALSE ||
						strpos($list[$i]['referrer'], 'spray.se')!==FALSE ||
						strpos($list[$i]['referrer'], 'sesam')!==FALSE
					) &&
					(
						strpos($list[$i]['referrer'], 'mail.google.com')===FALSE &&
						strpos($list[$i]['referrer'], 'mail.spray.se')===FALSE &&
						strpos($list[$i]['referrer'], 'images.google.')===FALSE	&&	//images.google.com, images.google.se etc
						strpos($list[$i]['referrer'], '/dnserror.aspx')===FALSE && //IE dns error queries: http://sea.search.msn.se/dnserror.aspx?FORM=DNSAS&q=sportal.nu
						strpos($list[$i]['referrer'], 'babelfish.altavista.com')===FALSE &&
						strpos($list[$i]['referrer'], 'gulasidorna.eniro.se')===FALSE	//..
					)
				) {
					echo '<img src="design/q.png" width=30 height=14 alt="?" title="Possible search engine, script: '.$url['path'].', query: '.$url['query'].'">';
				} else {
					echo '&nbsp;';
				}
			
		}
		echo '</td>';


		echo '<td>';
		echo '<a href="'.$list[$i]['referrer'].'">'.create_tooltip($list[$i]['referrer'], 55).'</a>';
		echo '</td>';

		echo '<td align="right">'.$list[$i]['cnt'].'</td>';
		$referrer_entry_count += $list[$i]['cnt'];
		$referrer_total_count++;
		echo '</tr>';
	}
	echo '<tr><td colspan=3 align="right"><b>Unique: '.$referrer_total_count.', Total: '.$referrer_entry_count.'</b></td></tr>';
	echo '</table>';

	include('design_foot.php');
?>