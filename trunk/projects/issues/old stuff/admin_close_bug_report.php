<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	if (isset($_GET["id"])) {
		$bugId = $_GET["id"];
		
		if (isset($_POST["reason"])) {
			closeBugReport($db, $_GET["id"], $_POST["reason"]);
			
			header("Location: admin_bug_reports.php"); die;
		} else {

			include("design_head.php");
		
			echo "<b class=\"topic\">Administration screen - Close bug report</b><br><br>";
		
			$item = getBugReport($db, $bugId);
	
			echo "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?id=".$bugId."\">";
			echo date($long_date, $item["timestamp"]).", by <a href=\"show_user.php?id=".$item["bugCreator"]."\">".$item["userName"]."</a><br>";
			echo "Details: <br>";
			echo nl2br($item["bugDesc"])."<br>";
			echo "Close reason: <select name=\"reason\">";
				for ($i=0; $i<count($close_bug_reason); $i++) {
					echo "<option value=\"".$i."\">".$close_bug_reason[$i];
				}
			echo "</select><br>";
			echo "<input type=\"submit\" value=\"Close bug report\">";
			echo "</form>";
		}

		include("design_foot.php");
		
	} else {
		header("Location: admin_bug_reports.php");
	}
?>
		