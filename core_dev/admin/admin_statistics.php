<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireAdmin();

require_once('../core/functions_statistics.php');

require($project.'design_head.php');

echo createMenu($admin_menu, 'blog_menu');

echo '<h1>'.t('Statistics').'</h1><br/>';

if (!empty($_GET['y']) && !empty($_GET['m'])) {
	showStatsMonth($_GET['y'], $_GET['m']);

	echo '<br/><br/>';
	echo '<a href="'.$_SERVER['PHP_SELF'].getProjectPath(0).'">Return to overview</a>';
} else if (isset($_POST['sdate']) && isset($_POST['edate'])) {
	echo '<h2>'.t('Custom report').'</h2>';

	$sdate = strtotime($_POST['sdate']);
	$edate = strtotime($_POST['edate']);

	echo 'From '.formatTime($sdate).'<br/>';
	echo 'To '.formatTime($edate).'<br/>';
	echo '<br/>';

	echo 'Total logins: '. getLoginCnt($sdate, $edate, false).'<br/>';
	echo 'Unique logins: '. getLoginCnt($sdate, $edate, true).'<br/>';

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

	echo '<h2>'.t('Custom report').'</h2>';
	echo '<form method="post" action="">';
	echo 'From date: '.xhtmlInput('sdate', '2008-04-01 00:00').'<br/>';
	echo 'To date: '.xhtmlInput('edate', 'now').'<br/>';
	echo xhtmlSubmit('Show');
	echo '</form>';
}

require($project.'design_foot.php');
?>
