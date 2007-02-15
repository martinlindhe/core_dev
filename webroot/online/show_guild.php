<?
	include_once("functions/include_all.php");

	if ($_SESSION["loggedIn"] === false) {
		header("Location: index.php"); die;
	} else if (isset($_GET["id"])) {
		$guildId = $_GET["id"];
	} else {	
		$guildId = $_SESSION["userId"];
	}

	$guild = getGameServerGuildInfo($db, $guildId);
	if (!$guild) {
		header("Location: index.php"); die;
	}	
	
	include("design_head.php");
		
	echo "<b class=\"topic\">Guild info for ".$guild["guildName"]."</b><br><br>";

	echo "This guild was created by <a href=\"show_character.php?id=".$guild["creatorId"]."\">".$guild["creatorName"]."</a> on ".date($long_date, $guild["timestamp"]).".<br>";
	echo "<br>";
	
	$list = getGameServerGuildMembers($db, $guildId);
	echo "The guild has ".count($list)." members, they are:<br><br>";	

	for ($i=0; $i<count($list); $i++) {
		echo "<a href=\"show_character.php?id=".$list[$i]["charId"]."\">".$list[$i]["charName"]."</a> (".ucfirst($guild_member_type[$list[$i]["guildMemberType"]]).", joined on ".date($long_date, $list[$i]["timeJoinedGuild"]).")<br>";
	}

	include("design_foot.php");
?>