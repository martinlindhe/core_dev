<?
	include_once("functions/include_all.php");
	if (!$_SESSION["superUser"]) { header("Location: index.php"); die; }

	include("design_head.php");

	echo "<b class=\"topic\">Administration screen</b><br>";
	echo "From here you can do most administrative tasks. One important daily routine<br>";
	echo "would be to go through the bug reports (if any) and move them to<br>";
	echo "the TODO-system/close them.<br>";
	echo "Also familiarize yourself with the TODO-system. If you figured you can't handle a PR<br>";
	echo "you've assigned to yourself, you'd better unassign it, so someone else sees that nobody<br>";
	echo "is working on it, and can pick it up.<br><br>";

	echo "<a href=\"admin_add_news.php\">&raquo; Add news</a><br>";
	echo "<br>";
	
	echo "<a href=\"admin_delete_user.php\">&raquo; Delete user</a><br>";
	echo "<a href=\"admin_inactive_users.php\">&raquo; List inactive users</a><br>";
	echo "<br>";

	echo "<a href=\"admin_add_downtime.php\">&raquo; Add scheduled server downtime</a><br>";
	echo "<a href=\"admin_siteinfo.php\">&raquo; Show site information</a><br>";
	echo "<a href=\"admin_edit_servers.php\">&raquo; Edit server settings</a><br>";
	echo "<a href=\"dbadmin/\">&raquo; Access database (phpMyAdmin)</a><br>";
	echo "<br>";

	echo "<a href=\"admin_create_contentcodes.php\">&raquo; Create new content codes</a><br>";
	echo "<a href=\"admin_contentcode_log.php\">&raquo; Show content code attempt log</a><br>";
	echo "<br>";

	echo "<a href=\"admin_send_newsletter.php\">&raquo; Send newsletter</a><br>";
	echo "<a href=\"admin_newsletter_archive.php\">&raquo; Show archived newsletters</a><br>";
	echo "<br>";

	$cntbugs = getBugReportsCount($db);
	if ($cntbugs) echo "<b>";
	echo "<a href=\"admin_bug_reports.php\">&raquo; Show bug reports</a> (".$cntbugs." unclosed)";
	if ($cntbugs) echo "</b>";
	echo "<br>";
	echo "<a href=\"admin_todo_lists.php\">&raquo; Show TODO lists</a> (".getTodoItemsCount($db)." unclosed)<br>";
	$cntassigned = getAssignedTasksCount($db, $_SESSION["userId"]);
	if ($cntassigned) echo "<b>";
	echo "<a href=\"admin_assigned_tasks.php\">&raquo; Show your assigned tasks</a> (".$cntassigned." unclosed)";
	if ($cntassigned) echo "</b>";
	echo "<br><br>";

	echo "<table cellpadding=0 cellspacing=0 border=0>";
	echo "<form name=\"lookuppr\" method=\"post\" action=\"admin_lookup_pr.php\">";
	echo "<tr><td>&raquo; Lookup PR:</td><td><input type=\"text\" name=\"pr\" size=6> <input type=\"submit\" value=\"Go\"></td></tr>";
	echo "</form>";
	echo "<form name=\"lookupuser\" method=\"post\" action=\"lookup_user.php\">";
	echo "<tr><td>&raquo; Lookup user:&nbsp;</td><td><input type=\"text\" name=\"user\" size=15> <input type=\"submit\" value=\"Go\"></td></tr>";
	echo "</form>";
	echo "</table>";
	echo "<br>";

	echo "Note: You can delete news directly from the news page.<br>";

	include("design_foot.php");
?>