<?
	include_once("functions/include_all.php");

	if ($_SESSION["loggedIn"] === false) {
		header("Location: index.php"); die;
	} else if (isset($_GET["id"])) {
		$showId = $_GET["id"];
	} else {	
		$showId = $_SESSION["userId"];
	}

	$char = getGameServerCharacterCombinedInfo($db, $showId);
	if (!$char) {
		header("Location: index.php"); die;
	}	
	
	include("design_head.php");
		
	echo "<b class=\"topic\">Character info for ".$char["charName"]."</b><br><br>";
	
	echo $char["charName"]." is a ".strtolower($genderName[$char["charGender"]])." ".ucwords($raceName[$char["charRace"]])." played by <a href=\"show_user.php?id=".$char["userId"]."\">".$char["userName"]."</a>.<br>";
	echo ucfirst($genderRefer[$char["charGender"]])." was created in ".date($long_date, $char["timeCreated"])." and was last seen played in ".date($long_date, $char["timeLastPlayed"]).".<br>";
	echo "In total ".$char["charName"]." has been played ".makeTimePeriod($char["timePlayed"])." on ".$char["playedCount"]." ". (($char["playedCount"]==1)?"occasion":"occasions").".<br>";
	echo "<br>";

	if ($char["guildId"]) {
		echo $char["charName"]." is a ".$guild_member_type[$char["guildMemberType"]]." of the guild <a href=\"show_guild.php?id=".$char["guildId"]."\">".$char["guildName"]."</a> since ".date($long_date, $char["timeJoinedGuild"]).".<br>";
	} else {
		echo $char["charName"]." is not a member of any guild.<br>";
	}
	echo "<br>";
	
	echo "STR: ".$char["charSTR"]."<br>";
	echo "DEX: ".$char["charDEX"]."<br>";
	echo "CON: ".$char["charCON"]."<br>";
	echo "INT: ".$char["charINT"]."<br>";
	echo "WIS: ".$char["charWIS"]."<br>";
	echo "CHA: ".$char["charCHA"]."<br>";

	include("design_foot.php");
?>