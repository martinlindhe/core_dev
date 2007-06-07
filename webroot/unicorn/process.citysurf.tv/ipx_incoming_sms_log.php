<?
	require_once('config.php');
	$session->requireLoggedIn();

	require_once('design_head.php');

	echo '<h1>IPX incoming SMS log</h1>';

	ipxIncomingLog();
	
	require_once('design_foot.php');
?>