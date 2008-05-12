<?php
	require_once('find_config.php');
	$session->requireAdmin();

	$itemId = $_GET['id'];
	
	if (!empty($_POST['categoryId'])) {
		moveTodoItem($db, $itemId, $_POST['categoryId']);
		header('Location: admin_todo_lists.php?id='.$itemId);
		die;
	}

	require($project.'design_head.php');

	$content = '<b>Administration screen - Move Problem Report</b><br><br>';

	$item = getTodoItem($db, $itemId);
	$PR = sprintf("PR%04d", $itemId);

	$content .= $item['itemDesc'].'<br><br>';

	$content .= nl2br($item['itemDetails']).'<br><br>';
	$content .= 'Created: '.getRelativeTimeLong($item['timestamp']).', ';
	if ($item['userName']) {
		$content .= 'by '.Users::link($item['itemCreator'], $item['userName']).'<br>';
	} else {
		$content .= '<b>creator has been deleted.</b><br>';
	}
	$content .= $PR.' is currently in category <b>'.getTodoCategoryName($db, $item['categoryId']).'</b><br><br>';
	

	$content .= 'Move '.$PR.' to category:<br>';

	$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
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

	$content .= '</select> ';
	$content .= '<input type="submit" class="button" value="Move PR"><br>';
	$content .= '</form>';
	
	$content .= '<br>';
	$content .= '<a href="admin_todo_lists.php?id='.$itemId.'">&raquo; Back to '.$PR.'</a><br>';
	$content .= '<br>';
	$content .= '<a href="admin_bug_reports.php">&raquo; Back to Bug Reports</a><br>';
	$content .= '<a href="admin_current_work.php">&raquo; Back to current work</a><br>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Move PR', $content);
		echo '</div>';

	require($project.'design_foot.php');
?>
