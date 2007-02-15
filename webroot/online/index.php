<?
	include_once("functions/include_all.php");
	include("design_head.php");

	$list = getNews($db);
	
	for ($i=0; $i<count($list); $i++) {

		echo "<table width=\"100%\" cellpadding=0 cellspacing=1 border=0 bgcolor=#707070><tr><td>";
			echo "<table width=\"100%\" cellpadding=0 cellspacing=2 border=0 bgcolor=#A0A0A0><tr><td>";
				echo "<b>".$list[$i]["subject"]."</b>, ".date($long_date, $list[$i]["timestamp"]);
				if ($_SESSION["superUser"]) {
					echo " <a href=\"admin_delete_news.php?id=".$list[$i]["itemId"]."\">&raquo; Delete</a>";
				}
			echo "</td></tr></table>";

			echo "<table width=\"100%\" cellpadding=0 cellspacing=2 border=0 bgcolor=#C0C0C0><tr><td>";
				echo nl2br($list[$i]["body"]);
			echo "</td></tr></table>";
		echo "</td></tr></table>";
		echo "<img src=\"gfx/blank.gif\" height=5><br>";
	}

	include("design_foot.php");
	
?>