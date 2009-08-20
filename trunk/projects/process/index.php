<?php

require_once('config.php');

require('design_head.php');

if (!$h->session->id) {
	showLoginForm();
}

//FIXME re-implement SOAP interface
if (!defined('SOAP_1_2')) {
	echo '<div class="critical">php_soap extension is not loaded! This application will not function properly</div>';
}

require('design_foot.php');
?>
