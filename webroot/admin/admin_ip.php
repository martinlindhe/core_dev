<?
	//takes an ip address as parameter
	if (empty($_GET['ip'])) $ip = '';
	else $ip = $_GET['ip'];

	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo 'admin IP - query IP information<br/><br/>';

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
		echo '<a href="http://www.dnsstuff.com/tools/whois.ch?ip='.$ip.'" target="_blank">whois</a><br/>';
		echo '<a href="http://www.dnsstuff.com/tools/tracert.ch?ip='.$ip.'" target="_blank">traceroute</a><br/>';
		echo '<a href="http://visualroute.visualware.com/" target="_blank">visual route (java traceroute)</a><br/>';
		echo '<a href="http://www.dnsstuff.com/tools/ping.ch?ip='.$ip.'" target="_blank">ping</a><br/>';
		echo '<br/>';

		echo '<a href="http://www.dnsstuff.com/tools/city.ch?ip='.$ip.'" target="_blank">City from IP lookup</a><br/>';
		echo '<a href="http://www.senderbase.org/search?searchString='.$ip.'" target="_blank">senderbase blacklist lookup</a><br/>';
		echo '<a href="http://openrbl.org/lookup?i='.$ip.'" target="_blank">open RLB ip lookup</a><br/>';
		echo '<br/>';
	}

	echo 'Your IP is '.$_SERVER['REMOTE_ADDR'].'<br/>';
	echo '<form method="get" action="'.$_SERVER['PHP_SELF'].'">';
	if (!empty($_GET['pr'])) echo '<input type="hidden" name="pr" value="'.$_GET['pr'].'"/>';
	echo '<input type="text" name="ip" value="'.$ip.'"/> ';
	echo '<input type="submit" class="button" value="query ip"/>';
	echo '</form>';

	require($project.'design_foot.php');
?>