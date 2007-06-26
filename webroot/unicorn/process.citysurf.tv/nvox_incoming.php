<?
	if (empty($_GET['id']) || empty($_GET['d']) || empty($_GET['l'])) die('no input');
	require_once('config.php');
	
	$_id = $_GET['id'];
	$_days = $_GET['d'];
	$_level = $_GET['l'];

	/*if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ip)) {
		$session->log('nvox_incoming.php accessed by unlisted IP', LOGLEVEL_ERROR);
		die('ip not allowed');
	}*/

	nvoxHandleIncoming($_id, $_days, $_level);
?>