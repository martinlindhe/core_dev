<?
	include_once("functions/include_all.php");
	if (!$_SESSION["loggedIn"]) {
		header("Location: index.php"); die;
	}
	if (!isset($_GET["contact"]) && !isset($_GET["billing"]) && !isset($_GET["password"]) && !isset($_GET["code"])) {
		header("Location: settings.php"); die;
	}

	include("design_head.php");

	if ($_SESSION["superUser"] && isset($_GET["id"])) {
		$showId   = $_GET["id"];
		$showName = getUserName($db, $showId);
	} else {
		$showId   = $_SESSION["userId"];
		$showName = $_SESSION["userName"];
	}

	
	if (isset($_GET["contact"])) {
		
		if (isset($_POST["email"])) {
			/* Save changes */
			$errorcode = updateUserContactInfo($db, $showId, $_POST["email"], $_POST["hideemail"], $_POST["newsletter"], $_POST["phone"], $_POST["street"], $_POST["zipcode"], $_POST["city"], $_POST["country"], $_POST["timezone"]);
			if ($errorcode === true) {
				echo "Changes saved successfully!<br><br>";
			} else {
				echo "Error saving changes: <font color=\"red\">".$errorcode."</font><br><br>";
			}
		}

		echo "<b class=\"topic\">Edit contact information - ".$showName."</b><br>";
		echo "Here you edit your contact information. Please make sure to check 'Hide e-mail'<br>";
		echo "if you don't want other users to see your mail address.<br>";
		echo "If you change your e-mail address, your account will be locked until you re-activate<br>";
		echo "it through a mail that will be sent to your new address.<br><br>";
		
		$contact = getUserContactInfo($db, $showId);
		
		echo "<table cellpadding=0 cellspacing=0 border=0>";
		echo "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?id=".$showId."&contact\">";

		echo "<tr><td>Real name:</td><td>".$contact["realName"]."</td></tr>";
		echo "<tr><td>Gender:</td><td>".$genderName[ $contact["gender"] ]."</td></tr>";
		echo "<tr><td>Phone number:&nbsp;</td><td><input type=\"text\" size=30 name=\"phone\" value=\"".$contact["adrPhoneHome"]."\"></td></tr>";
		echo "<tr><td>Street:</td><td><input type=\"text\" name=\"street\" size=30 value=\"".$contact["adrStreet"]."\"></td></tr>";
		echo "<tr><td>Zip code:</td><td><input type=\"text\" name=\"zipcode\" size=30 value=\"".$contact["adrZipcode"]."\"></td></tr>";
		echo "<tr><td>City:</td><td><input type=\"text\" name=\"city\" size=30 value=\"".$contact["adrCity"]."\"></td></tr>";
		echo "<tr><td>Country:</td><td>";
			$list = getCountries($db);
			echo "<select name=\"country\">";
			for ($i=0; $i<count($list); $i++) {
				echo "<option value=\"".$list[$i]["countryId"]."\"";
				if ($contact["adrCountry"] == $list[$i]["countryId"]) echo " selected";
				echo ">".$list[$i]["countryName"];
			}
			echo "</select></td></tr>";

		echo "<tr><td>Timezone:</td><td>";
		echo "<select name=\"timezone\">";
			for ($i=0; $i<count($timezone); $i++) {
				echo "<option value=\"".$i."\"";
				if ($contact["timezone"] == $i) echo " selected";
				echo ">".$timezone[$i]["gmt"].": ".$timezone[$i]["name"];
			}
		echo "</select></td></tr>";

		echo "<tr><td>E-mail:</td><td><input type=\"text\" name=\"email\" size=30 value=\"".$contact["userMail"]."\"></td></tr>";
		echo "<input type=\"hidden\" name=\"hideemail\" value=\"0\">";
		echo "<tr><td>Hide e-mail:</td><td><input type=\"checkbox\" name=\"hideemail\" value=\"1\" ". ($contact["userMailSecret"] ? "checked":"") ."></td></tr>";
		echo "<input type=\"hidden\" name=\"newsletter\" value=\"0\">";
		echo "<tr><td>Newsletter:</td><td><input type=\"checkbox\" name=\"newsletter\" value=\"1\" ". ($contact["newsletter"] ? "checked":"") ."></td></tr>";

		echo "<tr><td colspan=2><br><input type=\"submit\" value=\"Save changes\"></td></tr>";
		echo "</form></table><br>";
		
		echo "<a href=\"settings.php?id=".$showId."\">&raquo; Back to settings</a><br>";
		
	} else if (isset($_GET["billing"])) {
		
		if (isset($_POST["cc"])) {
			/* Save changes */
			$errorcode = updateBillingInformation($db, $showId, $_POST["cc"], $_POST["month"], $_POST["year"], $_POST["ownername"], $_POST["extracode"]);
			if ($errorcode === true) {
				echo "Changes saved successfully!<br><br>";
			} else {
				echo "Error: <font color=\"red\">".$errorcode."</font><br><br>";
			}
		}

		echo "<b class=\"topic\">Edit billing information - ".$showName."</b><br>";
		echo "This information is sent to our database through a encrypted SSL-connection.<br>";
		echo "The connection between you and the web server is also encrypted with SSL for maximum security.<br>";
		echo "<a href=\"\">Click here</a> for more information about the extra code.<br><br>";
		
		$cc = getUserCCInfo($db, $showId);

		echo "<table cellpadding=0 cellspacing=0 border=0>";
		echo "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?id=".$showId."&billing\">";
		echo "<tr><td>Credit card:</td><td><input size=40 type=\"text\" name=\"cc\" value=\"".CCprintNumber($cc["ccNumber"])."\"></td></tr>";
		echo "<tr><td>Expires:</td><td>";
		echo "<select name=\"month\">";
			echo "<option>";
			for ($i=1; $i<=12; $i++) {
				echo "<option value=\"".$i."\"";
				if ($i == $cc["ccExpireMonth"]) echo " selected";
				echo ">".$month_long[$i];
			}
		echo "</select> ";
		
		echo "<select name=\"year\">";
			echo "<option>";
			$curr_year = date("Y");
			for ($i=$curr_year; $i<$curr_year+10; $i++) {
				echo "<option value=\"".$i."\"";
				if ($i == $cc["ccExpireYear"]) echo " selected";
				echo ">".$i;
			}
		echo "</select></td></tr>";

		echo "<tr><td>Billing name:&nbsp;</td><td><input size=40 type=\"text\" name=\"ownername\" value=\"".$cc["ccOwnerName"]."\"></td></tr>";
		echo "<tr><td>Extra code:</td><td><input size=11 type=\"text\" name=\"extracode\" value=\"".$cc["ccExtraCode"]."\"></td></tr>";
		echo "<tr><td colspan=2><br><input type=\"submit\" value=\"Save changes\"></td></tr>";
		echo "</form>";
		echo "</table><br>";
		
		echo "<a href=\"settings.php?id=".$showId."\">&raquo; Back to settings</a><br>";

	} else if (isset($_GET["password"])) {

		if (isset($_POST["old"])) {
			/* Save new password */
			
			if (checkPassword($db, $showId, $_POST["old"])) {
			
				$errorcode = verifyPassword($_POST["new1"], $_POST["new2"], $showName);
				if ($errorcode === true) {
					//change pwd!
					setPassword($db, $showId, $_POST["new1"]);

					mailActivationCode($db, $showId);
					if (!$_SESSION["superUser"]) {
						$_SESSION=array();
						session_destroy();
						echo "Since you have changed your password, the account have been locked. Check your mail account for a mail describing how to unlock the account again.";
					} else {
						echo "Account re-activation mail has been sent to ".$showName."<br>";
					}
					include("design_foot.php");
					die;
				}
			} else {
				$errorcode = "The old password is incorrect!";
			}
			
			if (!($errorcode === true)) {
				echo "Error: <font color=\"red\">".$errorcode."</font><br><br>";
			}
		}

		echo "<b class=\"topic\">Change password - ".$showName."</b><br>";
		echo "When you have changed your password, your account will need to be reactivated trough your<br>";
		echo "e-mail address. So make sure your e-mail address is still functional before continuing.<br><br>";

		echo "<table cellpadding=0 cellspacing=0 border=0>";
		echo "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?id=".$showId."&password\">";
		echo "<tr><td>Old password:&nbsp;</td><td><input type=\"password\" size=30 name=\"old\"></td></tr>";
		echo "<tr><td>New password:&nbsp;</td><td><input type=\"password\" size=30 name=\"new1\"></td></tr>";
		echo "<tr><td>Verify:</td><td><input type=\"password\" size=30 name=\"new2\"></td></tr>";
		echo "<tr><td colspan=2><br><input type=\"submit\" value=\"Save changes\"></td></tr>";
		echo "</form>";
		echo "</table><br>";
		
		echo "<a href=\"settings.php?id=".$showId."\">&raquo; Back to settings</a><br>";

	} else if (isset($_GET["code"])) {
		echo "<b class=\"topic\">Enter content code - ".$showName."</b><br>";

		if (isset($_POST["newcode"])) {
			/* Tries to unlock the code */

			$unlocked_months = unlockContentCode($db, $showId, $_POST["newcode"]);
			if ($unlocked_months === false) {
				echo "Invalid code.<br>";
			} else {
				$expiretime = getUserExpireTime($db, $showId);
				echo "Unlocked ".$unlocked_months." months!<br>";
				echo "Your game account is now active until <b>".date($long_date, $expiretime)."</b>.<br>";
			}
			echo "<br>";
			echo "<a href=\"settings.php?id=".$showId."\">&raquo; Back to settings</a><br>";
		} else {		
			echo "A content code is a code that gives you 1, 3 or 6 months of free access to<br>";
			echo "our services, and can be bought or won from us.<br>";
			echo "Each code can only be used once.<br>";
			echo "Note: A month is always considered to be 30 days.<br><br>";
		
			echo "Enter your 12 digit content code below:<br><br>";
			echo "<table cellpadding=0 cellspacing=0 border=0>";
			echo "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."?id=".$showId."&code\">";
			echo "<tr><td><input type=\"text\" name=\"newcode\"> <input type=\"submit\" value=\"Unlock\"></td></tr>";
			echo "</form>";
			echo "</table>";
		
			echo "<br>";
			echo "<a href=\"settings.php?id=".$showId."\">&raquo; Back to settings</a><br>";
		}
	}
	
	include("design_foot.php");
?>