<?
	//Detta skript ska köras regelbundet

	include('include_all.php');
	
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
	
	
	$email_header =
'<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Övervakningsmeddelande</title>
<style type="text/css">
<!--
body {
	font-family: verdana;
}
th {
	padding: 8px;
	border: 1px solid #5B9F9E;
	background-color: #B6D999;
}
td {
	padding: 8px;
	border: 1px solid #5B9F9E;
}
-->
</style>
</head>
<body>';

	$email_footer = '<img src="cid:ai_logo"></body></html>';

	//1. Läs in en array med alla subscriptions och iterera den
	$list = getSubscriptions($db, SUBSCRIBE_TRACKSITE);

	for ($i=0; $i<count($list); $i++)
	{
		$output = $email_header;

		//2. För varje subscription, kolla hur ofta den ska köras, och när den senast kördes
		$trackId = getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'trackpoint');
		if (!$trackId) continue;
		
		$trackPoint = getTrackPoint($db, $trackId);
		if (!$trackPoint) continue;

		$freq = getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'interval');

		switch ($freq)
		{
			case 'daily':
				//Övervakning som körs dagligen. Sammanfattar gårdagen mellan 00:00 och 23:59
				$time_from = mktime( 0,  0,  0, date('m'), date('d')-1, date('Y'));	//Igår 00:00:00
				$time_to   = mktime(23, 59, 59, date('m'), date('d')-1, date('Y'));	//Igår 23:59:59

				$freq_name = 'dagligen';
				$freq_periodtext = getLongDate($time_from);
				$subject = 'Daglig rapport '.getShortDate($time_from).' - '.$trackPoint['siteName'];
				break;

			case 'weekly':
				//Övervakning som körs veckovis. Sammanfattar förra veckan mellan Måndag 00:00 och Söndag 23:59
				$curr_weekday  = date('N');	//1 (for Monday) through 7 (for Sunday)
				$time_to = mktime(23,59,59, date('m'), date('d')-$curr_weekday, date('Y'));
				$time_from = $time_to - (3600 * 24 * 7) + 1;

				$freq_name = 'veckovis';
				$freq_periodtext = 'vecka '.date('W, Y',$time_from).' ('.getShortDate($time_from).' - '.getShortDate($time_to).')';
				$subject = 'Veckorapport v.'.date('W, Y',$time_from).' - '.$trackPoint['siteName'];
				break;

			case 'monthly':
				//Övervakning som körs månadsvis. Sammanfattar hela förra månaden
				$last_month = mktime(0, 0, 0, date('m')-1, date('d'), date('Y'));
				$last_month_days = date('t', $last_month);

				$time_from = mktime(0, 0, 0, date('m')-1, 1, date('Y'));
				$time_to = mktime(23, 59, 59, date('m')-1, $last_month_days, date('Y'));

				$freq_name = 'varje månad';
				$freq_periodtext = getLongMonth($time_from);
				$subject = 'Månadsrapport '.getLongMonth($time_from).' - '.$trackPoint['siteName'];
				break;

			default: continue; //invalid frequency (ska ej förekomma)
		}

		$check = checkSubscriptionHistoryPeriod($db, $list[$i]['subscriptionId'], $time_from, $time_to);
		if ($check) {
			echo 'Bevakning #'.$list[$i]['subscriptionId'].': Utskick för denna period är redan registrerat, skippar.<br><br>';
			continue;
		}

		//Om det är dags, sammanställ ett mail
		echo 'Bevakning #'.$list[$i]['subscriptionId'].': Utskick ej gjort, skapar...<br>';

		$output .= 'Detta är ett autogenererat meddelande från Agent Interactive.<br>';
		$output .= 'Om du har frågor är du välkommen att kontakta oss på <a href="mailto:info@agentinteractive.se">info@agentinteractive.se</a>.<br><br>';

		$output .= 'Statistik för mätpunkten "<b>'.$trackPoint['location'].'</b>" på sajten "<b>'.$trackPoint['siteName'].'</b>".<br>';
		$output .= 'Informationen sammanställs <b>'.$freq_name.'</b>, detta mail sammanfattar <b>'.$freq_periodtext.'</b>.<br><br>';

		$include_top_search_phrases = getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'include_top_search_phrases');
		$include_search_popularity  = getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'include_search_popularity');

		if ($include_top_search_phrases || $include_search_popularity)
		{
			//Parse referrers, both features need this data
			$refs = getTrackerEntriesByReferrers($db, $trackId, $time_from, $time_to);
			$search = parseReferrers($refs);
		}

		$include_browser_popularity = getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'include_browser_popularity');
		$include_os_popularity      = getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'include_os_popularity');

		if ($include_browser_popularity || $include_os_popularity)
		{
			//Parse broweser stats, both these features need this data
			
			$browser_list = getTrackerBrowserinfoTimeperiod($db, $time_from, $time_to, $trackId);
			$browser_stats = parseBrowserStats($browser_list);
		}

		//Most popular search phrases
		if ($include_top_search_phrases)
		{
			$output .= 'Diagram för de '.$include_top_search_phrases.' populäraste sökorden under perioden:<br>';

			//Loop through $search['queries'] array and display the first X entries (they are ordered by descending popularity)
			$cnt = 0;

			$output .= '<table cellspacing=3>';
			$output .= '<tr><th>Sökfras</th><th>Frekvens</th></tr>';
				
			if (!empty($search['queries'])) {
				foreach ($search['queries'] as $key => $val) {
					$output .= '<tr><td>'.$key.'</td><td>'.$val['cnt'].'</td></tr>';
					$cnt++;
					if ($cnt == $include_top_search_phrases) break;
				}
			}

			$output .= '</table><br><br>';
		}
			
		//Search engine popularity
		if ($include_search_popularity)
		{
			$output .= 'Diagram för sökmotors-andelar:<br>';

			$output .= '<table cellspacing=3>';
			$output .= '<tr><th>Sökmotor</th><th>Andel</th></tr>';
				
			foreach ($search['engines'] as $key => $val) {
				if (!$val) continue;

				$pct = round($val / $search['engine_cnt'] * 100, 1);

				$output .= '<tr><td>'.$key.'</td><td>'.$pct.'% ('.$val.')</td></tr>';
			}
			$output .= '</table><br><br>';
		}


		//Browser popularity
		if ($include_browser_popularity)
		{
			//Skapar en lista över de X populäraste webbläsarna
			$browser_show_limit	= getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'browser_popularity_cnt');
			$browser_ver_limit	= getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'browser_versions_cnt');
			
			//todo: $browser_ver_limit är ej implementerat (datan finns i $browser_stats arrayen). Listar bara de X populäraste webbläsarna

			$output .= 'Diagram för de '.$browser_show_limit.' populäraste webbläsarna:<br>';

			$output .= '<table cellspacing=3>';
			$output .= '<tr><th>Webbläsare</th><th>Andel</th></tr>';

			$cnt = 0;
			if (!empty($browser_stats['stats'])) {
				foreach ($browser_stats['stats'] as $browser_key => $browser_val)
				{
					$output .= '<tr><td>'.$browser_key.'</td><td>'.round($browser_val['tot_pct'],1).'% ('.formatNumberSize($browser_val['tot_cnt']).')</td></tr>';
					$cnt++;
					if ($cnt >= $browser_show_limit) break;
				}
			}

			$output .= '</table><br><br>';
		}
	
		//OS popularity, EJ IMPLEMENTERAT
		if ($include_os_popularity)
		{
			$os_cnt =	getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'os_popularity_cnt');

			$output .= 'Diagram för de '.$os_cnt.' populäraste operativsystemen:<br><br>';

			$output .= '<table cellspacing=3>';
			$output .= '<tr><th>Operativsystem</th><th>Andel</th></tr>';

			if (!empty($browser_stats['OS'])) {
				$cnt = 0;
				foreach ($browser_stats['OS'] as $os_key => $os_val)
				{
					$os_pct = round($os_val / count($browser_list) * 100, 1);

					$output .= '<tr><td>'.$os_key.'</td><td>'.$os_pct.'% ('.$os_val.')</td></tr>';
					$cnt++;
					if ($cnt >= $os_cnt) break;
				}
			}

			$output .= '</table><br><br>';
		}
			
		//Google Pageranks (Show for All pages / Only show start page PR)
		$include_google_pr = getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'include_google_pr');
		if ($include_google_pr)
		{
			$pr_mode = getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'google_pr_mode');
				
			switch ($pr_mode)
			{
				case 'startpage':
					$check_url = 'http://'.$trackPoint['siteName'].'/';	//fixme: låt GetGooglePR() snygga till URL:en
					$pr = GetGooglePR($db, $check_url);
					$output .= 'Google PageRank för startsidan: <b>PR '.$pr.'</b><br><br>';
					break;
						
				default:
					$output .= 'Info om Google PageRank, mode '.$pr_mode.' = EJ IMPLEMENTERAT ÄNNU!<br><br>';
					break;
			}
		}

		//Google Indexing status, EJ IMPLEMENTERAT
		$include_google_indexing = getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'include_google_indexing');
		if ($include_google_indexing)
		{
			$output .= 'Google Indexing status (EJ IMPLEMENTERAT ÄNNU!):<br><br>';
		}
			
		//Google search phrase ranking, EJ IMPLEMENTERAT
		$include_google_ranking = getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'include_google_ranking');
		if ($include_google_ranking)
		{
			//todo: hantera ett okänt antal keywords
			$keyword1 = getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'google_rank_1');
			$keyword2 = getSetting($db, SETTING_SUBSCRIPTION, $list[$i]['subscriptionId'], 'google_rank_2');
				
			$output .= 'Google ranking på vissa keywords (EJ IMPLEMENTERAT ÄNNU!):<br><br>';
		}
			
		$mails = getEmailSubscribers($db, $list[$i]['subscriptionId']);

		$output .= $email_footer;
			
		//echo 'Mottagare: <b>'.$mail_to.'</b><br><br>';
		//echo $output;

		if ($mails) {
			smtp_auth_send_multiple($mails, $subject, $output);

			//Efter att ha mailat ut brevet så sparar vi historik över vad som mailats, när och till vilka.
			addSubscriptionHistory($db, $list[$i]['subscriptionId'], $time_from, $time_to, implode(', ', $mails), $output);
		} else {
			$error = 'Övervakning '.$list[$i]['subscriptionId'].' saknar mottagare, kan ej skicka mail!';
			echo $error.'<br><br>';
			debugLog($db, $error);
		}
	}
	
?>