<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	include("design_head.php");
	
	echo "<b class=\"topic\">Administration screen - Bug reports</b><br>";
	echo "Here is a list of bug reports/feature requests made by users of the game.<br>";
	echo "The sooner these reports are dealt with the better, because users will see<br>";
	echo "that their engagement pays off.<br><br>";
	
	$list = getBugReports($db);
	for ($i=0; $i<count($list); $i++) {
		echo date($long_date, $list[$i]["timestamp"]).", by ";
		if ($list[$i]["userName"]) {
			echo "<a href=\"show_user.php?id=".$list[$i]["bugCreator"]."\">".$list[$i]["userName"]."</a>";
		} else {
			echo "<b>removed user</b>";
		}
		echo " via the ".($list[$i]["reportMethod"] ? "game":"site")."<br>";
		echo nl2br($list[$i]["bugDesc"])."<br>";
		echo "<a href=\"admin_move_bug_report.php?id=".$list[$i]["bugId"]."\">&raquo; Move this report into the TODO system</a><br>";
		echo "<a href=\"admin_close_bug_report.php?id=".$list[$i]["bugId"]."\">&raquo; Close this report</a><br><br>";
	}
	
	$closedbugs = getClosedBugReportsCount($db);
	echo count($list)." bugs in list (Excluding ".$closedbugs." CLOSED bugs).<br><br>";
	if ($closedbugs) {
		echo "<a href=\"admin_closed_bug_reports.php\">&raquo; List CLOSED bug reports</a><br>";
	}
	echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";

	include("design_foot.php");
?>