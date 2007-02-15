<?
	include('include_all.php');

	if (!$_SESSION['isAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	if (empty($_GET['start']) || !is_numeric($_GET['start']) || empty($_GET['end']) || !is_numeric($_GET['end'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$geoip_start = $_GET['start'];
	$geoip_end = $_GET['end'];
	
	if (isset($_GET['renew_whois'])) {
		//Force WHOIS cache update
		forceWHOISCacheUpdate($geoip_start);
	}

	if (isset($_POST['private'])) {
		markWHOISRangePrivate($geoip_start, $geoip_end, $_POST['private']);
	}

	$whois = getExactWHOISData($geoip_start, $geoip_end);
	if (!$whois) {
		header('Location: '.$config['start_page']);
		die;
	}

	if ($_SESSION['isSuperAdmin'] && isset($_GET['delete_whois'])) {
		//Force WHOIS cache update
		deleteWHOISEntry($whois['entryId']);
		deleteComments($db, COMMENT_IP_RANGE, $whois['entryId']);

		include('design_popup_head.php');
		
		echo 'The specified IP range has been deleted!';
		
		include('design_popup_foot.php');
		die;
	}
	
	$list = getUniqueIPFromRange($db, $geoip_start, $geoip_end);

	$tot = 0;
	for ($i=0; $i<count($list); $i++) $tot += $list[$i]['cnt'];

	$geoip_range = $geoip_end - $geoip_start + 1;
	$geoip_range_pct = round((count($list) / $geoip_range) * 100, 2);
	
	include('design_popup_head.php');
?>
<script type="text/javascript">
function IP_SubmitChange() {
	document.setprivate.submit();
}
</script>
<?
	echo '<h2>IP address range owner information</h2>';
	echo 'Address range: <b>'.GeoIP_to_IPv4($geoip_start).' - '.GeoIP_to_IPv4($geoip_end).'</b><br>';
	echo 'Address space: <b>'.$geoip_range.'</b> IP\'s, ';
	echo '<a href="admin_popup_ip_owner_list.php?start='.$geoip_start.'&amp;end='.$geoip_end.'"><b>'.count($list).'</b> tracked ('.$geoip_range_pct.'%)</a><br>';
	echo 'We have registered <b>'.$tot.'</b>  unique track entries from this IP range.<br>';
	echo '<br>';
	
	$box  = '<b>'.$whois['name']."</b>\n\n";
	$box .= "<b>Address:</b>\n". $whois['address']."\n\n";
	if ($whois['phone']) $box .= "<b>Phone:</b>\n".$whois['phone']."\n\n";
	if ($_SESSION['isSuperAdmin']) $box .= "<b>Source:</b> ".$whois['source']."\n\n";
	$box .= '<form name="setprivate" method="post" onClick="IP_SubmitChange();" action="'.$_SERVER['PHP_SELF'].'?start='.$geoip_start.'&amp;end='.$geoip_end.'">';
	$box .= '<input name="private" type="hidden" value="0">';
	$box .= '<input name="private" type="checkbox" value="1"';
	if ($whois['privateRange']) $box .= ' checked';
	$box .= '> Private IP range';
	$box .= '</form>';

	echo MakeTrackerBox('WHOIS details', $box, false).'<br>';

	echo 'WHOIS cache is '.makeTimePeriodShort(time()-$whois['timeUpdated']).' old ';
	echo '(<a href="'.$_SERVER['PHP_SELF'].'?start='.$geoip_start.'&amp;end='.$geoip_end.'&amp;renew_whois">Renew WHOIS info</a>)<br>';
	echo '<a href="http://www.dnsstuff.com/tools/whois.ch?ip='.GeoIP_to_IPv4($geoip_start).'" target="_blank">WHOIS lookup</a><br>';
	echo '<br>';

	$comment_list = getComments($db, COMMENT_IP_RANGE, $whois['entryId']);
	if (count($comment_list)) {
		for ($i=0; $i<count($comment_list); $i++) {
			$title = $comment_list[$i]['timeCreated'].', '.$comment_list[$i]['userName'].' said:';
			echo MakeTrackerBox($title, nl2br($comment_list[$i]['commentText']));
			echo '<br>';
		}
	}
	echo '<a href="admin_popup_ip_owner_comment.php?id='.$whois['entryId'].'">Add a comment</a><br><br>';

	if ($_SESSION['isSuperAdmin']) {
		echo '<b><a href="'.$_SERVER['PHP_SELF'].'?start='.$geoip_start.'&amp;end='.$geoip_end.'&amp;delete_whois">Delete WHOIS entry</a></b>';
	}

	include('design_popup_foot.php');
?>