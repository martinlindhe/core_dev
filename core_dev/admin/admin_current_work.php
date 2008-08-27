<?php
/**
 * $Id$
 */

die('UNTESTED');

require_once('find_config.php');
$session->requireAdmin();

require('design_admin_head.php');

echo 'Admin current work<br/><br/>';

//Show all categories
$list = getCategories(CATEGORY_TODOLIST, 0);

foreach ($list as $row) {
	$sublist = getCategories(CATEGORY_TODOLIST, $row['categoryId']);
	if (!count($sublist)) {
		echo '<a href="admin_todo_lists.php?category='.$row['categoryId'].'">'.$row['categoryName'].'</a><br/>';
		continue;
	}
	for ($j=0; $j<count($sublist); $j++) {
		echo '<a href="admin_todo_lists.php?category='.$sublist[$j]['categoryId'].'">'.$list[$i]['categoryName'] . ' - '.$sublist[$j]['categoryName'].'</a>';
	}
}

echo '<br/>';
echo '<a href="admin_edit_todo_lists.php'.'">Create/modify TODO categories</a><br/>';

require('design_admin_foot.php');

?>
