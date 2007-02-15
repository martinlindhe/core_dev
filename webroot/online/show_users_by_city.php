<?
	include_once("functions/include_all.php");

	if (!isset($_GET["id"]) || !$_GET["id"] || !isset($_GET["city"]) || !$_GET["city"]) {
		header("Location: index.php"); die;
	}
	$countryId = $_GET["id"];
	$cityName  = $_GET["city"];

	include("design_head.php");
	
	echo "<b class=\"topic\">Users from ".$cityName.", ".getCountryName($db, $countryId)."</b><br><br>";
	
	$list = getUsersByCity($db, $countryId, $cityName);
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