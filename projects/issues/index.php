<?php

require_once('config.php');

require('design_head.php');

if (!$session->id) {
	$auth->showLoginForm();
} else {
	wiki('Index');
}

require('design_foot.php');
?>
