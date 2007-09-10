<?
	require_once('find_config.php');
	$session->requireAdmin();

	//a shared todo list for all admins.
	//each todo item can be marked as "private" to only be displayed for the current user

	//each todo item should be able to be edited
	//each item should be able to be commented upon

	require($project.'design_head.php');
	echo createMenu($admin_menu, 'blog_menu');

	if (isset($_GET['id'])) {
		/* Show a specific item */
		$itemId = $_GET['id'];
		
		$item = getTodoItem($db, $itemId);
		if (isset($_POST['changestatus'])) {
			/* Change item status */
			setTodoItemStatus($db, $itemId, $_POST['changestatus']);

			if ($_POST['changestatus'] == TODO_ITEM_ASSIGNED) {
				assignTodoItem($db, $itemId, $_SESSION['userId']);
				$comment ='Status changed from '.$todo_item_status[$item['itemStatus'] ].' to '.$todo_item_status[ $_POST['changestatus']].' by '.$_SESSION['userName'].'.<br>';
				$comment.='(Meaning item is now assigned to '.$_SESSION['userName'].').';
				$item['assignedTo'] = $_SESSION['userId']; //update changes
			} else {
				$comment='Status changed from '.$todo_item_status[$item['itemStatus'] ].' to '.$todo_item_status[ $_POST['changestatus']].' by '.$_SESSION['userName'].'.';
			}
			$item['itemStatus'] = $_POST['changestatus']; //update changes			
			addTodoItemComment($db, 0, $itemId, $comment);

		} else if (isset($_POST['assignto'])) {
			/* Assign item to a developer */
			assignTodoItem($db, $itemId, $_POST['assignto']);
			$item['itemStatus'] = TODO_ITEM_ASSIGNED; //update changes
			$item['assignedTo'] = $_POST['assignto']; //update changes
			
			$comment=$_SESSION['userName'].' assigned the task to '.getUserName($db, $_POST['assignto']).'.';
			addTodoItemComment($db, 0, $itemId, $comment);
			
		} else if (isset($_GET['unassign'])) {
			/* Unassign item */
			if ($item['assignedTo'] == $_SESSION['userId']) {
				unassignTodoItem($db, $itemId);
				
				$comment=$_SESSION['userName'].' unassigned himself from the task.';
				addTodoItemComment($db, 0, $itemId, $comment);
				$item['assignedTo'] = 0;
			}
			
		} else if (isset($_POST['addcomment'])) {
			/* Add a comment */
			addTodoItemComment($db, $_SESSION['userId'], $itemId, $_POST['addcomment']);
		}

		echo '<b>Todo list item #'.$itemId.'</b><br/>';

		echo '<table width=400 cellpadding=0 cellspacing=0 border=0>';
		echo '<tr><td colspan=2>'.$item['itemDesc'].'<br><br></td></tr>';

		echo '<tr><td colspan=2 bgcolor=#D0D0D0>'.nl2br($item['itemDetails']).'<br><br></td></tr>';
		echo '<tr><td width=80>Created:</td><td>'.getRelativeTimeLong($item['timestamp']).', '.'by '.nameLink($item['itemCreator'], $item['userName']);
		echo '</td></tr>';

		echo '<tr><td>TODO list:</td><td>';
			echo '<a href="'.$_SERVER['PHP_SELF'].'?category='.$item['categoryId'];
			if ($item['itemStatus'] == TODO_ITEM_CLOSED) echo '&showclosed=1';
			echo '">'.getTodoCategoryName($db, $item['categoryId']).'</a> - ';
			echo '<a href="admin_move_todo_item.php?id='.$itemId.'">&raquo; Move PR</a>';
		echo '</td></tr>';

		echo '<tr><td>Category:</td><td>'.$todo_item_category[ $item['itemCategory'] ].'</td></tr>';
		echo '<tr><td>Status:</td>';
			echo '<form name="changestatus" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
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
			echo '<form name="assignto" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
			echo '<td>';
			if (!$item['assignedTo']) {
				if ($item['itemStatus'] != TODO_ITEM_CLOSED) {
					echo 'Nobody, assign to ';
					
					echo '<select name="assignto">';
					$adminlist = getAdministrators($db);
					for ($i=0; $i<count($adminlist); $i++) {
						echo '<option value="'.$adminlist[$i]['userId'].'"';
						if ($adminlist[$i]['userId'] == $_SESSION['userId']) echo ' selected';
						echo '>'.$adminlist[$i]['userName'];
					}
					echo '</select> <input type="submit" class="button" value="Assign">';

				} else {
					echo 'Nobody';
				}
			} else {
				echo nameLink($item['assignedTo'], getUserName($db, $item['assignedTo']));
				if ($item['assignedTo'] == $_SESSION['userId']) {
					echo ', <a href="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'&unassign=1">unassign</a>';
				} else {
					echo ', only he can unassign himself.';
				}
			}
		echo '</td></form></tr>';
		echo '</table>';
		echo '<br>';

		echo '<b>Add comment</b><br><br>';
		echo '<table cellpadding=0 cellspacing=0 border=0>';
		echo '<form name="addcomment" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
		echo '<tr><td><textarea name="addcomment" cols=60 rows=6></textarea></td></tr>';
		echo '<tr><td><br><input type="submit" class="button" value="Add comment"></td></tr>';
		echo '</form>';
		echo '</table>';
		echo '<br>';
		
		echo '<b>Development log</b><br><br>';
		$list = getTodoItemComments($db, $itemId, 'desc');

		for ($i=0; $i<count($list); $i++) {
			echo '<div class="devlog">';
			echo getRelativeTimeLong($list[$i]['timestamp']).', ';
			if ($list[$i]['userId']) {
				echo 'by '.nameLink($list[$i]['userId'], $list[$i]['userName']);
			} else {
				echo 'system message';
			}
			echo '<br><br>';
			$comment = formatUserInputText($list[$i]['itemComment']);
			echo $comment;
			echo '</div><br>';
		}
		if (!count($list)) $content .= 'Empty.<br><br>';
		
		echo showFileAttachments($db, $itemId, FILETYPE_PR);
		
		echo '<a href="admin_assigned_tasks.php">&raquo; Back to your assigned tasks</a><br>';
		echo '<a href="admin_current_work.php">&raquo; Back to current work</a><br>';
	}
	
	if (isset($_GET['showclosed'])) {
		/* Show only CLOSED items in this category */
		echo '<b class="topic">Administration screen - TODO list for "'.$listName.'" category</b><br>';
		echo '<b>OBSERVE - ONLY CLOSED ITEMS IN THIS LIST.</b><br><br>';
			
		$list = getClosedTodoItems($db, $categoryId);
		for ($i=0; $i<count($list); $i++) {
			echo sprintf("PR%04d: ", $list[$i]['itemId']);
			echo '<a href="admin_todo_lists.php?id='.$list[$i]['itemId'].'">'.$list[$i]['itemDesc'].'</a> ('.$todo_item_status[$list[$i]['itemStatus']].')<br>';
		}
		echo '<br>'.count($list).' items in list.<br>';

		echo '<a href="'.$_SERVER['PHP_SELF'].'?category='.$categoryId.'">&raquo; Back to TODO list '.getTodoCategoryName($db, $categoryId).' index</a><br>';
		echo '<a href="admin_current_work.php">&raquo; Back to current work</a><br>';
	}


	/* Show the OPEN and ASSIGNED items in this category */
	if (isset($_POST['desc'])) {
		addComment(COMMENT_TODO_LIST, 0, $_POST['desc'], !empty($_POST['priv']) ? true : false );
	}

	echo 'Admin todo lists<br/><br/>';
		
	$list = getComments(COMMENT_TODO_LIST, 0, true);
	foreach ($list as $row) {
		echo '<a href="admin_todo_lists.php?id='.$row['commentId'].getProjectPath().'">';
		echo $row['commentText'].'</a> ('.$row['timeCreated'].')<br/>';
	}
	echo '<br/>'.count($list).' items.<br/>';
		
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].getProjectPath(0).'">';
	echo '<b class="topic">Add a item to the list</b><br/><br/>';
	echo 'Short summary:<br/>';
	echo '<textarea name="desc" cols=40 rows=8></textarea><br/>';
	echo '<input type="hidden" name="priv" value="0"/>';
	echo '<input type="checkbox" id="priv" name="priv" value="1"/><label for="priv">Private</label>';
	echo '<br/><br/>';

	echo '<input type="submit" class="button" value="Add item"/>';
	echo '</form>';
	echo '<br/>';
	
	showAllComments(COMMENT_TODO_LIST);

	require($project.'design_foot.php');
?>