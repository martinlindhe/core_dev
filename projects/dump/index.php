<?php

require_once('config.php');

require('design_head.php');

if (!$session->id) {
	$session->showLoginForm();
}

require('design_foot.php');
?>
