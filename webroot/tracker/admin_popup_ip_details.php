<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['ip']) || !is_numeric($_GET['ip'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$geoip = $_GET['ip'];
	$ipv4 = GeoIP_to_IPv4($geoip);
	
	include('design_popup_head.php');

	echo '<h2>Details for '.getDNSCacheHostname($geoip).'</h2>';
	echo 'IP address is <b>'.$ipv4.'</b> '.getGeoIPCountryFlag($geoip).' (DNS cache is '.makeTimePeriodShort(getDNSCacheAge($geoip)).' old).<br>';
	echo '<br>';

	$whois = getWhoisData($geoip);

	$ip_ranges = getMatchingIPRanges($geoip);
	echo 'Matching IP address ranges:<br>';
	for ($i=0; $i<count($ip_ranges); $i++) {
		echo '<b>'.GeoIP_to_IPv4($ip_ranges[$i]['geoIP_start']).' - '.GeoIP_to_IPv4($ip_ranges[$i]['geoIP_end']).'</b> belongs to <b>'.$ip_ranges[$i]['name'].'</b> (<a href="admin_popup_ip_owner.php?start='.$ip_ranges[$i]['geoIP_start'].'&amp;end='.$ip_ranges[$i]['geoIP_end'].'">WHOIS details</a>).<br>';
	}
	echo '<br>';

	$list = getTrackerFrequencyByIP($db, $geoip);

	$entries_cnt = 0;
	for ($i=0; $i<count($list); $i++) $entries_cnt += $list[$i]['cnt'];

	$last_visit = getIPLastVisit($db, $geoip);

	echo 'This IP occurs in <b>'.$entries_cnt.'</b> entries, for <b>'.count($list).'</b> unique track points.<br>';
	echo 'The last entry from this IP is <b>'.makeTimePeriodShort(time()-$last_visit).'</b> old.<br><br>';

	echo '<table cellpadding=0 cellspacing=0 border=1>';
	echo '<tr>';
	echo '<th width=400>Visited track points</th>';
	echo '<th>Frequency</th>';
	echo '</tr>';

	for ($i=0; $i<count($list); $i++) {
		echo '<tr>';
		echo '<td>';
		echo $list[$i]['trackerId'].': <b>'.$list[$i]['siteName'].' - '.$list[$i]['location'].'</b>';
		echo '</td>';
		echo '<td align="right">'.$list[$i]['cnt'].'</td>';
		echo '</tr>';
	}
	echo '<tr><td colspan=2 align="right"><b>Total: '.$entries_cnt.'</b></td></tr>';
	echo '</table><br>';

	echo '<a href="admin_popup_ip_activity.php?ip='.$geoip.'">Show all activity from this IP</a><br><br>';

	$comment_list = getComments($db, COMMENT_IP_DETAILS, $geoip);
	if (count($comment_list)) {
		for ($i=0; $i<count($comment_list); $i++) {
			$title = $comment_list[$i]['timeCreated'].', '.$comment_list[$i]['userName'].' said:';
			echo MakeTrackerBox($title, nl2br($comment_list[$i]['commentText']));
			echo '<br>';
		}
	}
	echo '<a href="admin_popup_ip_comment.php?ip='.$geoip.'">Add a comment to this IP</a>';

	include('design_popup_foot.php');
?>