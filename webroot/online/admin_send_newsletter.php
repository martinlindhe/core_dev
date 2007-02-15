<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	include("design_head.php");
	
	set_time_limit(0);
	
	echo "<b class=\"topic\">Administration screen - Send newsletter</b><br>";
	
	if (isset($_POST["subject"]) && isset($_POST["body"])) {

		sendNewsletter($db, $_POST["subject"], $_POST["body"]);

		echo "<br>";
		echo "The newsletter has been sent!<br>";
	} else {
	
		echo "From this screen you can send out a newsletter to all users who have checked<br>";
		echo "the 'Newsletter' checkbox in their configuration.<br><br>";
	
		echo "<table cellpadding=0 cellspacing=0 border=0>";
		echo "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
		echo "<tr><td>Subject: <input name=\"subject\" size=60 type=\"text\"></td></tr>";
		echo "<tr><td><textarea name=\"body\" cols=90 rows=30></textarea></td></tr>";
		echo "<tr><td><br><input type=\"submit\" value=\"Send newsletter\"></td></tr>";
		echo "</form>";
		echo "</table>";
	}

	echo "<br>";
	echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";

	include("design_foot.php");
?>	