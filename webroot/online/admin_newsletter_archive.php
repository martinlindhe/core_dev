<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	include("design_head.php");
	
	if (isset($_GET["id"])) {
		echo "<b class=\"topic\">Administration screen - Archived newsletter</b><br><br>";
		
		$item = getArchivedNewsletter($db, $_GET["id"]);
		
		echo "Date: ".date($long_date, $item["timestamp"])."<br>";
		echo "Subject: ".$item["subject"]."<br>";
		echo nl2br($item["headers"])."<br>";
		echo nl2br($item["body"])."<br>";
		
		echo "<br>";
		echo "<a href=\"".$_SERVER["PHP_SELF"]."\">&raquo; Back to Archived newsletters</a><br>";
		
	} else {
		echo "<b class=\"topic\">Administration screen - Archived newsletters</b><br><br>";

		$list = getArchivedNewsletters($db);
		for ($i=0; $i<count($list); $i++) {
			echo "<a href=\"".$_SERVER["PHP_SELF"]."?id=".$list[$i]["itemId"]."\">";
			echo date($short_date, $list[$i]["timestamp"]).": ";
			echo $list[$i]["subject"]."</a>";
			echo " (".$list[$i]["recievers"]." recipients)<br>";
		}
	
		echo "<br>";
		echo count($list)." items in list.<br><br>";
	}
	echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";

	include("design_foot.php");
?>	