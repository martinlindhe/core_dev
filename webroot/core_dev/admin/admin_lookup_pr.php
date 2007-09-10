<?
	require_once('find_config.php');
	$session->requireAdmin();

	$pr = $_POST['pr'];
	$prData = getTodoItem($db, $pr);
	if ($prData) {
		header('Location: admin_todo_lists.php?id='.$prData['itemId']);
		die;
	}

	require($project.'design_head.php');

	$content = 'PR '.$pr.' not found.<br><br>';
	$content .= '<a href="admin_current_work.php">Go back to current work</a><br>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Lookup PR', $content);
		echo '</div>';

	require($project.'design_foot.php');
?>