<?php

require_once('config.php');
$session->requireLoggedOut();

require('design_head.php');

$auth->showLoginForm();

require('design_foot.php');
?>
