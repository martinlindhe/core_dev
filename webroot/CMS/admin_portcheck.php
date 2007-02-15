<?
	//fixme: använd mer stylesheets

	include_once('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	$error = '';
	$ip = '';
	$port = 0;
	if (!empty($_GET['p'])) $port = strip_tags($_GET['p']);
	if (!empty($_POST['p'])) $port = strip_tags($_POST['p']);

	$geoip = IPv4_to_GeoIP($_SERVER['REMOTE_ADDR']);
	$ip = $_SERVER['REMOTE_ADDR'];
	if ($_SESSION['isSuperAdmin']) {
		if (!empty($_GET['i'])) $ip = strip_tags($_GET['i']);
		if (!empty($_POST['i'])) $ip = strip_tags($_POST['i']);
		$geoip = IPv4_to_GeoIP($ip);
	}

	if (($_SESSION['isSuperAdmin'] && !($geoip == IPv4_to_GeoIP('127.0.0.1') || $geoip == IPv4_to_GeoIP('192.168.0.1'))) && IgnoredIPRange($geoip))
	{
		$error = '<span class="msg_error">Error:</span> '.$ip.' is an invalid IP!';
	}
	
	$timeout = 2;
	if ($_SESSION['isSuperAdmin']) $timeout = 5;

	include('design_head.php');
	include('design_user_head.php');
	
		$content = '';

		$content .= getInfoField($db, 'page_portcheck').'<br><br>';
	
		if ($port) $content .= '<b>Checking  '.$ip.':'.$port.'</b> ... ('.$timeout.' seconds timeout)<br>';
		if ($error) $content .= $error.'<br>';
		if (!is_numeric($port) || !intval($port) || ($port < 0) || ($port > 65535)) {
			if ($port) $content .= '<span class="msg_error">Error:</span> The port '.$port.' is invalid. Please specify a number between 1 and 65535.<br>';
		}
		$content .= '<br>';

		if ($port && !$error) {
			$fp = @fsockopen($ip, $port, $errno, $errstr, 2);
			if (!$fp) {
				$content .= '<span class="msg_error">Error:</span> ';
				if ($errno == 10060) {
					$content .= $ip.':'.$port.' '.getGeoIPCountryFlag($db, $geoip).' appears to be closed.<br>';
				} else {
					$content .= $errstr.' ('.$errno.')<br>';
				}
			} else {
				$content .= '<span class="msg_success">Success:</span> '.$ip.':'.$port.' '.getGeoIPCountryFlag($db, $geoip).' is open!<br>';
				fclose($fp);
			}
			$content .= '<br>';
		}
		$content .= '<i style=background-color:#B9B9B9>Your IP is '.$_SERVER['REMOTE_ADDR'].'</i> '.getGeoIPCountryFlag($db, $_SERVER['REMOTE_ADDR']).'<br>';
		if ($_SESSION['isSuperAdmin']) {
			$content .= '<i style=background-color:#B9B9B9>Server IP is '.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].'</i> '.getGeoIPCountryFlag($db, $_SERVER['SERVER_ADDR']).'<br>';
		}
		$content .= '<br>';
	
		$content .= '<table width=200 cellpadding=0 cellspacing=0 border=0>';
		$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
		$content .= '<tr><td>IP:</td><td>';
		if ($_SESSION['isSuperAdmin']) {
			$content .= '<input type="text" value="'.$ip.'" name="i">';
		}
		$content .= '</td></tr><tr><td>Port:</td><td>';
		if ($port == 0) $port = 80; //default port
		$content .= '<input type="text" value="'.$port.'" name="p" size=5>';
		$content .= '</td></tr>';
		$content .= '</table><br>';
		
		$content .= '<input type="submit" class="button" value="Check">';
		$content .= '</form>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Portcheck', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>