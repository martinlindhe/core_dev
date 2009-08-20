<?php

require_once('config.php');
$session->requireLoggedIn();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die;
$itemId = $_GET['id'];

require('design_head.php');

shareForumItem($itemId);

echo '<a href="forum.php?id='.$itemId.'#'.$itemId.'">Return</a>';

require('design_foot.php');
?>
