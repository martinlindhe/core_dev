<?
	/* Activate account */

	if (isset($_GET["c"])) {
		$code = $_GET["c"];
	} else {
		header("Location: index.php"); die;
	}
	
	include_once("functions/include_all.php");
	include("design_head.php");

	if (activateAccount($db, $code) === true) {
		echo "You account is now locked up!<br><br>";
		echo "<a href=\"login.php\">Click here</a> to log in";
	} else {
		echo "Invalid code.";
	}
	
	include("design_foot.php");
?>