<?php

require_once('config.php');
require('design_head.php');

echo xhtmlMenu($forum_menu, 'blog_menu');

$_id = 0;
if (!empty($_GET['id'])) $_id = $_GET['id'];

displayForum($_id);

require('design_foot.php');
?>
