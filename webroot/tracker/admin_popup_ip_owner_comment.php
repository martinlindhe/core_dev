<?
	include('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_GET['id']) || !is_numeric($_GET['id'])) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$entryId = $_GET['id'];
	$whois = getCachedWhoisEntryByID($entryId);

	if (!$whois) {
		header('Location: '.$config['start_page']);
		die;
	}
	

	if (!empty($_POST['comment'])) {
		addComment($db, COMMENT_IP_RANGE, $entryId, $_POST['comment']);
		header('Location: admin_popup_ip_owner.php?start='.$whois['geoIP_start'].'&end='.$whois['geoIP_end']);
		die;
	}

	include('design_popup_head.php');

	echo '<h2>Comments for IP range <b>'.GeoIP_to_IPv4($whois['geoIP_start']).' - '.GeoIP_to_IPv4($whois['geoIP_end']).'</b></h2>';
	
	echo '<a href="admin_popup_ip_owner.php?start='.$whois['geoIP_start'].'&amp;end='.$whois['geoIP_end'].'">Return to IP address range owner information</a><br><br>';
	
	echo 'Add a new comment:<br>';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$entryId.'">';
	echo '<textarea name="comment" cols=50 rows=8></textarea><br><br>';
	echo '<input type="submit" class="button" value="Add comment">';
	echo '</form>';
	
	include('design_popup_foot.php');
?>