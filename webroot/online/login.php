<?
	include_once("functions/include_all.php");

	/* Fetch post data and tries to log in user */
	if (isset($_POST["username"]) && isset($_POST["password"])) {
		$_SESSION["username"] = $_POST["username"];

		setcookie("username", $_POST["username"], time()+((3600*24)*30));
		$temp = userLogIn($db, $_SESSION["username"], $_POST["password"]);
		if ($temp === false) {
			$_SESSION["failedLogin"] = true;
		} else {
			$_SESSION["loggedIn"] = true;
			$_SESSION["userId"] = $temp;
			$_SESSION["userName"] = trim($_POST["username"]);
			$_SESSION["superUser"] = isSuperUser($db, $_SESSION["userId"]);
		}
		unset($temp);
	}

	if ($_SESSION["loggedIn"] == true) {
		header("Location: index.php");
		die;
	}		
		
	include("design_head.php");

	echo "<b class=\"topic\">Log in screen</b><br><br>";
		
	if (isset($_SESSION["failedLogin"]) && ($_SESSION["failedLogin"] === true)) {
		echo "<font color=red>";
		echo "The login failed!</font><br>";
		echo "<br>";
		echo "Please make sure that you entered the username and password correctly,<br>";
		echo "also check that you didn't have CAPS-LOCK turned on by accident.<br> ";
		echo "Failure to log in can also occur if you have not yet activated your account,<br>";
		echo "so check your e-mail after activation instructions.<br><br>";
		
		$_SESSION["failedLogin"] = false;
	}
	echo "<table cellpadding=0 cellspacing=0 border=0>";
	echo "<form method=\"post\" name=\"login\" action=\"".$_SERVER["PHP_SELF"]."\">";
	echo "<tr><td>Username:&nbsp;</td><td><input type=\"text\" name=\"username\"";
	$fillu=0;
	if (isset($_SESSION["username"])) { echo "value=\"".$_SESSION["username"]."\""; $fillu = 1; }
	else if (isset($_COOKIE["username"])) { echo "value=\"".$_COOKIE["username"]."\""; $fillu = 1; }
	echo "></td></tr>";
	echo "<tr><td>Password:</td><td><input type=\"password\" name=\"password\"></td></tr>";
	echo "<tr><td colspan=2><br><input type=\"submit\" value=\"Log in\"></td></tr>";
	echo "</form></table>";
		
	echo "<br>";
	echo "Did you forget your password?<br><a href=\"lost_password.php\">Click here</a> to have it sent to your e-mail account!<br>";
		
	echo "<script type=\"text/javascript\">\n<!--\n";
	if ($fillu == 1) {
		echo "document.login.password.focus();\n";
	} else {
		echo "document.login.username.focus();\n";
	}
	echo "//-->\n</script>";
	
	include("design_foot.php");

?>