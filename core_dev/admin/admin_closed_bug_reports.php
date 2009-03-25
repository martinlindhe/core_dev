<?php
/**
 * $Id$
 */

die('UNTESTED');

require_once('find_config.php');
$h->session->requireAdmin();

require('design_admin_head.php');

echo '<h1>Closed bug reports</h1>';
echo '<b>OBSERVE: THESE BUG REPORTS ARE CLOSED!</b><br/><br/>';

$list = getClosedBugReports();
for ($i=0; $i<count($list); $i++) {	//FIXME: foreach
	echo getRelativeTimeLong($list[$i]['timestamp']).', by '.Users::link($list[$i]['bugCreator'], $list[$i]['userName']);

	echo ' via the '.($list[$i]['reportMethod'] ? 'game':'site');
	echo ' (Closed because: <b>'.$close_bug_reason[$list[$i]['bugClosedReason']].'</b>)<br/>';
	echo nl2br($list[$i]['bugDesc']).'<br/><br/>';
}

echo count($list).' CLOSED bugs in list.<br/><br/>';

echo '<a href="admin_bug_reports.php">&raquo; Back to Bug Reports</a><br/>';
echo '<a href="admin_current_work.php">&raquo; Back to current work</a><br/>';

require('design_admin_foot.php');
?>
