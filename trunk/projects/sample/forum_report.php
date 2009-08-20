<?php

require_once('config.php');
$session->requireLoggedIn();

if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;	//invalid request
$itemId = $_GET['id'];

require('design_head.php');

wiki('Forum abuse reporting');

reportForumPost($itemId);

echo '<a href="javascript:history.go(-1);">Return</a>';

require('design_foot.php');
?>
