<?
	include_once("functions/include_all.php");

	if (!isset($_GET["id"]) || !$_GET["id"]) {
		header("Location: index.php"); die;
	}
	$timezoneId = $_GET["id"];

	include("design_head.php");
	
	echo "<b class=\"topic\">Users in timezone GMT ".$timezone[$timezoneId]["gmt"].", ".$timezone[$timezoneId]["name"]."</b><br><br>";
	
	echo "The local time in GMT ".$timezone[$timezoneId]["gmt"]." is ".date($long_date, makeLocalTime($timezone[$timezoneId]["gmt"]) )."<br><br>";

	$list = getUsersByTimezone($db, $timezoneId);
	for ($i=0; $i<count($list); $i++) {
		echo "<a href=\"show_user.php?id=".$list[$i]["userId"]."\">".$list[$i]["userName"]."</a> ";
		echo "(<a href=\"show_users_by_city.php?id=".$list[$i]["adrCountry"]."&city=".urlencode($list[$i]["adrCity"])."\">".$list[$i]["adrCity"]."</a>, ";
		echo "<a href=\"show_users_by_country.php?id=".$list[$i]["adrCountry"]."\">".$list[$i]["countryName"]."</a>, ";
		echo "<a href=\"show_users_by_timezone.php?id=".$list[$i]["timezone"]."\">GMT ".$timezone[$list[$i]["timezone"]]["gmt"]."</a>)<br>";
	}

	echo "<br>";
	echo count($list)." users in list.<br>";
	
	include("design_foot.php");
?>