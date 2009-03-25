<?php
/**
 * $Id$
 */

die('UNTESTED');

require_once('find_config.php');
$h->session->requireAdmin();

$bugId = $_GET['id'];

if (isset($_POST['desc'])) {
	$pr = moveBugReport($bugId, $_POST['creator'], $_POST['desc'], $_POST['details'], $_POST['timestamp'], $_POST['itemCategory'], $_POST['categoryId']);

	include('design_head.php');
	echo 'The bug report has been successfully moved into the todo list system!<br/>';
	echo '<a href="admin_todo_lists.php?id='.$pr.'">&raquo; Click here to go to the PR.</a><br/>';
	include('design_foot.php');
	die;
}

$item = getBugReport($bugId);
if (!$item) {
	header('Location: admin_bug_reports.php');
	die;
}

require('design_admin_head.php');

echo '<h1>Move bug report</h1>';

echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$bugId.'">';
echo getRelativeTimeLong($item['timestamp']).', by '.Users::link($item['bugCreator'], $item['userName']).'<br/>';
echo '<input name="timestamp" type="hidden" value="'.$item['timestamp'].'">';
echo '<input name="creator" type="hidden" value="'.$item['bugCreator'].'">';
echo 'Description: <input size=40 type="text" name="desc"><br/>';
echo '<textarea name="details" cols=60 rows=8>'.$item['bugDesc'].'</textarea><br/>';

echo 'Category: ';
echo '<select name="itemCategory">';
echo '<option>';
for ($i=0; $i<count($todo_item_category); $i++) {
	echo '<option value="'.$i.'">'.$todo_item_category[$i];
}
echo '</select><br/>';

echo 'Add to TODO-list: ';

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
echo '</select><br/>';

echo '<input type="submit" class="button" value="Move bug"><br/>';
echo '</form>';

echo '<a href="admin_bug_reports.php">&raquo; Back to Bug Reports</a><br/>';
echo '<a href="admin_current_work.php">&raquo; Back to current work</a><br/>';

require('design_admin_foot.php');
?>
