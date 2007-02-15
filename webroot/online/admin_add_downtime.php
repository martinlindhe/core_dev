<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }	

	include("design_head.php");
	
	echo "<b class=\"topic\">Administration screen - Add scheduled downtime</b><br>";
	echo "You may write the date in any format you like, for example '15 nov' works fine<br>";
	echo "and will be understood as November 15, current year.<br><br>";
	
	if (isset($_POST["date"]) && isset($_POST["info"])) {
		if (addServerDowntime($db, $_POST["date"], $_POST["info"]) === false) {
			echo "Failed to add downtime, be sure you enter a valid date.<br>";
		} else {
			echo "Downtime added.<br>";
		}
	}
?>
	<table cellpadding=0 cellspacing=0 border=0>
	<form method="post" action="admin_add_downtime.php">
	<tr><td>Date:</td><td><input type="text" name="date"></td></tr>
	<tr><td colspan=2>Information:<br>
	<textarea name="info" rows=10 cols=40></textarea></td></tr>
	<tr><td colspan=2><br><input type="submit" value="Add downtime"></td></tr>
	</form>
	</table>
	<br>
<?
	echo "<a href=\"server_status.php\">&raquo; Show scheduled server downtimes</a><br>";
	echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";


	include("design_foot.php");
?>