<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	include("design_head.php");
	
	echo "<b class=\"topic\">Administration screen - List inactive users</b><br>";
	
	
	if (isset($_GET["span"])) {
		echo "<br>";
		$list = getInactiveUsers($db, $_GET["span"]);
		for ($i=0; $i<count($list); $i++) {
			echo $list[$i]["userName"]."<br>";
		}
		echo "<br>";
		echo count($list)." inactive users.<br>";
		
	} else {
		echo "Click below to list all users that has not been logged in for the ammount of time specified.<br>";
		echo "We should never delete inactive users that has been inactive less than 6 months.<br><br>";
		echo "<b> WE SHOULD NOT USE THIS SHIT, INSTEAD DELETE USERS FROM THE VALUE OF tblUsers.accountExpires !!!!<br><br>";
		
		echo "<a href=\"admin_inactive_users.php?span=1\">1 month</a><br>";
		echo "<a href=\"admin_inactive_users.php?span=3\">3 months</a><br>";
		echo "<a href=\"admin_inactive_users.php?span=6\">6 months</a><br>";
		echo "<a href=\"admin_inactive_users.php?span=12\">1 year</a><br>";
	}
	
	echo "<br>";
	echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";
	
	
	include("design_foot.php");
?>