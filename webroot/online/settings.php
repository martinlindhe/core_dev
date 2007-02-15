<?
	include_once("functions/include_all.php");
	if ($_SESSION["loggedIn"] != true) {
		header("Location: index.php"); die;
	}
	include("design_head.php");
	
	if ($_SESSION["superUser"] && isset($_GET["id"])) {
		$showId   = $_GET["id"];
		$showName = getUserName($db, $showId);
		if (!$showName) {
			header("Location: index.php"); die;
		}
	} else {
		$showId   = $_SESSION["userId"];
		$showName = $_SESSION["userName"];
	}
	
	
	echo "<b class=\"topic\">Settings for ".$showName."</b><br>";
	echo "Here you see your personal, contact and billing information.<br>";
	echo "You can also edit your information from here.<br><br>";

	$contact = getUserContactInfo($db, $showId);
	echo "<b class=\"topic\">Contact information</b><br><br>";
	echo "<table cellpadding=0 cellspacing=0 border=0>";
	echo "<tr><td width=110>Real name:</td><td>".$contact["realName"]."</td></tr>";
	echo "<tr><td>Gender:</td><td>".   $genderName[  $contact["gender"] ]."</td></tr>";
	echo "<tr><td>Phone number:&nbsp;</td><td>".$contact["adrPhoneHome"]."</td></tr>";
	echo "<tr><td>Street:</td><td>".   $contact["adrStreet"]."</td></tr>";
	echo "<tr><td>Zip code:</td><td>". $contact["adrZipcode"]."</td></tr>";
	echo "<tr><td>City:</td><td>".     $contact["adrCity"]."</td></tr>";
	echo "<tr><td>Country:</td><td>".  getCountryName($db, $contact["adrCountry"])."</td></tr>";
	echo "<tr><td>Timezone:</td><td>". $timezone[$contact["timezone"]]["gmt"].": ".$timezone[$contact["timezone"]]["name"]."</td></tr>";
	echo "<tr><td>E-mail:</td><td>".   $contact["userMail"]."</td></tr>";
	echo "<tr><td>Hide e-mail:</td><td>". ($contact["userMailSecret"] ? "Yes" : "No") ."</td></tr>";
	echo "<tr><td>Newsletter:</td><td>". ($contact["newsletter"] ? "Yes" : "No") ."</td></tr>";
	echo "</table>";
	echo "<a href=\"edit_settings.php?id=".$showId."&contact\">&raquo; Edit contact information</a><br>";
	echo "<br>";

	$cc = getUserCCInfo($db, $showId);
	echo "<b class=\"topic\">Billing information</b><br>";
	if ($cc["ccNumber"]) {
		echo "<table cellpadding=0 cellspacing=0 border=0>";
		echo "<tr><td width=110>Number:</td><td>".CCprintNumber($cc["ccNumber"])." (".CCgetTypeName($cc["ccNumber"]).")</td></tr>";
		echo "<tr><td>Expires:</td><td>".$month_long[$cc["ccExpireMonth"]]." ".$cc["ccExpireYear"]."</td></tr>";
		echo "<tr><td>Billing name:</td><td>".$cc["ccOwnerName"]."</td></tr>";
		echo "<tr><td>Extra code:</td><td>".$cc["ccExtraCode"]."</td></tr>";
		echo "</table>";
	} else {
		echo "No billing information entered.<br>";
	}
	echo "<a href=\"edit_settings.php?id=".$showId."&billing\">&raquo; Edit billing information</a><br>";
	echo "<br>";	

	$stats = getUserStats($db, $showId);
	echo "<b class=\"topic\">User stats</b><br>";
	echo "<table cellpadding=0 cellspacing=0 border=0>";
	echo "<tr><td width=110>Account created:</td><td>". date($long_date, $stats["timeCreated"])."</td></tr>";
	echo "<tr><td>Account activated:&nbsp;</td><td>". 	date($long_date, $stats["timeActivated"])."</td></tr>";
	echo "<tr><td>Account expires(*):</td><td>".		date($long_date, $stats["timeExpires"])."</td></tr>";
	echo "<tr><td>Last login (website):</td><td>".		date($long_date, $stats["timeLastLogin"])."</td></tr>";
	echo "<tr><td>Times logged in (website):</td><td>".	$stats["cntLogins"]."<br>";
	echo "</table>";
	echo "* = This indicates when your access to the game expires, not your access to the website.<br>";
	echo "You can unlock more gaming time with content codes, see below.<br>";
	echo "<br>";
	
	echo "<a href=\"edit_settings.php?id=".$showId."&password\">&raquo; Change password</a><br>";
	echo "<a href=\"edit_settings.php?id=".$showId."&code\">&raquo; Enter content code</a><br>";

	include("design_foot.php");
?>