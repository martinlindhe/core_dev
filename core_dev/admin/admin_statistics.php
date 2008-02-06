<?
	require_once('find_config.php');
	$session->requireAdmin();

	require_once('../core/functions_statistics.php');

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo '<h1>Statistics</h1>';

	/*for ($m = 1; $m <= date('n'); $m++) {
		echo 'Month '.$m.' 2007:<br/>';
		//generateStatsMonth(2007, $m);
		showStatsMonth(2007, $m);
	}*/
	
	$yr = date('Y');
	$mn = date('n');

	showStatsMonth($yr, $mn);

	require($project.'design_foot.php');
?>