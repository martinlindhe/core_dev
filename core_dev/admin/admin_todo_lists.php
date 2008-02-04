<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	echo 'Admin todo lists<br/><br/>';

	echo '<a href="admin_current_work.php'.getProjectPath(0).'">Show your current work</a>';

	if (empty($_GET['id']) || !is_numeric($_GET['id'])) die('give id');

	/* Show a specific item */
	$itemId = $_GET['id'];
		
	$item = getTodoItem($itemId);
	if (isset($_POST['changestatus'])) {
		/* Change item status */
		setTodoItemStatus($itemId, $_POST['changestatus']);

		if ($_POST['changestatus'] == TODO_ITEM_ASSIGNED) {
			assignTodoItem($itemId, $_SESSION['userId']);
			$comment ='Status changed from '.$todo_item_status[$item['itemStatus'] ].' to '.$todo_item_status[ $_POST['changestatus']].' by '.$session->username.'.<br>';
			$comment.='(Meaning item is now assigned to '.$_SESSION['userName'].').';
			$item['assignedTo'] = $_SESSION['userId']; //update changes
		} else {
			$comment='Status changed from '.$todo_item_status[$item['itemStatus'] ].' to '.$todo_item_status[ $_POST['changestatus']].' by '.$session->username.'.';
		}
		$item['itemStatus'] = $_POST['changestatus']; //update changes			
		addComment(CATEGORY_TODOLIST, $itemId, $comment);

	} else if (isset($_POST['assignto'])) {
		/* Assign item to a developer */
		assignTodoItem($itemId, $_POST['assignto']);
		$item['itemStatus'] = TODO_ITEM_ASSIGNED; //update changes
		$item['assignedTo'] = $_POST['assignto']; //update changes
			
		$comment = $session->username.' assigned the task to '.Users::getName($_POST['assignto']).'.';
		addComment(CATEGORY_TODOLIST, $itemId, $comment);
			
	} else if (isset($_GET['unassign'])) {
		/* Unassign item */
		if ($item['assignedTo'] == $_SESSION['userId']) {
			unassignTodoItem($itemId);
				
			$comment=$_SESSION['userName'].' unassigned himself from the task.';
			addComment(CATEGORY_TODOLIST, $itemId, $comment);
			$item['assignedTo'] = 0;
		}
	}

	echo sprintf('<b>Problem Report PR%04d</b><br>', $itemId);

	echo '<table width=400 cellpadding=0 cellspacing=0 border=0>';
	echo '<tr><td colspan=2>'.$item['itemDesc'].'<br><br></td></tr>';

	echo '<tr><td colspan=2 bgcolor=#D0D0D0>'.nl2br($item['itemDetails']).'<br><br></td></tr>';
	echo '<tr><td width=80>Created:</td><td>'.$item['timeCreated'].', '.'by '.Users::link($item['itemCreator'], $item['userName']);
	echo '</td></tr>';

	echo '<tr><td>TODO list:</td><td>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?category='.$item['categoryId'];
	if ($item['itemStatus'] == TODO_ITEM_CLOSED) echo '&showclosed=1';
	echo '">'.getCategoryName(CATEGORY_TODOLIST, $item['categoryId']).'</a> - ';
	echo '<a href="admin_move_todo_item.php?id='.$itemId.'">&raquo; Move PR</a>';
	echo '</td></tr>';

	echo '<tr><td>Category:</td><td>'.$todo_item_category[ $item['itemCategory'] ].'</td></tr>';
	echo '<tr><td>Status:</td>';
	echo '<form name="changestatus" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.getProjectPath().'">';
	echo '<td>'.$todo_item_status[ $item['itemStatus'] ].', change to ';
	echo '<select name="changestatus">';
	for ($i=0; $i<count($todo_item_status); $i++) {
		if ($i != $item['itemStatus']) {
			if (! (($item['itemStatus'] == TODO_ITEM_ASSIGNED) && ($i == TODO_ITEM_OPEN)) ) {//är den assigned kan man inte välja OPEN
				if (! (($item['itemStatus'] == TODO_ITEM_CLOSED) && ($i == TODO_ITEM_ASSIGNED)) ) {//är den closed kan man inte välja ASSIGNED
					echo '<option value="'.$i.'">'.$todo_item_status[$i];
				}
			}
		}
	}
	echo '</select> <input type="submit" class="button" value="Change">';
	echo '</td></form></tr>';
	echo '<tr><td>Assigned to:&nbsp;</td>';
		echo '<form name="assignto" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.getProjectPath().'">';
		echo '<td>';
		if (!$item['assignedTo']) {
			if ($item['itemStatus'] != TODO_ITEM_CLOSED) {
				echo 'Nobody, assign to ';
					
				echo '<select name="assignto">';
				$admins = Users::getAdmins();
				foreach ($admins as $arow) {
					echo '<option value="'.$arow['userId'].'"';
					if ($arow['userId'] == $session->id) echo ' selected';
					echo '>'.$arow['userName'];
				}
				echo '</select> <input type="submit" class="button" value="Assign">';

			} else {
				echo 'Nobody';
			}
		} else {
			echo Users::link($item['assignedTo']);
			if ($item['assignedTo'] == $session->id) {
				echo ', <a href="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'&unassign=1">unassign</a>';
			} else {
				echo ', only he can unassign himself.';
			}
		}
	echo '</td></form></tr>';
	echo '</table>';
	echo '<br>';

	echo '<b>Development log</b><br><br>';
	showComments(COMMENT_TODOLIST, $itemId);

	//echo showFileAttachments($itemId, FILETYPE_PR);
		
	echo '<a href="admin_assigned_tasks.php">&raquo; Back to your assigned tasks</a><br>';
	echo '<a href="admin_current_work.php">&raquo; Back to current work</a><br>';

	if (isset($_GET['category'])) {
		/* Show a item category */
		$categoryId = $_GET['category'];
		$listName = getCategoryName(CATEGORY_TODOLIST, $categoryId);
		
		if (isset($_GET['showclosed'])) {
			/* Show only CLOSED items in this category */
			echo '<b class="topic">Administration screen - TODO list for "'.$listName.'" category</b><br>';
			echo '<b>OBSERVE - ONLY CLOSED ITEMS IN THIS LIST.</b><br><br>';
			
			$list = getClosedTodoItems($categoryId);
			for ($i=0; $i<count($list); $i++) {
				echo sprintf("PR%04d: ", $list[$i]['itemId']);
				echo '<a href="admin_todo_lists.php?id='.$list[$i]['itemId'].getProjectPath().'">'.$list[$i]['itemDesc'].'</a> ('.$todo_item_status[$list[$i]['itemStatus']].')<br>';
			}
			echo '<br>'.count($list).' items in list.<br>';

			echo '<a href="'.$_SERVER['PHP_SELF'].'?category='.$categoryId.'">&raquo; Back to TODO list '.getTodoCategoryName($categoryId).' index</a><br>';
			echo '<a href="admin_current_work.php">&raquo; Back to current work</a><br>';

		} else {
			/* Show the OPEN and ASSIGNED items in this category */
			if (isset($_POST['desc'])) {
				addTodoItem($categoryId, $_POST['desc'], $_POST['details'], $_POST['category']);
			}

			echo '<b class="topic">Administration screen - TODO list for "'.$listName.'" category</b><br><br>';
		
			$list = getTodoItems($categoryId);
			for ($i=0; $i<count($list); $i++) {
				echo '<a href="admin_todo_lists.php?id='.$list[$i]['itemId'].getProjectPath().'">';
				echo sprintf('PR%04d: ', $list[$i]['itemId'] );
				echo $list[$i]['itemDesc'].'</a> ('.$todo_item_status[$list[$i]['itemStatus']].')<br>';
			}
			$closeditems = getClosedTodoCategoryItems($categoryId);
			echo '<br>'.count($list).' items (ignoring '.$closeditems.' closed items).<br>';
			if ($closeditems) {
				echo '<a href="'.$_SERVER['PHP_SELF'].'?category='.$categoryId.'&showclosed=1">&raquo; List closed items for this category</a><br>';
			}
		
			echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?category='.$categoryId.getProjectPath().'">';
			echo '<b class="topic">Add a item to the list</b><br><br>';
			echo 'Description:<br>';
			echo '<input type="text" name="desc" size=66><br>';
			echo 'Details:<br>';
			echo '<textarea name="details" cols=64 rows=12></textarea><br>';
			echo 'Category: ';
			echo '<select name="category">';
			for ($i=0; $i<count($todo_item_category); $i++) {
				echo '<option value="'.$i.'">'.$todo_item_category[$i];
			}
			echo '</select>';
			echo '<br><br>';
		
			echo '<input type="submit" class="button" value="Add item">';
			echo '</form>';
			echo '<br>';
		
			echo '<a href="admin_current_work.php">&raquo; Back to current work</a><br>';
		}
	}

	require($project.'design_foot.php');
?>