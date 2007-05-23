<?
	require_once('config.php');

	require_once('design_head.php');
?>	
	<h1>Process server</h1>
	
	<a href="client.php">Simulate client adding work order</a><br/>
	<br/>
	<a href="ipx_client.php">Send a test SMS with IPX</a><br/>
<?
	$current_php_soap = phpversion('soap');
	if (!defined('SOAP_1_2')) {
		echo '<div class="critical">php_soap extension is not loaded! This application will not function properly</div>';
	}

	require_once('design_foot.php');
?>