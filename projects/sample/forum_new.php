<?php

require_once('config.php');
$session->requireLoggedIn();

$itemId = 0;
if (!empty($_GET['id']) && is_numeric($_GET['id'])) $itemId = $_GET['id'];
if (!$itemId && !$session->isAdmin) die;	//invalid request

require('design_head.php');

echo xhtmlMenu($forum_menu, 'blog_menu');

createForumCategory($itemId);

echo '<a href="javascript:history.go(-1);">Go back</a>';

require('design_foot.php');

?>
