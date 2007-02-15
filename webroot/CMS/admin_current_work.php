<?
	include_once('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

	/* Show all categories */
	$content = getInfoField($db, 'page_current_work');


	$list = getTodoCategories($db, 0);

	for ($i=0; $i<count($list); $i++) {

		$sublist = getTodoCategories($db, $list[$i]['categoryId']);
		if (!count($sublist)) {
			$cnt = getTodoCategoryItemsCount($db, $list[$i]['categoryId']);
			$content .= '<a href="admin_todo_lists.php?category='.$list[$i]['categoryId'].'"';
			if ($cnt) $content .= ' class="bold_link"';
			$content .= '>'.$list[$i]['categoryName'].'</a>';
			$content .= ' ('.$cnt.' items)<br>';
			continue;
		}
		for ($j=0; $j<count($sublist); $j++) {
			$cnt = getTodoCategoryItemsCount($db, $sublist[$j]['categoryId']);

			$content .= '<a href="admin_todo_lists.php?category='.$sublist[$j]['categoryId'].'"';
			if ($cnt) $content .= ' class="bold_link"';
			$content .= '>'.$list[$i]['categoryName'] . ' - '.$sublist[$j]['categoryName'].'</a>';
			$content .= ' ('.$cnt.' items)<br>';
		}
	}
	$todos = getTodoItemsCount($db);
	$content .= '<b>'.$todos.' unclosed todo items found</b>';
	$content .= '<br><br>';



	$cntbugs = getBugReportsCount($db);
	if ($cntbugs) $content .= '<b>';
	$content .= '<a href="admin_bug_reports.php">Show bug reports</a> ('.$cntbugs.' open bug report)';
	if ($cntbugs) $content .= '</b>';
	$content .= '<br><br>';

	$cntassigned = getAssignedTasksCount($db, $_SESSION['userId']);
	if ($cntassigned) $content .= '<b>';
	$content .= '<a href="admin_assigned_tasks.php">Show your tasks</a> ('.$cntassigned.' assigned tasks)';
	if ($cntassigned) $content .= '</b>';
	$content .= '<br><br>';

	$content .= '<form name="lookuppr" method="post" action="admin_lookup_pr.php">';
	$content .= 'Lookup PR: <input type="text" name="pr" size=6> <input type="submit" class="button" value="Go">';
	$content .= '</form>';

	$content .= '<br>';
	$content .= '<a href="admin_edit_todo_lists.php">Create/modify TODO categories</a><br>';
	$content .= '<a href="admin_notes.php">Edit shared admin notes</a>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Current work', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>