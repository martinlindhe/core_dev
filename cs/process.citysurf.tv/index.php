<?
	require_once('config.php');

	require_once('design_head.php');


	echo '<h1>Process server</h1>';
	
	if ($session->id) {
		echo '<a href="client.php">Simulate client adding work order</a><br/>';
		echo '<br/>';
		echo '<a href="ipx_client.php">Send a test SMS with IPX</a><br/>';
		echo '<br/>';
		echo '<a href="ipx_incoming_sms_log.php">Show incoming SMS</a><br/>';
		echo '<br/>';
		echo '<a href="ipx_outgoing_sms_log.php">Show outgoing SMS</a>';
	}

	$current_php_soap = phpversion('soap');
	if (!defined('SOAP_1_2')) {
		echo '<div class="critical">php_soap extension is not loaded! This application will not function properly</div>';
	}

	require_once('design_foot.php');
?>