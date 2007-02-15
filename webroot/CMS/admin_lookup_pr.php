<?
	include_once('include_all.php');

	if (!$_SESSION['isSuperAdmin'] || empty($_POST['pr'])) {
		header('Location: '.$config['start_page']);
		die;
	}

	$pr = $_POST['pr'];
	$prData = getTodoItem($db, $pr);
	if ($prData) {
		header('Location: admin_todo_lists.php?id='.$prData['itemId']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');

	$content = 'PR '.$pr.' not found.<br><br>';
	$content .= '<a href="admin_current_work.php">Go back to current work</a><br>';

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Lookup PR', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>