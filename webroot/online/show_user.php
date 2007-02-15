<?
	include_once("functions/include_all.php");

	if ($_SESSION["loggedIn"] === false) {
		header("Location: index.php"); die;
	} else if (!isset($_GET["id"])) {
		$showId = $_SESSION["userId"];
	} else {	
		$showId = $_GET["id"];
	}
	$contact = getUserContactInfo($db, $showId);
	if (!$contact) {
		header("Location: index.php"); die;
	}
	$showName = $contact["userName"];
	$stats = getUserStats($db, $showId);

	include("design_head.php");

	echo "<b class=\"topic\">Personal page for ".$showName."</b><br><br>";
	
	echo $showName." is from <a href=\"show_users_by_city.php?id=".$contact["adrCountry"]."&city=".urlencode($contact["adrCity"])."\">".$contact["adrCity"]."</a>, <a href=\"show_users_by_country.php?id=".$contact["adrCountry"]."\">".$contact["countryName"]."</a>.<br>";
	echo ucfirst($genderOwner[$contact["gender"]])." mail address is ";
	if ($contact["userMailSecret"]) {
		echo "SECRET.<br>";
	} else {
		echo "<a href=\"mailto:".$contact["userMail"]."\">".$contact["userMail"]."</a>.<br>";
	}

	echo ucfirst($genderOwner[$contact["gender"]])." local time is ".date($long_date, makeLocalTime($timezone[$contact["timezone"]]["gmt"]) )." (<a href=\"show_users_by_timezone.php?id=".$contact["timezone"]."\">GMT ".$timezone[$contact["timezone"]]["gmt"]."</a>)<br>";
	echo "<br>";	

	echo "<b class=\"topic\">This users' characters</b><br><br>";
	
	$list = getGameServerCharacters($db, $showId);
	for ($i=0; $i<count($list); $i++) {
		echo "<a href=\"show_character.php?id=".$list[$i]["charId"]."\">".$list[$i]["charName"]."</a><br>";
	}
	if (!count($list)) echo "None.<br>";
	echo "<br>";
	
	if ($_SESSION["superUser"]) {
		echo "<a href=\"settings.php?id=".$showId."\">&raquo; Edit this user's settings</a><br>";
		echo "<a href=\"create_character.php?id=".$showId."\">&raquo; Create a new chatacter for this user</a><br>";
	}

	include("design_foot.php");
?>