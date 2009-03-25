<?php
/**
 * $Id$
 */

require_once('find_config.php');
$h->session->requireSuperAdmin();

$error = '';
$port = 0;

if (!empty($_GET['p'])) $port = strip_tags($_GET['p']);
if (!empty($_POST['p'])) $port = strip_tags($_POST['p']);

$ip = $_SERVER['REMOTE_ADDR'];
if (!empty($_GET['i'])) $ip = strip_tags($_GET['i']);
if (!empty($_POST['i'])) $ip = strip_tags($_POST['i']);
$geoip = IPv4_to_GeoIP($ip);

if (reservedIPv4($geoip)) {
	//$error = '<span class="msg_error">Error:</span> '.$ip.' is an invalid IP!';
}

$timeout = 5;

require('design_admin_head.php');

echo '<h1>Port checker</h1>';

if ($port) echo '<b>Checking  '.$ip.':'.$port.'</b> ... ('.$timeout.' seconds timeout)<br/>';
if ($error) echo $error.'<br/>';
if (!is_numeric($port) || !intval($port) || ($port < 0) || ($port > 65535)) {
	if ($port) echo '<span class="msg_error">Error:</span> The port '.$port.' is invalid. Please specify a number between 1 and 65535.<br/>';
}
echo '<br/>';

if ($port && !$error) {
	$fp = @fsockopen($ip, $port, $errno, $errstr, 2);
	if (!$fp) {
		echo '<div class="critical">Error: ';
		if ($errno == 10060) {
			echo $ip.':'.$port.' appears to be closed.<br/>';
		} else {
			echo $errstr.' ('.$errno.')<br/>';
		}
		echo '</div>';
	} else {
		echo '<div class="okay">Success: '.$ip.':'.$port.' is open!</div>';
		fclose($fp);
	}
	echo '<br/>';
}
echo '<div class="item">';
	echo 'Your IP is '.$_SERVER['REMOTE_ADDR'].'<br/>';
	echo 'Server IP is '.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'];
echo '</div><br/>';

echo '<form method="post" action="">';
echo 'IP: ';
echo xhtmlInput('i', $ip).'<br/>';
echo 'Port: ';
if ($port == 0) $port = 80; //default port
echo xhtmlInput('p', $port, 5).'<br/>';
echo xhtmlSubmit('Test');
echo '</form>';

require('design_admin_foot.php');
?>
