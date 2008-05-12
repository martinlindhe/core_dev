<?php
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo 'Admin current work<br/><br/>';

	/* Show all categories */


	$list = getCategories(CATEGORY_TODOLIST, 0);

	foreach ($list as $row) {

		$sublist = getCategories(CATEGORY_TODOLIST, $row['categoryId']);
		if (!count($sublist)) {
			echo '<a href="admin_todo_lists.php?category='.$row['categoryId'].getProjectPath().'">'.$row['categoryName'].'</a><br/>';
			continue;
		}
		for ($j=0; $j<count($sublist); $j++) {
			echo '<a href="admin_todo_lists.php?category='.$sublist[$j]['categoryId'].'">'.$list[$i]['categoryName'] . ' - '.$sublist[$j]['categoryName'].'</a>';
		}
	}

	echo '<br/>';
	echo '<a href="admin_edit_todo_lists.php'.getProjectPath(0).'">Create/modify TODO categories</a><br/>';

	require($project.'design_foot.php');
?>
