<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	include("design_head.php");

	echo "<b class=\"topic\">Administration screen - Your assigned tasks</b><br>";
	echo "Here is all your currently assigned tasks, please update task progress in the Development Log<br>";
	echo "for each task, so other developers can see how things progress.<br><br>";

	if (isset($_GET["closed"])) {
		echo "<b>OBSERVE: THIS IS YOUR CLOSED TASKS!</b><br><br>";
		
		$list = getClosedAssignedTasks($db, $_SESSION["userId"]);
		for ($i=0; $i<count($list); $i++) {
			printf("PR%04d: ", $list[$i]["itemId"] );
			echo "<a href=\"admin_todo_lists.php?id=".$list[$i]["itemId"]."\">".$list[$i]["itemDesc"]."</a> (".$todo_list[$list[$i]["listId"]].")<br>";
		}
	
		echo "<br>";
		echo "You have ".count($list)." CLOSED assigned tasks.<br><br>";
		echo "<a href=\"".$_SERVER["PHP_SELF"]."\">&raquo; Show your UNCLOSED assigned tasks</a><br>";
		echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";
		
	} else {
		$list = getAssignedTasks($db, $_SESSION["userId"]);
		for ($i=0; $i<count($list); $i++) {
			printf("PR%04d: ", $list[$i]["itemId"] );
			echo "<a href=\"admin_todo_lists.php?id=".$list[$i]["itemId"]."\">".$list[$i]["itemDesc"]."</a> (".$todo_list[$list[$i]["listId"]].")<br>";
		}
	
		echo "<br>";
		$closedtasks=getClosedAssignedTasksCount($db, $_SESSION["userId"]);
		echo "<b>You have ".count($list)." assigned tasks</b> (excluding ".$closedtasks." CLOSED tasks).<br><br>";
		if ($closedtasks) {
			echo "<a href=\"".$_SERVER["PHP_SELF"]."?closed\">&raquo; Show your CLOSED assigned tasks</a><br>";
		}
		echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";
	}

	include("design_foot.php");
?>
