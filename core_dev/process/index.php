<?
	require_once('config.php');

	require('design_head.php');

	wiki('ProcessHome');
	echo '<br/>';

	if (!$session->id) {
		$auth->showLoginForm();
	}

	/*
	//FIXME re-implement SOAP interface	
	$current_php_soap = phpversion('soap');
	if (!defined('SOAP_1_2')) {
		echo '<div class="critical">php_soap extension is not loaded! This application will not function properly</div>';
	}*/

	require('design_foot.php');
?>