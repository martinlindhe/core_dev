<?php

require_once('config.php');

require('design_head.php');

if (!$session->id) {
	$auth->showLoginForm();
} else {
	echo 'You are logged in';
}

require('design_foot.php');
?>
