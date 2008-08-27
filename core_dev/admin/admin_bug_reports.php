<?php
/**
 * $Id$
 */

die('UNTESTED');
//TODO: merge admin_closed_bug_reports.php into this file

require_once('find_config.php');
$session->requireAdmin();

require('design_admin_head.php');

echo '<h1>Bug reports</h1>';
echo 'Here is a list of bug reports/feature requests made by users of the game.<br/>';
echo 'The sooner these reports are dealt with the better, because users will see<br/>';
echo 'that their engagement pays off.<br/><br/>';

$list = getBugReports();
foreach ($list as $row) {
	echo '<div class="objectCritical">';
	echo getRelativeTimeLong($row['timestamp']).', by '.Users::link($row['bugCreator'], $row['userName']);
	echo ' via the '.($row['reportMethod'] ? 'game':'site').'<br/>';
	echo nl2br($row['bugDesc']).'<br/>';
	echo '<a href="admin_move_bug_report.php?id='.$row['bugId'].'">&raquo; Move this report into the TODO system</a><br/>';
	echo '<a href="admin_close_bug_report.php?id='.$row['bugId'].'">&raquo; Close this report</a>';
	echo '</div>';
	echo '<br/>';
}

$closedbugs = getClosedBugReportsCount();
echo count($list).' bugs in list (Excluding '.$closedbugs.' CLOSED bugs).<br/><br/>';
if ($closedbugs) {
	echo '<a href="admin_closed_bug_reports.php">&raquo; List CLOSED bug reports</a><br/>';
}
echo '<a href="admin_current_work.php">&raquo; Back to current work</a><br/>';

require('design_admin_foot.php');
?>
