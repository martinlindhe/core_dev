<?
	//takes an ip address as parameter
	if (empty($_GET['ip'])) $ip = '';
	else $ip = $_GET['ip'];

	require_once('find_config.php');
	$session->requireAdmin();
	if (!$ip) $session->requireSuperAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');
	if ($session->isSuperAdmin) echo createMenu($super_admin_menu, 'blog_menu');

	echo '<h2>Query IP information</h2>';

	if ($ip) {
		$geoip = IPv4_to_GeoIP($ip);

		echo '<h1>'.$ip.' ('.gethostbyaddr($ip).')</h1>';
		echo '<br/><br/>';
		
		$list = getUsersByIP($geoip);

		echo 'This IP is associated with '.count($list).' registered users:<br/>';
		foreach ($list as $row) {
			echo nameLink($row['userId'], $row['userName']).'<br/>';
		}
		echo '<hr/>';
		echo '<a href="http://www.dnsstuff.com/tools/whois.ch?ip='.$ip.'" target="_blank">Perform whois lookup</a><br/>';
		echo '<a href="http://www.dnsstuff.com/tools/tracert.ch?ip='.$ip.'" target="_blank">Perform traceroute</a><br/>';
		echo '<a href="http://www.dnsstuff.com/tools/ping.ch?ip='.$ip.'" target="_blank">Ping IP</a><br/>';
		echo '<a href="http://www.dnsstuff.com/tools/city.ch?ip='.$ip.'" target="_blank">Lookup city from IP</a><br/>';
		echo '<hr/>';

		//Admin notes
		showComments(COMMENT_ADMIN_IP, $geoip);

	} else {

		echo 'Your IP is '.$_SERVER['REMOTE_ADDR'].'<br/>';
		echo '<form method="get" action="'.$_SERVER['PHP_SELF'].'">';
		if (!empty($_GET['pr'])) echo '<input type="hidden" name="pr" value="'.$_GET['pr'].'"/>';
		echo '<input type="text" name="ip" value="'.$ip.'"/> ';
		echo '<input type="submit" class="button" value="query ip"/>';
		echo '</form>';
	}

	require($project.'design_foot.php');
?>