<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	if (isset($_GET["id"])) {
		$bugId = $_GET["id"];
		
		
		if (isset($_POST["desc"])) {
			
			$pr = moveBugReport($db, $_SESSION["userId"], $bugId, $_POST["creator"], $_POST["desc"], $_POST["details"], $_POST["timestamp"], $_POST["itemCategory"], $_POST["listId"]);

			include("design_head.php");
			echo "The bug report has been successfully moved into the todo list system!<br>";
			echo "<a href=\"admin_todo_lists.php?id=".$pr."\">&raquo; Click here to go to the PR.</a><br>";
			include("design_foot.php");
			die;
		}
		
	} else {
		header("Location: admin_bug_reports.php"); die;
	}

	$item = getBugReport($db, $bugId);
	if (!$item) {
		header("Location: admin_bug_reports.php"); die;
	}

	include("design_head.php");

	echo "<b class=\"topic\">Administration screen - Move bug report</b><br><br>";

	echo "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?id=".$bugId."\">";
	echo date($long_date, $item["timestamp"]).", by <a href=\"show_user.php?id=".$item["bugCreator"]."\">".$item["userName"]."</a><br>";
	echo "<input name=\"timestamp\" type=\"hidden\" value=\"".$item["timestamp"]."\">";
	echo "<input name=\"creator\" type=\"hidden\" value=\"".$item["bugCreator"]."\">";
	echo "Description: <input size=40 type=\"text\" name=\"desc\"><br>";
	echo "<textarea name=\"details\" cols=60 rows=8>".$item["bugDesc"]."</textarea><br>";
	
	echo "Category: ";
	echo "<select name=\"itemCategory\">";
		echo "<option>";
		for ($i=0; $i<count($todo_item_category); $i++) {
			echo "<option value=\"".$i."\">".$todo_item_category[$i];
		}
	echo "</select><br>";

	echo "Add to TODO-list: ";
	echo "<select name=\"listId\">";
		echo "<option>";
		for ($i=0; $i<count($todo_list); $i++) {
			echo "<option value=\"".$i."\">".$todo_list[$i];
		}
	echo "</select><br>";
	echo "<input type=\"submit\" value=\"Move bug\"><br>";
	echo "</form>";
	
	echo "<a href=\"admin_bug_reports.php\">&raquo; Back to Bug Reports</a><br>";
	echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";

	include("design_foot.php");
?>
