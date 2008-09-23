<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireAdmin();

require_core('functions_statistics.php');

require('design_admin_head.php');

echo '<h1>'.t('Statistics').'</h1>';

if (!empty($_GET['y']) && !empty($_GET['m'])) {
	showStatsMonth($_GET['y'], $_GET['m']);

	echo '<br/><br/>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'">Return to overview</a>';
} else if (isset($_POST['sdate']) && isset($_POST['edate'])) {
	echo '<h2>'.t('Custom report').'</h2>';

	$sdate = strtotime($_POST['sdate']);
	$edate = strtotime($_POST['edate']);

	echo 'From '.formatTime($sdate).'<br/>';
	echo 'To '.formatTime($edate).'<br/>';
	echo '<br/>';

	$logins_tot = getLoginCnt($sdate, $edate, false);
	$logins_uniq = getLoginCnt($sdate, $edate, true);
	echo 'Total logins: '.$logins_tot.'<br/>';
	echo 'Unique logins: '.$logins_uniq.'<br/>';
	echo 'Average: '.round($logins_tot / $logins_uniq, 2).' logins per user<br/>';

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

			echo '<a href="'.$_SERVER['PHP_SELF'].'?m='.$m.'&y='.$yr.'">'.date('F', $curr).', '.$yr.'</a><br/>';
		}
		echo '<br/>';
	}

	echo '<h2>'.t('Custom report').'</h2>';
	echo xhtmlForm();
	echo 'From date: '.xhtmlInput('sdate', sql_datetime($start_date)).'<br/>';
	echo 'To date: '.xhtmlInput('edate', 'now').'<br/>';
	echo xhtmlSubmit('Show');
	echo xhtmlFormClose();
}

require('design_admin_foot.php');
?>
