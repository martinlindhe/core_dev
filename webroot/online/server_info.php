<?
	include_once("functions/include_all.php");
	
	if (!isset($_GET["id"]) || !$_GET["id"]) {
		header("Location: server_status.php"); die;
	}
	include("design_head.php");

	
	echo "<h1>THIS PAGE MUST BE TOTALLY REMADE. SERVER INFO SHALL BE STORED IN A DIFFERENT DB</h1>";
	$server = getGameServerInfo($db, $_GET["id"]);
	echo "<b class=\"topic\">Server information - ".$server["serverName"]."</b><br><br>";
	
	echo "The current IP is ".$server["serverIP"]."<br>";
	echo "There are ".$server["users"]." users on this server, with a total of ".$server["characters"]." characters.<br>";
	echo "<br>";
	echo "<br>";
	
	if ($_SESSION["superUser"]) {
		echo "<b class=\"topic\">Edit server information</b><br><br>";
		echo "<table cellpadding=0 cellspacing=0 border=0>";
		echo "<form method=\"post\" action=\"admin_edit_servers.php?id=".$_GET["id"]."&update\">";
		echo "<tr><td>Server name:&nbsp;</td><td><input type=\"text\" name=\"name\" value=\"".$server["serverName"]."\"></td></tr>";
		echo "<tr><td>Server IP:</td><td><input type=\"text\" name=\"host\" value=\"".$server["serverIP"]."\"></td></tr>";
		echo "<tr><td colspan=2><br><input type=\"submit\" value=\"Save changes\"></td></tr>";
		echo "</td></tr>";
		echo "</form>";
		echo "</table>";
		echo "<br>";
	}

	echo "<a href=\"server_status.php\">&raquo; Back to Server Status screen</a><br>";
	if ($_SESSION["superUser"]) {
		echo "<a href=\"admin_edit_servers.php\">&raquo; Back to Administration screen - Edit server settings</a><br>";
	}

	include("design_foot.php");
?>	