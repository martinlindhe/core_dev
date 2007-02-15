<?
	include_once("functions/include_all.php");	

	if (!isset($_SESSION["username"])) {
		/* Initialize session */
		$_SESSION["username"]  = "";
		$_SESSION["password"]  = "";
		$_SESSION["email"]     = "";
		$_SESSION["country"]   = "";
		$_SESSION["gender"]    = "";
		$_SESSION["realname"]  = "";
		$_SESSION["city"]      = "";
		$_SESSION["street"]	   = "";
		$_SESSION["zipcode"]   = "";
		$_SESSION["timezone"]  = "";
		$_SESSION["phone"]	   = "";
		$_SESSION["hideemail"] = "";
		$_SESSION["newsletter"]= "1";
	}

	if (isset($_POST["step"])) {

		$error = "";
		
		/* Register username */
		if (isset($_POST["username"]) && !$_SESSION["username"]) {
			$_SESSION["newUserId"] = registerUsername($db, $_POST["username"]);
			if ($_SESSION["newUserId"] === false) {
				if ($_POST["username"]) {
					$error .= "<li><b>Username ".$_POST["username"]." taken</b><br>";
				}
			} else {
				$_SESSION["username"] = $_POST["username"];
			}
		}

		/* Verify password */
		if (isset($_POST["password1"])	&& isset($_POST["password2"]) && !$_SESSION["password"]) {
			if ($_SESSION["username"]) {
				$pwd_check = verifyPassword($_POST["password1"], $_POST["password2"], $_SESSION["username"]);
			} else {
				$pwd_check = verifyPassword($_POST["password1"], $_POST["password2"], $_POST["username"]);
			}
			
			if ($pwd_check === true) {
				$_SESSION["password"] = $_POST["password1"];
			} else {
				$error .=  "<li><b>".$pwd_check."</b><br>";
			}
		}
		
		/* Verify email */
		if (isset($_POST["email"]) && !$_SESSION["email"]) {
			
			$mail_check = verifyEmail($_POST["email"]);
			if ($mail_check === true) {
				if (isFreeEmail($db, $_POST["email"])=== true) {
					$_SESSION["email"] = $_POST["email"];
				} else {
					$error .= "<li><b>A user with this email is already registered!</b><br>";
				}
			} else {
				$error .=  "<li><b>".$mail_check."</b><br>";
			}
		}

		if (isset($_POST["country"])) {
			$_SESSION["country"] = $_POST["country"];
			if ($_POST["country"] == 0) {
				$error .= "<li><b>Please select a country</b><br>";
			}
		}
		
		if (!isset($_POST["agree"]) || !$_POST["agree"]) {
			$error .= "<li><b>You have to agree to the Terms and Conditions before you can sign up</b><br>";
		}
		
		
		if (isset($_POST["gender"])) {
			if ($_POST["gender"] == "female")
				$_SESSION["gender"]	= 1;
			else
				$_SESSION["gender"]	= 0;
		}

		if (isset($_POST["realname"])	) $_SESSION["realname"]	= $_POST["realname"];
		if (isset($_POST["city"])		) $_SESSION["city"]		= $_POST["city"];
		if (isset($_POST["street"])		) $_SESSION["street"]	= $_POST["street"];
		if (isset($_POST["zipcode"])	) $_SESSION["zipcode"]	= $_POST["zipcode"];
		
		if (isset($_POST["timezone"])	) $_SESSION["timezone"] = $_POST["timezone"];
		if (isset($_POST["phone"])		) $_SESSION["phone"]	= $_POST["phone"];
		if (isset($_POST["hideemail"])  ) $_SESSION["hideemail"]= $_POST["hideemail"];
		if (isset($_POST["newsletter"]) ) $_SESSION["newsletter"]= $_POST["newsletter"];
		
		
		/* Everything is eventually filled out properly and we may proceed to Step 2 */
		if (
			isset($_SESSION["username"]) && isset($_SESSION["password"]) && isset($_SESSION["email"])   &&
			isset($_SESSION["realname"]) && isset($_SESSION["gender"])   && isset($_SESSION["country"]) &&
			isset($_SESSION["city"])     && isset($_SESSION["street"])   && isset($_SESSION["zipcode"]) &&
			isset($_SESSION["timezone"]) && isset($_SESSION["phone"])    && isset($_SESSION["hideemail"]) &&
			isset($_SESSION["newsletter"]) &&
			$_SESSION["city"] && $_SESSION["street"] && $_SESSION["zipcode"] ) {
				
				if (!isset($error) || !$error) {
					header("Location: register_continued.php"); die;
				}
		}
	}
	
	include("design_head.php");
	
	$r_hostname = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
	if ($_SERVER["REMOTE_ADDR"] != $r_hostname) {
		/* We got a address */
		$pos = strrpos($r_hostname, ".");
		$end = substr($r_hostname, $pos+1);
		
		if ($end) {
			$c = getCountryBySuffix($db, $end);
			$user_timezone = $c["timezoneId"];
			$user_country  = $c["countryId"];
		}
	}


	/* Step 1 - Get username suggestion & contact info */
	echo "<b class=\"topic\">Register new user</b><br>";
	echo "All fields are required in this form.<br>";
	echo "Your e-mail will NEVER be used to spam you or given away to a 3rd party.<br>";
	echo "We will only need it to confirm we have a way to contact you, for password<br>";
	echo "retrival, and for the news letter (optional of course).<br><br>";

	if (isset($error) && $error) {
		echo "<font color=\"red\">One or more errors occured, they are:<br>";
		echo $error;
		echo "</font><br>";
	}
	echo "<table width=430 cellpadding=0 cellspacing=0 border=0>";
	echo "<form name=\"step1\" method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
	echo "<input type=\"hidden\" name=\"step\" value=\"1\">";
	echo "<tr>";
		echo "<td>Wanted username:</td>";
		echo "<td>";
			if ($_SESSION["username"]) {
				echo "<b>".$_SESSION["username"]."</b> (accepted)";
			} else {
				echo "<input type=\"text\" name=\"username\">";
			}
		echo "</td>";
	echo "</tr>";

	if ($_SESSION["password"]) {
		echo "<tr><td>Password:</td><td>".$_SESSION["password"]." (accepted)</td></tr>";
	} else {
		echo "<tr><td>Password:</td><td><input type=\"password\" name=\"password1\"></td></tr>";
		echo "<tr><td>Repeat password:</td><td><input type=\"password\" name=\"password2\"></td></tr>";
	}
	if ($_SESSION["email"]) {
		echo "<tr><td>Email:</td><td><b>".$_SESSION["email"]."</b> (accepted)</td></tr>";
	} else {
		echo "<tr><td>Email:</td><td><input type=\"text\" name=\"email\"></td></tr>";
	}
	
	echo "<input type=\"hidden\" name=\"hideemail\" value=\"0\">";
	echo "<tr><td>Hide email:</td><td><input type=\"checkbox\" class=\"cbstyle\" value=\"1\" name=\"hideemail\"".($_SESSION["hideemail"]?" checked":"")."></td></tr>";

	echo "<input type=\"hidden\" name=\"newsletter\" value=\"0\">";
	echo "<tr><td>Newsletter:</td><td><input type=\"checkbox\" class=\"cbstyle\" value=\"1\" name=\"newsletter\"".($_SESSION["newsletter"]?" checked":"")."></td></tr>";

	echo "<tr><td>Real name:</td>";
		echo "<td><input type=\"text\" name=\"realname\"";
		if (isset($_SESSION["realname"])) echo "value=\"".$_SESSION["realname"]."\"";
		echo "></td>";
	echo "</tr>";
	
	echo "<tr><td>Gender:</td>";
		echo "<td>";
			echo "<input type=\"radio\" class=\"rbstyle\" name=\"gender\" value=\"male\"";
			if (isset($_SESSION["gender"]) && ($_SESSION["gender"] === 0)) echo " checked";
			echo ">";
			echo "<img width=40 height=48 src=\"gfx/icon_male.png\" alt=\"Male\"> ";
			echo "<input type=\"radio\" class=\"rbstyle\" name=\"gender\" value=\"female\"";
			if (isset($_SESSION["gender"]) && ($_SESSION["gender"] === 1)) echo " checked";
			echo ">";
			echo "<img width=40 height=48 src=\"gfx/icon_female.png\" alt=\"Female\">";
		echo "</td>";
	echo "</tr>";

	echo "<tr><td>Phone number:</td>";
		echo "<td><input type=\"text\" name=\"phone\" value=\"".$_SESSION["phone"]."\"></td>";
	echo "</tr>";

	echo "<tr><td>Country:</td>";
		echo "<td>";
			echo "<select name=\"country\">";
			$list = getCountries($db);
			echo "<option value=\"0\">- Select from the list -";
			for ($i=0; $i<count($list); $i++) {
				echo "<option value=\"".$list[$i]["countryId"]."\"";
				if ($_SESSION["country"]) {
					if ($_SESSION["country"] == $list[$i]["countryId"]) echo " selected";
				} else if (isset($user_country) && $user_country) {
					if ($user_country == $list[$i]["countryId"]) echo " selected";
				}
				echo ">".$list[$i]["countryName"];
			}
			echo "</select>";
		echo "</td>";
	echo "</tr>";
	
	echo "<tr><td>Time zone:</td>";
		echo "<td>";
		echo "<select name=\"timezone\">";
			echo "<option>- Select from the list -";
			for ($i=0; $i<count($timezone); $i++) {
				echo "<option value=\"".$i."\"";
				if (is_numeric($_SESSION["timezone"])) {
					if ($i == $_SESSION["timezone"]) echo " selected";
				} else if (isset($user_timezone)) {
					if ($user_timezone == $i) echo " selected";
				}
				echo ">".$timezone[$i]["gmt"].": ".$timezone[$i]["name"];
			}
		echo "</select>";
		echo "</td>";
	echo "</tr>";
	
	echo "<tr><td>City:</td>";
		echo "<td><input type=\"text\" name=\"city\" value=\"".$_SESSION["city"]."\"></td>";
	echo "</tr>";
	echo "<tr><td>Street:</td>";
		echo "<td><input type=\"text\" name=\"street\" value=\"".$_SESSION["street"]."\"></td>";
	echo "</tr>";
	echo "<tr><td>Zipcode:</td>";
		echo "<td><input type=\"text\" name=\"zipcode\" value=\"".$_SESSION["zipcode"]."\"></td>";
	echo "</tr>";
	
	
	echo "<tr><td colspan=2><br><input type=\"checkbox\" class=\"cbstyle\" value=\"1\" name=\"agree\"> I agree to the <a href=\"conditions.php\">Terms and Conditions</a>.<br>";
	
	echo "<tr><td colspan=2><br><input type=\"submit\" value=\"Register\"></td></tr>";
	echo "</form>";
	echo "</table>";
	
	
	include("design_foot.php");
	
?>