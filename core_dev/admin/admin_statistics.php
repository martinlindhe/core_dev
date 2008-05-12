<?php
	require_once('find_config.php');
	$session->requireAdmin();

	require_once('../core/functions_statistics.php');

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo '<h1>Statistics</h1>';


	if (!empty($_GET['y']) && !empty($_GET['m'])) {
		showStatsMonth($_GET['y'], $_GET['m']);

		echo '<br/><br/><a href="'.$_SERVER['PHP_SELF'].getProjectPath(0).'">Return to overview</a>';

	} else {
		//list overview of all months from oldest entry in db until today

		$start_date = strtotime(getOldestLoginTime());
		$end_date = time();

		$start_year = date('Y', $start_date);
		$end_year = date('Y', $end_date);

		$start_month = date('n', $start_date);
		$end_month = date('n', $end_date);

		for ($yr = $start_year; $yr <= $end_year; $yr++) {
			echo '<h2>'.$yr.'</h2>';

			for ($m = ($yr == $start_year ? $start_month : 1); $m <= ($yr == $end_year ? $end_month : 12); $m++) {
				$curr = mktime(0, 0, 0, $m, 1, $yr);

				echo '<a href="'.$_SERVER['PHP_SELF'].'?m='.$m.'&y='.$yr.getProjectPath().'">'.date('F', $curr).', '.$yr.'</a><br/>';
			}
			echo '<br/>';
		}
	}

	require($project.'design_foot.php');
?>
