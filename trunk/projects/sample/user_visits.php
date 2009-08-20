<?php

require_once('config.php');
$session->requireLoggedIn();

require('design_head.php');

createMenu($profile_menu, 'blog_menu');

echo 'These people have visited your page:<br/><br/>';

$list = getVisits(VISIT_USERPAGE, $session->id);

foreach ($list as $row) {
	echo Users::link($row['creatorId'], $row['creatorName']).' at '.$row['timeCreated'].'<br/>';
}

require('design_foot.php');
?>
