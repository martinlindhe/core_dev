<?
	include_once('include_all.php');

	if (!$_SESSION['isSuperAdmin']) {
		header('Location: '.$config['start_page']);
		die;
	}

	include('design_head.php');
	include('design_user_head.php');
	
	$content = '';

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

		$content .= sprintf('<b>Problem Report PR%04d</b><br>', $itemId);

		$content .= '<table width=400 cellpadding=0 cellspacing=0 border=0>';
		$content .= '<tr><td colspan=2>'.$item['itemDesc'].'<br><br></td></tr>';

		$content .= '<tr><td colspan=2 bgcolor=#D0D0D0>'.nl2br($item['itemDetails']).'<br><br></td></tr>';
		$content .= '<tr><td width=80>Created:</td><td>'.getRelativeTimeLong($item['timestamp']).', '.'by '.nameLink($item['itemCreator'], $item['userName']);
		$content .= '</td></tr>';

		$content .= '<tr><td>TODO list:</td><td>';
			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?category='.$item['categoryId'];
			if ($item['itemStatus'] == TODO_ITEM_CLOSED) $content .= '&showclosed=1';
			$content .= '">'.getTodoCategoryName($db, $item['categoryId']).'</a> - ';
			$content .= '<a href="admin_move_todo_item.php?id='.$itemId.'">&raquo; Move PR</a>';
		$content .= '</td></tr>';

		$content .= '<tr><td>Category:</td><td>'.$todo_item_category[ $item['itemCategory'] ].'</td></tr>';
		$content .= '<tr><td>Status:</td>';
			$content .= '<form name="changestatus" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
			$content .= '<td>'.$todo_item_status[ $item['itemStatus'] ].', change to ';
			$content .= '<select name="changestatus">';
			for ($i=0; $i<count($todo_item_status); $i++) {
				if ($i != $item['itemStatus']) {
					if (! (($item['itemStatus'] == TODO_ITEM_ASSIGNED) && ($i == TODO_ITEM_OPEN)) ) {//är den assigned kan man inte välja OPEN
						if (! (($item['itemStatus'] == TODO_ITEM_CLOSED) && ($i == TODO_ITEM_ASSIGNED)) ) {//är den closed kan man inte välja ASSIGNED
							$content .= '<option value="'.$i.'">'.$todo_item_status[$i];
						}
					}
				}
			}
			$content .= '</select> <input type="submit" class="button" value="Change">';
		$content .= '</td></form></tr>';
		$content .= '<tr><td>Assigned to:&nbsp;</td>';
			$content .= '<form name="assignto" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
			$content .= '<td>';
			if (!$item['assignedTo']) {
				if ($item['itemStatus'] != TODO_ITEM_CLOSED) {
					$content .= 'Nobody, assign to ';
					
					$content .= '<select name="assignto">';
					$adminlist=getAdministrators($db);
					for ($i=0; $i<count($adminlist); $i++) {
						$content .= '<option value="'.$adminlist[$i]['userId'].'"';
						if ($adminlist[$i]['userId'] == $_SESSION['userId']) $content .= ' selected';
						$content .= '>'.$adminlist[$i]['userName'];
					}
					$content .= '</select> <input type="submit" class="button" value="Assign">';

				} else {
					$content .= 'Nobody';
				}
			} else {
				$content .= nameLink($item['assignedTo'], getUserName($db, $item['assignedTo']));
				if ($item['assignedTo'] == $_SESSION['userId']) {
					$content .= ', <a href="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'&unassign=1">unassign</a>';
				} else {
					$content .= ', only he can unassign himself.';
				}
			}
		$content .= '</td></form></tr>';
		$content .= '</table>';
		$content .= '<br>';

		$content .= '<b>Add comment</b><br><br>';
		$content .= '<table cellpadding=0 cellspacing=0 border=0>';
		$content .= '<form name="addcomment" method="post" action="'.$_SERVER['PHP_SELF'].'?id='.$itemId.'">';
		$content .= '<tr><td><textarea name="addcomment" cols=60 rows=6></textarea></td></tr>';
		$content .= '<tr><td><br><input type="submit" class="button" value="Add comment"></td></tr>';
		$content .= '</form>';
		$content .= '</table>';
		$content .= '<br>';
		
		$content .= '<b>Development log</b><br><br>';
		$list = getTodoItemComments($db, $itemId, 'desc');

		for ($i=0; $i<count($list); $i++) {
			$content .= '<div class="devlog">';
			$content .= getRelativeTimeLong($list[$i]['timestamp']).', ';
			if ($list[$i]['userId']) {
				$content .= 'by '.nameLink($list[$i]['userId'], $list[$i]['userName']);
			} else {
				$content .= 'system message';
			}
			$content .= '<br><br>';
			$comment = formatUserInputText($list[$i]['itemComment']);
			$content .= $comment;
			$content .= '</div><br>';
		}
		if (!count($list)) $content .= 'Empty.<br><br>';
		
		$content .= showFileAttachments($db, $itemId, FILETYPE_PR);
		
		$content .= '<a href="admin_assigned_tasks.php">&raquo; Back to your assigned tasks</a><br>';
		$content .= '<a href="admin_current_work.php">&raquo; Back to current work</a><br>';

	} else if (isset($_GET['category'])) {
		/* Show a item category */
		$categoryId = $_GET['category'];
		$listName = getTodoCategoryName($db, $categoryId);
		
		if (isset($_GET['showclosed'])) {
			/* Show only CLOSED items in this category */
			$content .= '<b class="topic">Administration screen - TODO list for "'.$listName.'" category</b><br>';
			$content .= '<b>OBSERVE - ONLY CLOSED ITEMS IN THIS LIST.</b><br><br>';
			
			$list = getClosedTodoItems($db, $categoryId);
			for ($i=0; $i<count($list); $i++) {
				$content .= sprintf("PR%04d: ", $list[$i]['itemId']);
				$content .= '<a href="admin_todo_lists.php?id='.$list[$i]['itemId'].'">'.$list[$i]['itemDesc'].'</a> ('.$todo_item_status[$list[$i]['itemStatus']].')<br>';
			}
			$content .= '<br>'.count($list).' items in list.<br>';

			$content .= '<a href="'.$_SERVER['PHP_SELF'].'?category='.$categoryId.'">&raquo; Back to TODO list '.getTodoCategoryName($db, $categoryId).' index</a><br>';
			$content .= '<a href="admin_current_work.php">&raquo; Back to current work</a><br>';

		} else {
			/* Show the OPEN and ASSIGNED items in this category */
			if (isset($_POST['desc'])) {
				addTodoItem($db, $categoryId, $_SESSION['userId'], $_POST['desc'], $_POST['details'], $_POST['category']);
			}

			$content .= '<b class="topic">Administration screen - TODO list for "'.$listName.'" category</b><br><br>';
		
			$list = getTodoItems($db, $categoryId);
			for ($i=0; $i<count($list); $i++) {
				$content .= '<a href="admin_todo_lists.php?id='.$list[$i]['itemId'].'">';
				$content .= sprintf('PR%04d: ', $list[$i]['itemId'] );
				$content .= $list[$i]['itemDesc'].'</a> ('.$todo_item_status[$list[$i]['itemStatus']].')<br>';
			}
			$closeditems = getClosedTodoCategoryItems($db, $categoryId);
			$content .= '<br>'.count($list).' items (ignoring '.$closeditems.' closed items).<br>';
			if ($closeditems) {
				$content .= '<a href="'.$_SERVER['PHP_SELF'].'?category='.$categoryId.'&showclosed=1">&raquo; List closed items for this category</a><br>';
			}
		
			$content .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'?category='.$categoryId.'">';
			$content .= '<b class="topic">Add a item to the list</b><br><br>';
			$content .= 'Description:<br>';
			$content .= '<input type="text" name="desc" size=66><br>';
			$content .= 'Details:<br>';
			$content .= '<textarea name="details" cols=64 rows=12></textarea><br>';
			$content .= 'Category: ';
			$content .= '<select name="category">';
			for ($i=0; $i<count($todo_item_category); $i++) {
				$content .= '<option value="'.$i.'">'.$todo_item_category[$i];
			}
			$content .= '</select>';
			$content .= '<br><br>';
		
			$content .= '<input type="submit" class="button" value="Add item">';
			$content .= '</form>';
			$content .= '<br>';
		
			$content .= '<a href="admin_current_work.php">&raquo; Back to current work</a><br>';
		}
	}

		echo '<div id="user_admin_content">';
		echo MakeBox('<a href="admin.php">Administrationsgr&auml;nssnitt</a>|Todo lists', $content);
		echo '</div>';

	include('design_admin_foot.php');
	include('design_foot.php');
?>