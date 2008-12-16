<?php

require_once('config.php');

require('design_head.php');

if (!$h->sess->id) {
	echo 'You need to log in to continue.<br/><br/>';
	$h->auth->showLoginForm();
} else {
	echo 'You are logged in';
}

require('design_foot.php');
?>
