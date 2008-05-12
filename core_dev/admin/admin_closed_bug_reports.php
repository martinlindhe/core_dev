<?php
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	$content = '<b>Administration screen - Closed bug reports</b><br>';
	$content .= '<b>OBSERVE: THESE BUG REPORTS ARE CLOSED!</b><br><br>';
	
	$list = getClosedBugReports($db);
	for ($i=0; $i<count($list); $i++) {
		$content .= getRelativeTimeLong($list[$i]['timestamp']).', by '.Users::link($list[$i]['bugCreator'], $list[$i]['userName']);

		$content .= ' via the '.($list[$i]['reportMethod'] ? 'game':'site');
		$content .= ' (Closed because: <b>'.$close_bug_reason[$list[$i]['bugClosedReason']].'</b>)<br>';
		$content .= nl2br($list[$i]['bugDesc']).'<br><br>';
	}
	
	$content .= count($list).' CLOSED bugs in list.<br><br>';
	
	$content .= '<a href="admin_bug_reports.php">&raquo; Back to Bug Reports</a><br>';
	$content .= '<a href="admin_current_work.php">&raquo; Back to current work</a><br>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Closed bug reports', $content);
		echo '</div>';

	require($project.'design_foot.php');
?>
