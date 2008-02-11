<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo 'Users online (was active in the last '.shortTimePeriod($session->online_timeout).')<br/><br/>';

	$list = Users::allOnline();
	foreach ($list as $row) {
		echo Users::link($row['userId'], $row['userName']).' at '.$row['timeLastLogin'].'<br/>';
	}

	require($project.'design_foot.php');
?>