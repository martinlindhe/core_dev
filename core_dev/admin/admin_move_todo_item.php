<?php
/**
 * $Id$
 */

die('UNTESTED');

require_once('find_config.php');
$session->requireAdmin();

$itemId = $_GET['id'];
	
if (!empty($_POST['categoryId'])) {
	moveTodoItem($itemId, $_POST['categoryId']);
	header('Location: admin_todo_lists.php?id='.$itemId);
	die;
}

require($project.'design_head.php');

echo '<h2>Move Problem Report</h2>';

$item = getTodoItem($db, $itemId);
$PR = sprintf("PR%04d", $itemId);

echo $item['itemDesc'].'<br/><br/>';

echo nl2br($item['itemDetails']).'<br/><br/>';
echo 'Created: '.getRelativeTimeLong($item['timestamp']).', ';
if ($item['userName']) {
	echo 'by '.Users::link($item['itemCreator'], $item['userName']).'<br/>';
} else {
	echo '<b>creator has been deleted.</b><br/>';
}
echo $PR.' is currently in category <b>'.getTodoCategoryName($item['categoryId']).'</b><br/><br/>';

echo 'Move '.$PR.' to category:<br/>';

echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
echo '<select name="categoryId">';

$list = getTodoCategories(0);
foreach ($list as $row) {

	echo '<option>';			
	echo '<option value="'.$row['categoryId'].'">'.$row['categoryName'];
	$sublist = getTodoCategories($row['categoryId']);
	foreach ($sublist as $sub) {
		echo '<option value="'.$sub['categoryId'].'">';
		echo '&nbsp;&nbsp;&nbsp;';
		echo $row['categoryName'] . ' - ';
		echo $sub['categoryName'];
	}
}

echo '</select> ';
echo '<input type="submit" class="button" value="Move PR"><br/>';
echo '</form>';
	
echo '<br/>';
echo '<a href="admin_todo_lists.php?id='.$itemId.'">&raquo; Back to '.$PR.'</a><br/>';
echo '<br/>';
echo '<a href="admin_bug_reports.php">&raquo; Back to Bug Reports</a><br/>';
echo '<a href="admin_current_work.php">&raquo; Back to current work</a><br/>';

require($project.'design_foot.php');
?>
