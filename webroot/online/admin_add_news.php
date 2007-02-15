<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	include("design_head.php");

	echo "<b class=\"topic\">Administration screen - Add news</b><br><br>";

	if (isset($_POST["subject"]) && isset($_POST["body"])) {
		addNews($db, $_POST["subject"], $_POST["body"]);
		echo "News added.";
	}
?>

	<table cellpadding=0 cellspacing=0 border=0>
	<form method="post" action="admin_add_news.php">
	<tr><td>Subject:</td><td><input type="text" name="subject" size=50></td></tr>
	<tr><td colspan=2>Body: <br>
	<textarea name="body" cols=59 rows=10></textarea></td></tr>
	<tr><td colspan=2><br><input type="submit" value="Add news"></td></tr>
	</form>
	</table>
	<br>

	<a href="admin.php">&raquo; Back to Administration screen</a><br>
<?
	include("design_foot.php");
?>