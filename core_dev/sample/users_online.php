<?
	require_once('config.php');

	require('design_head.php');

	createMenu($user_menu, 'blog_menu');

	echo 'Users online (was active in the last '.shortTimePeriod($session->online_timeout).')<br/><br/>';

	$list = getUsersOnline();
	foreach ($list as $row) {
		echo nameLink($row['userId'], $row['userName']).' at '.$row['timeLastLogin'].'<br/>';
	}

	require('design_foot.php');
?>