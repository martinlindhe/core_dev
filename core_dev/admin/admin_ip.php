<?php
/**
 * $Id$
 *
 * Takes an ip address as parameter
 */

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
if ($user) $userId = Users::getId($user);

require('design_admin_head.php');

echo '<h1>Query IP information</h1>';

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

	echo '<table>';
	echo '<tr>';
	echo '<th>IP</th>';
	echo '<th>Tid</th>';
	echo '<th>&nbsp;</th>';
	echo '</tr>';
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
	echo 'IP: '.xhtmlInput('ip', $ip).'<br/><br/>';
	echo 'or<br/>';
	echo 'User: '.xhtmlInput('user', $ip).'<br/>';
	echo xhtmlSubmit('Search');
	echo '</form>';
}

require('design_admin_foot.php');
?>
