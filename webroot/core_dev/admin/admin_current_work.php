<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo 'Admin current work<br/><br/>';

	/* Show all categories */
	$content = '';


	$list = getCategories(CATEGORY_TODOLIST, 0);

	//for ($i=0; $i<count($list); $i++) {
	foreach ($list as $row) {

		$sublist = getCategories(CATEGORY_TODOLIST, $row['categoryId']);
		if (!count($sublist)) {
			//$cnt = getTodoCategoryItemsCount($db, $list[$i]['categoryId']);
			$content .= '<a href="admin_todo_lists.php?category='.$row['categoryId'].'"';
			//if ($cnt) $content .= ' class="bold_link"';
			$content .= '>'.$list[$i]['categoryName'].'</a>';
			//$content .= ' ('.$cnt.' items)<br>';
			continue;
		}
		for ($j=0; $j<count($sublist); $j++) {
			//$cnt = getTodoCategoryItemsCount($db, $sublist[$j]['categoryId']);

			$content .= '<a href="admin_todo_lists.php?category='.$sublist[$j]['categoryId'].'"';
			//if ($cnt) $content .= ' class="bold_link"';
			$content .= '>'.$list[$i]['categoryName'] . ' - '.$sublist[$j]['categoryName'].'</a>';
			//$content .= ' ('.$cnt.' items)<br>';
		}
	}
	//$todos = getTodoItemsCount($db);
	//$content .= '<b>'.$todos.' unclosed todo items found</b>';
	$content .= '<br><br>';


/*
	$cntassigned = getAssignedTasksCount($db, $_SESSION['userId']);
	if ($cntassigned) $content .= '<b>';
	$content .= '<a href="admin_assigned_tasks.php">Show your tasks</a> ('.$cntassigned.' assigned tasks)';
	if ($cntassigned) $content .= '</b>';
	$content .= '<br><br>';
*/
	$content .= '<form name="lookuppr" method="post" action="admin_lookup_pr.php">';
	$content .= 'Lookup PR: <input type="text" name="pr" size=6> <input type="submit" class="button" value="Go">';
	$content .= '</form>';

	$content .= '<br>';
	$content .= '<a href="admin_edit_todo_lists.php">Create/modify TODO categories</a><br>';

	echo $content;

	require($project.'design_foot.php');
?>