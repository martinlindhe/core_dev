<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	if (isset($_GET["id"])) {
		if (isset($_GET["on"])) {
			turnOnGameServer($db, $_GET["id"]);
		} else if (isset($_GET["off"])) {
			turnOffGameServer($db, $_GET["id"]);
		}

		if (isset($_POST["name"]) && isset($_POST["host"])) {
			updateGameServer($db, $_GET["id"], $_POST["name"], $_POST["host"]);
		}
	} else {
		if (isset($_POST["name"]) && isset($_POST["host"])) {
			addGameServer($db, $_POST["name"], $_POST["host"]);
		}
	}

	include("design_head.php");

	echo "<h1>THIS PAGE MUST BE TOTALLY REMADE. SERVER INFO SHALL BE STORED IN A DIFFERENT DB</h1>";
	echo "<b class=\"topic\">Administration screen - Edit server settings</b><br><br>";
	echo "If we need to take down a machine for service, we can simply disable it from here.<br>";
	echo "Also we can easily add new servers here. All changes here will update the servers.txt file<br>";
	echo "which the game client always fetches a fresh version of.<br><br>";

	$list = getGameServers($db);
	for ($i=0; $i<count($list); $i++) {
		echo "<a href=\"server_info.php?id=".$list[$i]["serverId"]."\">".$list[$i]["serverName"]."</a> (".$list[$i]["serverIP"].") - ".($list[$i]["serverOnline"]?"Online, <a href=\"".$_SERVER["PHP_SELF"]."?id=".$list[$i]["serverId"]."&off&update\">turn off</a>":"<b>Offline, <a href=\"".$_SERVER["PHP_SELF"]."?id=".$list[$i]["serverId"]."&on&update\">turn on</a></b>")."<br>";
	}

	if (isset($_GET["update"])) {
		/* Update live server list */
		$fp = fopen("G:/webroot/online/patcher/servers.txt", "w");
		for ($i=0; $i<count($list); $i++) {
			fputs($fp, $list[$i]["serverIP"].",".$list[$i]["serverOnline"].",".$list[$i]["serverName"]."\n");
		}
		fclose($fp);
		echo "<b>SERVER LIST HAS BEEN UPDATED</b><br>";		
	}

	echo "<br>";
	echo "<br>";

	echo "<b class=\"topic\">Add new server</b><br><br>";
	echo "<table cellpadding=0 cellspacing=0 border=0>";
	echo "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."&update\">";
	echo "<tr><td>Server name:&nbsp;</td><td><input type=\"text\" name=\"name\"></td></tr>";
	echo "<tr><td>Server IP:</td><td><input type=\"text\" name=\"host\"></td></tr>";
	echo "<tr><td colspan=2><br><input type=\"submit\" value=\"Add server\"></td></tr>";
	echo "</td></tr>";
	echo "</form>";
	echo "</table>";
	echo "<br>";

	echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";

	include("design_foot.php");
?>