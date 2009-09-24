<?php

require_once('config.php');
require('design_head.php');

echo xhtmlMenu($user_menu, 'blog_menu');

echo 'Here is the 50 last users logged in<br/><br/>';

$list = Users::lastLoggedIn(50);
foreach ($list as $row) {
	echo Users::link($row['userId'], $row['userName']).' at '.$row['timeLastLogin'].'<br/>';
}

require('design_foot.php');
?>
