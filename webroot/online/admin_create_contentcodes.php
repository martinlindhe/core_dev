<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	include("design_head.php");

	echo "<b class=\"topic\">Administration screen - Create content codes</b><br>";
	
	if (isset($_POST["ammount"]) && isset($_POST["months"])) {
		createContentCodes($db, $_POST["ammount"], $_POST["months"]);
		
		echo "<br>";
		echo "Created ".$_POST["ammount"]." content codes, with credit for ".$_POST["months"]." months<br>";
		echo "<br>";
		echo "<a href=\"".$_SERVER["PHP_SELF"]."\">&raquo; Back to Create content codes</a><br>";
		echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";		
		include("design_foot.php");
		die;
	}

	echo "Content codes are given/sold to users, they pays the bill for a couple of months,<br>";
	echo "and they have expiration dates aswell (2 years from creation time).<br>";
	echo "When a code has been used it's used up, and that particular code can not<br>";
	echo "be used again.<br>";
	echo "Please dont create more content codes than needed. 100 give or take should be<br>";
	echo "more than enough now in the beginning.<br><br>";
	
	
	$stats = getContentCodeStats($db);
	echo "<table width=250 cellpadding=0 cellspacing=0 border=0>";
	echo "<tr><td width=100><b>In database</b></td><td>USED</td><td>UNUSED</td></tr>";
	echo "<tr><td>1 month codes:</td><td>".$stats[1]["used"]."</td><td>".$stats[1]["unused"]."</td></tr>";
	echo "<tr><td>3 month codes:</td><td>".$stats[3]["used"]."</td><td>".$stats[3]["unused"]."</td></tr>";
	echo "<tr><td>6 month codes:</td><td>".$stats[6]["used"]."</td><td>".$stats[6]["unused"]."</td></tr>";
	echo "</table>";
	echo "<br>";
	

	echo "<table cellpadding=0 cellspacing=0 border=0>";
	echo "<form method=\"post\" action=\"".$_SERVER["PHP_SELF"]."\">";
	echo "<tr><td>";
	echo "Create ";
	echo "<select name=\"ammount\">";
	echo "<option value=\"10\">10";
	echo "<option value=\"100\">100";
	echo "<option value=\"500\">500";
	echo "</select>";
	echo " content codes, with credit for ";
	echo "<select name=\"months\">";
	echo "<option value=\"1\">1 month";
	echo "<option value=\"3\">3 months";
	echo "<option value=\"6\">6 months";
	echo "</select> ";
	echo "<input type=\"submit\" value=\"Go\">";
	echo "</td></tr>";
	echo "</form>";
	echo "</table>";
	
	echo "<br>";
	echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";
	
	include("design_foot.php");
?>