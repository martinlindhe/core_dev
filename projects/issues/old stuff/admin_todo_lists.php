<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	include("design_head.php");

	$lookup_pr  = "<table cellpadding=0 cellspacing=0 border=0>";
	$lookup_pr .= "<form method=\"post\" action=\"admin_lookup_pr.php\"><tr><td>";
	$lookup_pr .= "&raquo; Lookup PR: <input type=\"text\" name=\"pr\" size=6> <input type=\"submit\" value=\"Go\">";
	$lookup_pr .= "</td></tr></form></table>";

	if (isset($_GET["id"])) {
		/* Show a specific item */
		$itemId = $_GET["id"];
		
		$item = getTodoItem($db, $itemId);
		if (isset($_POST["changestatus"])) {
			/* Change item status */
			setTodoItemStatus($db, $itemId, $_POST["changestatus"]);

			if ($_POST["changestatus"] == TODO_ITEM_ASSIGNED) {
				assignTodoItem($db, $itemId, $_SESSION["userId"]);
				$comment ="Status changed from ".$todo_item_status[$item["itemStatus"] ]." to ".$todo_item_status[ $_POST["changestatus"]]." by ".$_SESSION["userName"].".<br>";
				$comment.="(Meaning item is now assigned to ".$_SESSION["userName"].").";
				$item["assignedTo"] = $_SESSION["userId"]; //update changes
			} else {
				$comment="Status changed from ".$todo_item_status[$item["itemStatus"] ]." to ".$todo_item_status[ $_POST["changestatus"]]." by ".$_SESSION["userName"].".";
			}
			$item["itemStatus"] = $_POST["changestatus"]; //update changes			
			addTodoItemComment($db, 0, $itemId, $comment);

		} else if (isset($_POST["assignto"])) {
			/* Assign item to a developer */
			assignTodoItem($db, $itemId, $_POST["assignto"]);
			$item["itemStatus"] = TODO_ITEM_ASSIGNED; //update changes
			$item["assignedTo"] = $_POST["assignto"]; //update changes
			
			$comment=$_SESSION["userName"]." assigned the task to ".getUserName($db, $_POST["assignto"]).".";
			addTodoItemComment($db, 0, $itemId, $comment);
			
		} else if (isset($_GET["unassign"])) {
			/* Unassign item */
			if ($item["assignedTo"] == $_SESSION["userId"]) {
				unassignTodoItem($db, $itemId);
				
				$comment=$_SESSION["userName"]." unassigned himself from the task.";
				addTodoItemComment($db, 0, $itemId, $comment);
				$item["assignedTo"] = 0;
			}
			
		} else if (isset($_POST["addcomment"])) {
			/* Add a comment */
			addTodoItemComment($db, $_SESSION["userId"], $itemId, $_POST["addcomment"]);
		}

		printf("<b class=\"topic\">Problem Report PR%04d</b><br>", $itemId);

		echo "<table width=400 cellpadding=0 cellspacing=0 border=0>";
		echo "<tr><td colspan=2>".$item["itemDesc"]."<br><br></td></tr>";

		echo "<tr><td colspan=2 bgcolor=#D0D0D0>".nl2br($item["itemDetails"])."<br><br></td></tr>";
		echo "<tr><td width=80>Created:</td><td>".date($long_date, $item["timestamp"]).", ";
		if ($item["userName"]) {
			echo "by <a href=\"show_user.php?id=".$item["itemCreator"]."\">".$item["userName"]."</a>";
		} else {
			echo "<b>creator has been deleted.</b>";
		}
		echo "</td></tr>";

		echo "<tr><td>TODO list:</td><td>".$todo_list[$item["listId"]]."</td></tr>";
		echo "<tr><td>Category:</td><td>".$todo_item_category[ $item["itemCategory"] ]."</td></tr>";
		echo "<tr><td>Status:</td>";
			echo "<form name=\"changestatus\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?id=".$itemId."\">";
			echo "<td>".$todo_item_status[ $item["itemStatus"] ].", change to ";
			echo "<select name=\"changestatus\">";
			for ($i=0; $i<count($todo_item_status); $i++) {
				if ($i != $item["itemStatus"]) {
					if (! (($item["itemStatus"] == TODO_ITEM_ASSIGNED) && ($i == TODO_ITEM_OPEN)) ) {//är den assigned kan man inte välja OPEN
						if (! (($item["itemStatus"] == TODO_ITEM_CLOSED) && ($i == TODO_ITEM_ASSIGNED)) ) {//är den closed kan man inte välja ASSIGNED
							echo "<option value=\"".$i."\">".$todo_item_status[$i];
						}
					}
				}
			}
			echo "</select> <input type=\"submit\" value=\"Change\">";
		echo "</td></form></tr>";
		echo "<tr><td>Assigned to:&nbsp;</td>";
			echo "<form name=\"assignto\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?id=".$itemId."\">";
			echo "<td>";
			if (!$item["assignedTo"]) {
				if ($item["itemStatus"] != TODO_ITEM_CLOSED) {
					echo "Nobody, assign to ";
					
					echo "<select name=\"assignto\">";
					$adminlist=getAdministrators($db);
					for ($i=0; $i<count($adminlist); $i++) {
						echo "<option value=\"".$adminlist[$i]["userId"]."\">".$adminlist[$i]["userName"];
					}
					echo "</select> <input type=\"submit\" value=\"Assign\">";

				} else {
					echo "Nobody";
				}
			} else {
				echo "<a href=\"show_user.php?id=".$item["assignedTo"]."\">".getUserName($db, $item["assignedTo"])."</a>";
				if ($item["assignedTo"] == $_SESSION["userId"]) {
					echo ", <a href=\"".$_SERVER["PHP_SELF"]."?id=".$itemId."&unassign=1\">unassign</a>";
				} else {
					echo ", only he can unassign himself.";
				}
			}
		echo "</td></form></tr>";
		echo "</table>";
		echo "<br>";

		echo "<b class=\"topic\">Add comment</b><br><br>";
		echo "<table cellpadding=0 cellspacing=0 border=0>";
		echo "<form name=\"addcomment\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?id=".$itemId."\">";
		echo "<tr><td><textarea name=\"addcomment\" cols=60 rows=6></textarea></td></tr>";
		echo "<tr><td><br><input type=\"submit\" value=\"Add comment\"></td></tr>";
		echo "</form>";
		echo "</table>";
		echo "<br>";
		
		echo "<b class=\"topic\">Development log</b><br><br>";
		$list = getTodoItemComments($db, $itemId);
		for ($i=0; $i<count($list); $i++) {
			echo "<table bgcolor=#D0D0D0 width=400 cellpadding=0 cellspacing=0 border=0>";
			echo "<tr><td>";
			echo date($long_date, $list[$i]["timestamp"]).", ";
			if ($list[$i]["userId"]) {
				echo "by <a href=\"show_user.php?id=".$list[$i]["userId"]."\">".$list[$i]["userName"]."</a>";
			} else {
				echo "system message";
			}
			echo "</td></tr>";
			echo "<tr><td>".nl2br($list[$i]["itemComment"])."</td></tr>";
			echo "</table><br>";
		}
		if (!count($list)) echo "Empty.<br><br>";

		echo $lookup_pr;
		echo "<br>";

		echo "<a href=\"admin_todo_lists.php?list=".$item["listId"]."\">&raquo; Back to TODO lists ".$todo_list[$item["listId"]]." index</a><br>";
		echo "<a href=\"admin_todo_lists.php\">&raquo; Back to TODO lists index</a><br>";
		echo "<a href=\"admin_assigned_tasks.php\">&raquo; Back to your assigned tasks</a><br>";
		echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";

	} else if (isset($_GET["list"])) {
		/* Show a item category */
		$listId = $_GET["list"];
		
		if (isset($_GET["showclosed"])) {
			/* Show only CLOSED items in this category */
			echo "<b class=\"topic\">Administration screen - TODO list for '".$todo_list[$listId]."' category</b><br>";
			echo "<b>OBSERVE - ONLY CLOSED ITEMS IN THIS LIST.</b><br><br>";
			
			$list = getClosedTodoItems($db, $listId);
			for ($i=0; $i<count($list); $i++) {
				printf("PR%04d: ", $list[$i]["itemId"] );
				echo "<a href=\"admin_todo_lists.php?id=".$list[$i]["itemId"]."\">".$list[$i]["itemDesc"]."</a> (".$todo_item_status[$list[$i]["itemStatus"]].")<br>";
			}
			echo "<br>".count($list)." items in list.<br>";

			echo $lookup_pr;
			echo "<br>";
			
			echo "<a href=\"admin_todo_lists.php?list=".$listId."\">&raquo; Back to TODO lists ".$todo_list[$listId]." index</a><br>";
			echo "<a href=\"admin_todo_lists.php\">&raquo; Back to TODO lists index</a><br>";
			echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";

		} else {
			/* Show the OPEN and ASSIGNED items in this category */
			if (isset($_POST["desc"])) {
				addTodoItem($db, $listId, $_SESSION["userId"], $_POST["desc"], $_POST["details"], $_POST["category"]);
			}

			echo "<b class=\"topic\">Administration screen - TODO list for '".$todo_list[$listId]."' category</b><br><br>";
		
			$list = getTodoItems($db, $listId);
			for ($i=0; $i<count($list); $i++) {
				printf("PR%04d: ", $list[$i]["itemId"] );
				echo "<a href=\"admin_todo_lists.php?id=".$list[$i]["itemId"]."\">".$list[$i]["itemDesc"]."</a> (".$todo_item_status[$list[$i]["itemStatus"]].")<br>";
			}
			$closeditems = getClosedTodoCategoryItems($db, $listId);
			echo "<br>".count($list)." items (ignoring ".$closeditems." closed items).<br>";
			if ($closeditems) {
				echo "<a href=\"".$_SERVER["PHP_SELF"]."?list=".$listId."&showclosed=1\">&raquo; List closed items for this category</a><br>";
			}
		
			echo "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?list=".$listId."\">";
			echo "<b class=\"topic\">Add a item to the list</b><br><br>";
			echo "Description: <input type=\"text\" name=\"desc\" size=45><br>";
			echo "Details:<br>";
			echo "<textarea name=\"details\" cols=60 rows=8></textarea><br>";
			echo "Category: ";
				echo "<select name=\"category\">";
				for ($i=0; $i<count($todo_item_category); $i++) {
					echo "<option value=\"".$i."\">".$todo_item_category[$i];
				}
				echo "</select><br>";
		
			echo "<input type=\"submit\" value=\"Add item\">";
			echo "</form>";
		
			echo $lookup_pr;
			echo "<br>";

			echo "<a href=\"admin_todo_lists.php\">&raquo; Back to TODO lists index</a><br>";
			echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";
		}
		
	} else {
		/* Show all categories */
		echo "<b class=\"topic\">Administration screen - TODO lists</b><br>";
		echo "Each category below contains it's own PR's, so one can easily<br>";
		echo "focus on a particular part of the project.<br><br>";
	
		for ($i=0; $i<count($todo_list); $i++) {
			echo "<a href=\"admin_todo_lists.php?list=".$i."\">".$todo_list[$i]."</a>";
			echo " (".getTodoCategoryItemsCount($db, $i)." items)<br>";
		}
		
		echo "<br>";
		echo $lookup_pr;
		echo "<br>";

		echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";
	}

	include("design_foot.php");
?>