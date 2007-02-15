<?
	include_once("functions/include_all.php");

	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }
	if (!isset($_GET["id"])) {
		$showId = $_SESSION["userId"];
		$showName = $_SESSION["userName"];
	} else {	
		$showId = $_GET["id"];
		$showName = getUserName($db, $showId);
	}

	include("design_head.php");
	
	echo "<h1>THIS PAGE MUST BE TOTALLY REMADE. SERVER INFO SHALL BE STORED IN A DIFFERENT DB</h1>";
	echo "<b class=\"topic\">Create new character for ".$showName."</b><br><br>";
	
	echo "Select the server the character is to be played on:<br>";
	echo "<select name=\"serverId\">";
	$list = getGameServers($db);
	for ($i=0; $i<count($list); $i++) {
		echo "<option value=\"".$list[$i]["serverId"]."\">".$list[$i]["serverName"];
	}
	echo "</select>";
	echo "<br>";
	
	echo "Select the race of the character:<br>";
	
	echo "<select>";
	for ($i=0; $i<count($raceName); $i++) {
		echo "<option value=\"".$i."\">".ucwords($raceName[$i]);
	}
	echo "</select>";
	echo "<br>";
	
	echo "Select the gender: ";
			echo "<input type=\"radio\" class=\"rbstyle\" name=\"gender\" value=\"male\"";
			if (isset($_SESSION["gender"]) && ($_SESSION["gender"] === 0)) echo " checked";
			echo ">";
			echo "<img width=40 height=48 src=\"gfx/icon_male.png\" alt=\"Male\"> ";
			echo "<input type=\"radio\" class=\"rbstyle\" name=\"gender\" value=\"female\"";
			if (isset($_SESSION["gender"]) && ($_SESSION["gender"] === 1)) echo " checked";
			echo ">";
			echo "<img width=40 height=48 src=\"gfx/icon_female.png\" alt=\"Female\">";
	echo "<br><br>";
	
	
	echo "Select a name: <input type=\"text\"><br>";
	echo "Select en inriktning (magi, botanik, krigskonst):<br>";
	
	echo "<input type=\"submit\" value=\"Continue...\">";
	
	
	//echo "Roll dices:<br>";
	

	include("design_foot.php");
?>