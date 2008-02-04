<?
	require_once('find_config.php');
	$session->requireAdmin();

	$bugId = $_GET['id'];
	
	if (isset($_POST['desc'])) {
			
		$pr = moveBugReport($db, $_SESSION['userId'], $bugId, $_POST['creator'], $_POST['desc'], $_POST['details'], $_POST['timestamp'], $_POST['itemCategory'], $_POST['categoryId']);

		include('design_head.php');
		echo 'The bug report has been successfully moved into the todo list system!<br>';
		echo '<a href="admin_todo_lists.php?id='.$pr.'">&raquo; Click here to go to the PR.</a><br>';
		include('design_foot.php');
		die;
	}

	$item = getBugReport($db, $bugId);
	if (!$item) {
		header('Location: admin_bug_reports.php');
		die;
	}

	require($project.'design_head.php');	

	$content = '<b>Administration screen - Move bug report</b><br><br>';

	$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$bugId.'">';
	$content .= getRelativeTimeLong($item['timestamp']).', by '.Users::link($item['bugCreator'], $item['userName']).'<br>';
	$content .= '<input name="timestamp" type="hidden" value="'.$item['timestamp'].'">';
	$content .= '<input name="creator" type="hidden" value="'.$item['bugCreator'].'">';
	$content .= 'Description: <input size=40 type="text" name="desc"><br>';
	$content .= '<textarea name="details" cols=60 rows=8>'.$item['bugDesc'].'</textarea><br>';
	
	$content .= 'Category: ';
	$content .= '<select name="itemCategory">';
		$content .= '<option>';
		for ($i=0; $i<count($todo_item_category); $i++) {
			$content .= '<option value="'.$i.'">'.$todo_item_category[$i];
		}
	$content .= '</select><br>';

	$content .= 'Add to TODO-list: ';

	$content .= '<select name="categoryId">';
	$list = getTodoCategories($db, 0);
	for ($i=0; $i<count($list); $i++) {
		$content .= '<option>';			
		$content .= '<option value="'.$list[$i]['categoryId'].'">'.$list[$i]['categoryName'];
		$sublist = getTodoCategories($db, $list[$i]['categoryId']);
		for ($j=0; $j<count($sublist); $j++) {
			$content .= '<option value="'.$sublist[$j]['categoryId'].'">';
			$content .= '&nbsp;&nbsp;&nbsp;';
			$content .= $list[$i]['categoryName'] . ' - ';
			$content .= $sublist[$j]['categoryName'];
		}
	}
	$content .= '</select><br>';

	$content .= '<input type="submit" class="button" value="Move bug"><br>';
	$content .= '</form>';
	
	$content .= '<a href="admin_bug_reports.php">&raquo; Back to Bug Reports</a><br>';
	$content .= '<a href="admin_current_work.php">&raquo; Back to current work</a><br>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Move bug report', $content);
		echo '</div>';

	require($project.'design_foot.php');
?>
