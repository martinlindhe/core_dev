<?
	require_once('config.php');
	$session->requireLoggedIn();

	require_once('design_head.php');

	echo '<h1>IPX outgoing SMS log</h1>';
	
	ipxOutgoingLog();
	
	require_once('design_foot.php');
?>