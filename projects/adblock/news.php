<?php

require_once('config.php');

$_cat = 0;
if (!empty($_GET['cat']) && is_numeric($_GET['cat'])) $_cat = $_GET['cat'];
$meta_rss[] = array("title" => "RSS News feed #".$_cat, "name" => "news", "category" => $_cat);
require('design_head.php');

showNews();

require('design_foot.php');
?>
