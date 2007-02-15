<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	include("design_head.php");
	
	
	echo "<b class=\"topic\">Administration screen - Delete user</b><br>";
	echo "You may enter either the username or userId for the user to be deleted.<br><br>";
	
	if (isset($_POST["deleteuser"])) {
		if (deleteUser($db, $_POST["deleteuser"])) {
			echo "User deleted.<br>";
		} else {
			echo "No such user.<br>";
		}
	}

?>
	<table cellpadding=0 cellspacing=0 border=0>
	<form method="post" action="admin_delete_user.php">
	<tr><td>User:&nbsp;</td><td><input type="text" name="deleteuser"></td></tr>
	<tr><td colspan=2><br><input type="submit" value="Delete"></td></tr>
	</form>
	</table>
	<br>

	<a href="admin.php">&raquo; Back to Administration screen</a><br>

<?
	include("design_foot.php");
?>