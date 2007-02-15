<?
	//parameters:
	//id = numeric track ID
	//l = document.location
	//r = document.referrer

	//ssi = if present, the following parameters are also recognized
	//ip = client IP
	//ua = client user agent string (browser info)

	if (empty($_GET['i']) || !is_numeric($_GET['i']) || empty($_GET['l']) || !isset($_GET['r'])) die;

	$ip = $_SERVER['REMOTE_ADDR'];
	$ua = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

	if (isset($_GET['ssi'])) {
		if (!isset($_GET['ip']) || !isset($_GET['ua'])) die;

		$ip = $_GET['ip'];
		$ua = $_GET['ua'];
	}

	include('include_all.php');

	trackVisitor($db, $_GET['i'], $_GET['l'], $_GET['r'], $ip, $ua);
?>