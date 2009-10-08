<?php
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	if (!isset($_POST["pr"]) || !$_POST["pr"]) {
		header("Location: admin.php"); die;
	}

	$pr = $_POST["pr"];
	$prData = getTodoItem($db, $pr);
	if (!$prData) {
		include("design_head.php");

		echo "PR ".$pr." not found.<br><br>";
		echo "<a href=\"admin.php\">&raquo; Go back to Administration screen</a><br>";

		include("design_foot.php");
	} else {
		header("Location: admin_todo_lists.php?id=".$prData["itemId"]); die;
	}
?>
