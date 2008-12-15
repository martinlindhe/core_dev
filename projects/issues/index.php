<?php

require_once('config.php');

require('design_head.php');

if (!$session->id) {
	echo 'You need to log in to continue.<br/><br/>';
	$session->auth->showLoginForm();
} else {
	echo 'You are logged in';
}

require('design_foot.php');
?>
