<?php
/**
 * $Id$
 */

die('UNTESTED');

require_once('find_config.php');
$session->requireAdmin();

require($project.'design_head.php');

echo '<h2>Your assigned tasks</h2>';
echo 'Here is all your currently assigned tasks, please update task progress in the Development Log<br/>';
echo 'for each task, so other developers can see how things progress.<br/><br/>';

if (isset($_GET['closed'])) {
	echo '<b>OBSERVE: THIS IS YOUR CLOSED TASKS!</b><br/><br/>';
		
	$list = getClosedAssignedTasks($session->id);
	foreach ($list as $row) {
		echo sprintf('PR%04d: ', $row['itemId']);
		echo '<a href="admin_todo_lists.php?id='.$row['itemId'].'">'.$row['itemDesc'].'</a> ('.getTodoCategoryName($row['categoryId']).')<br/>';
	}

	echo '<br/>';
	echo 'You have '.count($list).' CLOSED assigned tasks.<br/><br/>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'">&raquo; Show your UNCLOSED assigned tasks</a><br/>';
	echo '<a href="admin_current_work.php">&raquo; Back to current work</a><br/>';
} else {
	$list = getAssignedTasks($session->id);
	foreach ($list as $row) {
		echo sprintf('PR%04d: ', $row['itemId']);
		echo '<a href="admin_todo_lists.php?id='.$row['itemId'].'">'.$row['itemDesc'].'</a> ('. getTodoCategoryName($row['categoryId']).')<br/>';
	}
	
	echo '<br/>';
	$closedtasks = getClosedAssignedTasksCount($session->id);
	echo '<b>You have '.count($list).' assigned tasks</b> (excluding '.$closedtasks.' CLOSED tasks).<br/><br/>';
	if ($closedtasks) {
		echo '<a href="'.$_SERVER['PHP_SELF'].'?closed">&raquo; Show your CLOSED assigned tasks</a><br/>';
	}
	echo '<a href="admin_current_work.php">&raquo; Back to current work</a><br/>';
}

require($project.'design_foot.php');
?>
