<?
	require_once('config.php');

	require_once('design_head.php');
?>	
	process server...
<?
	$current_php_soap = phpversion('soap');
	if (!defined('SOAP_1_2')) {
		echo '<div class="critical">php_soap extension is not loaded! This application will not function properly</div>';
	}

	require_once('design_foot.php');
?>