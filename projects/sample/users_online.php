<?php

require_once('config.php');
require('design_head.php');

createMenu($user_menu, 'blog_menu');

echo 'Users online (was active in the last '.shortTimePeriod($session->online_timeout).')<br/><br/>';

$list = Users::allOnline();
foreach ($list as $row) {
	echo Users::link($row['userId'], $row['userName']).' at '.$row['timeLastActive'].'<br/>';
}

require('design_foot.php');
?>
