<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	include("design_head.php");
	
	echo "<b class=\"topic\">Administration screen - Closed bug reports</b><br>";
	echo "<b>OBSERVE: THESE BUG REPORTS ARE CLOSED!</b><br><br>";
	
	$list = getClosedBugReports($db);
	for ($i=0; $i<count($list); $i++) {
		echo date($long_date, $list[$i]["timestamp"]).", by ";
		
		if ($list[$i]["userName"]) {
			echo "<a href=\"show_user.php?id=".$list[$i]["bugCreator"]."\">".$list[$i]["userName"]."</a>";
		} else {
			echo "<b>removed user</b>";
		}
		
		echo " via the ".($list[$i]["reportMethod"] ? "game":"site");
		echo " (Closed because: <b>".$close_bug_reason[$list[$i]["bugClosedReason"]]."</b>)<br>";
		echo nl2br($list[$i]["bugDesc"])."<br><br>";
	}
	
	echo count($list)." CLOSED bugs in list.<br><br>";
	
	echo "<a href=\"admin_bug_reports.php\">&raquo; Back to Bug Reports</a><br>";
	echo "<a href=\"admin.php\">&raquo; Back to Administration screen</a><br>";

	include("design_foot.php");
?>