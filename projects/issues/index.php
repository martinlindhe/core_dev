<?php

require_once('config.php');

require('design_head.php');

if (!$h->session->id) {
	echo 'You need to log in to continue.<br/><br/>';
	showLoginForm();
} else {
	echo 'You are logged in.';
}

require('design_foot.php');
?>
