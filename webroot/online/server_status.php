<?
	include_once("functions/include_all.php");
	include("design_head.php");

	echo "<h1>THIS PAGE MUST BE TOTALLY REMADE. SERVER INFO SHALL BE STORED IN A DIFFERENT DB</h1>";
	echo "<b class=\"topic\">Server status</b><br>";
	echo "Here you can see the current status of the game servers,<br>";
	echo "and also planned upcoming downtimes.<br>";
	echo "If the servers are down or you have problems logging in despite what<br>";
	echo "is said on this page, we apologize.<br>";
	echo "You may contact us at ".SUPPORT_MAIL_HTML." for further information.<br>";
	echo "<br>";


	echo "<b class=\"topic\">Current status</b><br>";
	
	$current = getCurrentServerDowntime($db);
	if (!$current) {
		echo "Up and running.";
	} else {
		echo $current;
	}
	echo "<br><br>";
	
	echo "<b class=\"topic\">Scheduled downtimes</b><br>";
	$list = getServerDowntimes($db);
	for ($i=0; $i<count($list); $i++) {
		echo date($short_date, $list[$i]["timestamp"]).": ";
		echo $list[$i]["info"]."<br>";
	}
	if ($_SESSION["superUser"]) {
		echo "<a href=\"admin_add_downtime.php\">&raquo; Add scheduled server downtime</a><br>";
	}
	
	echo "<br>";
	echo "<b class=\"topic\">Server information</b><br>";
	$list = getGameServers($db);
	for ($i=0; $i<count($list); $i++) {
		echo "<a href=\"server_info.php?id=".$list[$i]["serverId"]."\">".$list[$i]["serverName"]."</a><br>";
	}

	include("design_foot.php");
?>