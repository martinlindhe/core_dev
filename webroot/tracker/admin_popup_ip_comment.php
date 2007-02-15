<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['ip']) || !is_numeric($_GET['ip'])) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$geoip = $_GET['ip'];
	$ipv4 = GeoIP_to_IPv4($geoip);

	if (!empty($_POST['comment'])) {
		addComment($db, COMMENT_IP_DETAILS, $geoip, $_POST['comment']);
		header('Location: admin_popup_ip_details.php?ip='.$geoip);
		die;
	}

	include('design_popup_head.php');

	echo '<h2>Comments for '.getDNSCacheHostname($geoip).'</h2>';
	
	echo '<a href="admin_popup_ip_details.php?ip='.$geoip.'">Return to IP details</a><br><br>';
	
	echo 'Add a new comment:<br>';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?ip='.$geoip.'">';
	echo '<textarea name="comment" cols=50 rows=8></textarea><br><br>';
	echo '<input type="submit" class="button" value="Add comment">';
	echo '</form>';
	
	include('design_popup_foot.php');
?>