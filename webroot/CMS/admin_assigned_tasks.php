<?
	include_once('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}
	
	include('design_head.php');
	include('design_user_head.php');

	$content = '<b>Administration screen - Your assigned tasks</b><br>';
	$content .= 'Here is all your currently assigned tasks, please update task progress in the Development Log<br>';
	$content .= 'for each task, so other developers can see how things progress.<br><br>';

	if (isset($_GET['closed'])) {
		$content .= '<b>OBSERVE: THIS IS YOUR CLOSED TASKS!</b><br><br>';
		
		$list = getClosedAssignedTasks($db, $_SESSION['userId']);
		for ($i=0; $i<count($list); $i++) {
			$content .= sprintf('PR%04d: ', $list[$i]['itemId']);
			$content .= '<a href="admin_todo_lists.php?id='.$list[$i]['itemId'].'">'.$list[$i]['itemDesc'].'</a> ('.getTodoCategoryName($db, $list[$i]['categoryId']).')<br>';
		}
	
		$content .= '<br>';
		$content .= 'You have '.count($list).' CLOSED assigned tasks.<br><br>';
		$content .= '<a href="'.$_SERVER['PHP_SELF'].'">&raquo; Show your UNCLOSED assigned tasks</a><br>';
		$content .= '<a href="admin_current_work.php">&raquo; Back to current work</a><br>';
		
	} else {
		$list = getAssignedTasks($db, $_SESSION['userId']);
		for ($i=0; $i<count($list); $i++) {
			$content .= sprintf('PR%04d: ', $list[$i]['itemId']);
			$content .= '<a href="admin_todo_lists.php?id='.$list[$i]['itemId'].'">'.$list[$i]['itemDesc'].'</a> ('. getTodoCategoryName($db, $list[$i]['categoryId']).')<br>';
		}
	
		$content .= '<br>';
		$closedtasks = getClosedAssignedTasksCount($db, $_SESSION['userId']);
		$content .= '<b>You have '.count($list).' assigned tasks</b> (excluding '.$closedtasks.' CLOSED tasks).<br><br>';
		if ($closedtasks) {
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?closed">&raquo; Show your CLOSED assigned tasks</a><br>';
		}
		$content .= '<a href="admin_current_work.php">&raquo; Back to current work</a><br>';
	}

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Assigned tasks', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>
