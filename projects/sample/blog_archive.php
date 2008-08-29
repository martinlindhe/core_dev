<?php

require_once('config.php');
$session->requireLoggedIn();

if (empty($_GET['y']) || !is_numeric($_GET['y']) || empty($_GET['m']) || !is_numeric($_GET['m'])) die;

$show = $session->id;
if (isset($_GET['id']) && is_numeric($_GET['id'])) $show = $_GET['id'];

require('design_head.php');

$show_year = $_GET['y'];
$show_month = $_GET['m'];

echo 'Archive for '.$show_month.' '.$show_year.'<br/><br/>';

$list = getBlogsByMonth($show, $show_month, $show_year);
foreach($list as $row) {
	echo $row['timeCreated'].' - <a href="blog.php?Blog:'.$row['blogId'].'">'.$row['subject'].'</a><br/>';
}

if (!count($list)) {
	echo '<div class="critical">No archive for specified month.</div>';
}

require('design_foot.php');
?>
