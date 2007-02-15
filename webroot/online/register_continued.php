<?
	/* Step 2 - Save away data, registration complete! */	
	
	include_once("functions/include_all.php");
	
	if (
		isset($_SESSION["username"]) && isset($_SESSION["password"]) && isset($_SESSION["email"])   &&
		isset($_SESSION["realname"]) && isset($_SESSION["gender"])   && isset($_SESSION["country"]) &&
		isset($_SESSION["city"])     && isset($_SESSION["street"])   && isset($_SESSION["zipcode"]) &&
		isset($_SESSION["zipcode"])  && isset($_SESSION["phone"])    && isset($_SESSION["hideemail"]) &&
		isset($_SESSION["newsletter"])
		) {

		include("design_head.php");

		if (registerUserinfo($db, $_SESSION["newUserId"], $_SESSION["password"], $_SESSION["email"], $_SESSION["hideemail"], $_SESSION["newsletter"], $_SESSION["realname"], $_SESSION["gender"], $_SESSION["street"], $_SESSION["zipcode"], $_SESSION["city"], $_SESSION["country"], $_SESSION["timezone"], $_SESSION["phone"] ) === false) {
			echo "Something went wrong with the registration!<br>";
			die;
		} else {
			echo "Registration complete!<br>";
		}
		echo "<br>";
		
		if (mailActivationCode($db, $_SESSION["newUserId"]) === false) {
			echo "Trouble mailing you!";
		} else {
			echo "Your account has been created and a email sent to the address you gave us, for verification.<br>";
			echo "Please read it to activate your account.";
		}
		$_SESSION = array();
		
		include("design_foot.php");

	} else {
		header("Location: register.php");
		die;
	}
?>