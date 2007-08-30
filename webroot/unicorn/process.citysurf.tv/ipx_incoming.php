<?
	/*
		This script is called by IPX for incoming SMS
	*/

	require_once('config.php');


	if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ip)) {
		$session->log('ipx_incoming.php accessed by unlisted IP', LOGLEVEL_ERROR);
		die('ip not allowed');
	}

	ipxHandleIncoming();
?>