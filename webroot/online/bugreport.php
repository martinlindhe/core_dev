<?
	include_once("functions/include_all.php");
	include("design_head.php");
	
	if (isset($_POST["desc"])) {
		
		//add report
		addBugReport($db, $_SESSION["userId"], $_POST["desc"]);
		
		echo "<b class=\"topic\">Report a bug/missing feature</b><br><br>";
		
		echo "Thank you for your support!<br>";
		echo "The bug report have been stored in our databases and will be overlooked as soon as possible!<br><br>";	
		
		echo "<a href=\"index.php\">&raquo; Back to start page</a>";

	} else {
		echo "<b class=\"topic\">Report a bug/missing feature</b><br>";
		echo "From here you can submit bug reports or feature requests regarding the game or website.<br>";
		echo "Please leave as many details as possible.<br><br>";
	
		echo "<table cellpadding=0 cellspacing=0 border=0>";
		echo "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
		echo "<tr><td>Description:<br>";
		echo "<textarea name=\"desc\" cols=60 rows=8></textarea></td></tr>";
		echo "<tr><td><br><input type=\"submit\" value=\"Submit bugreport\"></td></tr>";
		echo "</form>";
		echo "</table>";
		echo "<br>";
		
		echo "If the bug reporting tool gives you problems, then please ";
		echo "contact us at ".SUPPORT_MAIL_HTML." instead!<br>";
		echo "<br>";
		echo "<a href=\"index.php\">&raquo; Back to start page</a>";
	}
	
	include("design_foot.php");
?>