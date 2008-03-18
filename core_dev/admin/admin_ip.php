<?
	//takes an ip address as parameter
	if (empty($_GET['ip'])) $ip = '';
	else $ip = $_GET['ip'];

	require_once('find_config.php');
	$session->requireAdmin();
	if (!$ip) $session->requireSuperAdmin();

	if (empty($_GET['user'])) $user = '';
	else $user = $_GET['user'];

	if (isset($_GET['block'])) $block = 1;
	else $block = 0;

	$userId = '';
	if ($user) {
		$userId = Users::getId($user);
	}

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');
	if ($session->isSuperAdmin) {
		echo createMenu($super_admin_menu, 'blog_menu');
		echo createMenu($super_admin_tools_menu, 'blog_menu');
	}

	echo '<h2>Query IP information</h2>';

	if ($ip && !$block) {
		$geoip = IPv4_to_GeoIP($ip);

		echo '<h1>'.$ip.' ('.gethostbyaddr($ip).')</h1>';
		echo '<br/><br/>';
		
		$list = Users::byIP($geoip);

		echo 'This IP is associated with '.count($list).' registered users:<br/>';
		foreach ($list as $row) {
			echo Users::link($row['userId'], $row['userName']).'<br/>';
		}
		echo '<hr/>';
		echo '<a href="http://www.dnsstuff.com/tools/whois.ch?ip='.$ip.'" target="_blank">Perform whois lookup</a><br/>';
		echo '<a href="http://www.dnsstuff.com/tools/tracert.ch?ip='.$ip.'" target="_blank">Perform traceroute</a><br/>';
		echo '<a href="http://www.dnsstuff.com/tools/ping.ch?ip='.$ip.'" target="_blank">Ping IP</a><br/>';
		echo '<a href="http://www.dnsstuff.com/tools/city.ch?ip='.$ip.'" target="_blank">Lookup city from IP</a><br/>';
		echo '<hr/>';

		//Admin notes
		showComments(COMMENT_ADMIN_IP, $geoip);

	} else if ($userId) {
		$name = Users::getName($userId);
		echo '<h1>'.$name.'</h1>';
		echo '<br/><br/>';
		
		$ips = Users::getIPByUser($userId);

		echo '<table><tr><td><b>IP</b></td><td><b>Tid</b></td><td>&nbsp;</td></tr>';
			foreach ($ips as $ip) {
				echo '<tr>';
					echo '<td>'.GeoIP_to_IPv4($ip['IP']).'</td>';
					echo '<td>'.$ip['time'].'</td>';
					echo '<td><a href="'.$_SERVER['PHP_SELF'].'?block&ip='.GeoIP_to_IPv4($ip['IP']).'">Blockera</a></td>';
				echo '</tr>';
			}
		echo '</table>';
		
	} else if ($block) {
		addBlock(BLOCK_IP, IPv4_to_GeoIP($ip));
		echo '<h2>IP Blocked</h2>';
	} else {

		echo 'Your IP is '.$_SERVER['REMOTE_ADDR'].'<br/>';
		echo '<form method="get" action="'.$_SERVER['PHP_SELF'].'">';
		if (!empty($_GET['pr'])) echo '<input type="hidden" name="pr" value="'.$_GET['pr'].'"/>';
		echo '<input type="text" name="ip" value="'.$ip.'"/> Ip';
		echo '<br/>or';
		echo '<br/><input type="text" name="user" value="'.$ip.'"/> User';
		echo '<br/><input type="submit" class="button" value="query"/>';
		echo '</form>';
	}

	require($project.'design_foot.php');
?>
