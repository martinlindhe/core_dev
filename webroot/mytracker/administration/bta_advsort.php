<?php

	/*
	 * Module:	bta_main.php
	 * Description: This is the main screen of the administrative interface.
	 * 		It lists all the torrents and has functions that can be
	 * 		used with them.
	 *
	 * Author:	danomac
	 * Written:	14-Febrary-2004
	 *
	 * Copyright (C) 2004 danomac
	 *
	 * This program is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program; if not, write to the Free Software
	 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 */

	/*
	 * Session webserver farm check
	 */
	require_once ("../config.php");

	if (isset($GLOBALS["webserver_farm"]) && isset($GLOBALS["webserver_farm_session_path"])) {
		if ($GLOBALS["webserver_farm"] && strlen($GLOBALS["webserver_farm_session_path"]) > 0) {
			session_save_path($GLOBALS["webserver_farm_session_path"]);
		}
	}
	session_start();
	header("Cache-control: private");

	/*
	 * List of the external modules required
	 */
	require_once ("../funcsv2.php");
	require_once ("../version.php");
	require_once ("bta_funcs.php");

	/*
	 * Get the current script name. Used to build HREF strings later on.
	 */
	$scriptname = $_SERVER["PHP_SELF"];

	/*
	 * Get the client's IP address. Used for verifying access.
	 */
	$ip = str_replace("::ffff:", "", $_SERVER["REMOTE_ADDR"]);

	/*
	 * Check to make sure person is logged in, and that the session
	 * is actually theirs.
	 */
	if (!admIsLoggedIn($ip)) {
		admShowError("You can't access this page directly.",
			     "You don't appear to be logged in. Use admin/index.php to login to the administrative interface.",
			     $adm_pageerr_title);
		exit;		
	}

	/*
	 * Group admin: are they actually allowed to view this page?
	 * If not, redirect them back to main
	 */
	if (!($_SESSION["admin_perms"]["advsort"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Permissions have been set to deny you access to this page.", $adm_pageerr_title);
		exit;
	}
	
	/*
	 * Root user check - they can't be viewing all groups!
	 */
	if ($_SESSION["admin_perms"]["root"] && !isset($_SESSION["root_last_cat"])) {
		/*
		 * Boot the root user back to main to select a subcategory.
		 */
		admShowMsg("Root user error", "You have to choose a category on the main page before attempting to sort it.", "Root user error", true, "bta_main.php", 5);
	}
	
	if ($GLOBALS["dynamic_torrents"]) {
		admShowMsg("Administration error", "You cannot use the dynamic torrents setting with advanced sorting.", "Dynamic Torrent Setting error", true, "bta_main.php", 5);
	}
	
	/*
	 * Get the requested category if there is one.
	 * If a group is logged in, ignore requests and force them
	 * to view their torrents only...
	 */
	if (!isset($_SESSION["admin_perms"]["category"])) {
		/*
		 * Root user, check for selected category is above
		 */
		$defaultcategory = $_SESSION["root_last_cat"];
		$where = " WHERE namemap.category = \"" . $_SESSION["root_last_cat"] . "\"";
	} else {
		/*
		 * Okay, force them to view only their torrents...
		 */
		$where = " WHERE namemap.category = \"" . $_SESSION["admin_perms"]["category"] . "\"";
		$hrefCategory = "?category=" . $_SESSION["admin_perms"]["category"];
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<META NAME="Author" CONTENT="danomac">
<LINK REL="stylesheet" HREF="admin.css" TYPE="text/css" TITLE="Default">
<?php
	/*
	 * Set the page title.
	 */
	echo "<TITLE>". $adm_page_title . " - Advanced sorting/grouping</TITLE>\r\n";
?>
</HEAD>

<BODY>
<FORM ACTION="bta_advsort_process.php" METHOD=POST ENCTYPE="multipart/form-data">
<TABLE CLASS="tblAdminOuter">
<TR>
<?php
	/*
	 * Display the page heading.
	 */
	echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>Advanced Sorting/Grouping";
	echo "</TD>\r\n";
?>
</TR>
<?php 
	admShowURL_Login($ip);
?>
<TR>
	<TD CLASS="data" COLSPAN=15 ALIGN="center"><BR>
		Welcome to the advanced sorting screen. In this screen you can specify groups of torrents and manually sort them.<BR>
	   <A HREF="help/help_advanced_sort.php" TARGET="_blank">Need help?</A><BR>&nbsp;
	</TD>
</TR>
<TR>
	<TD COLSPAN=15 ALIGN=CENTER>
		<B>Automatic Torrent sorting</B>	
		<?php if ($_SESSION["admin_perms"]["root"]) echo "<A HREF=\"bta_advsort_process.php?action=rootsort\"><BR>Alphabetically resort torrents in ALL categories and groups (<B>tracker wide</B> - does not change the order of groups)</A><BR><A HREF=\"bta_advsort_process.php?action=rootsortall\">Alphabetically resort groups and torrents in ALL categories and groups (<B>tracker wide</B> - includes group headings)</A><BR>"; else echo "&nbsp;";?>
		<BR><A HREF="bta_advsort_process.php?action=grpsort">Alphabetically resort torrents in all groups (does not change the order of groups)</A>
		<BR><A HREF="bta_advsort_process.php?action=grpsortall">Alphabetically resort groups and torrents alphabetically (includes the group headings)</A>
		<BR><A HREF="bta_advsort_process.php?action=grponlysort">Alphabetically resort group headings only</A><HR>
		</TD>
</TR>
<TR>
	<TD CLASS="data" COLSPAN=15>&nbsp;</TD>
</TR>
<TR>
	<TD ALIGN="center" COLSPAN=15>Add a new torrent group<SUP>1</SUP>: <INPUT TYPE=TEXT NAME="addgrp" SIZE=40>&nbsp;&nbsp;<INPUT TYPE="submit" NAME="addnewgrp" VALUE="Add..." CLASS="button"><BR><BR><A HREF="bta_main.php">Return to main administration panel</A></TD>
</TR>
<?php
	/*
	 * Connect to the database
	 */
	if ($GLOBALS["persist"])
		$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	else
		$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	mysql_select_db($database) or sqlErr(mysql_error());

	echo "<TR>\r\n\t<TD COLSPAN=15>\r\n\t\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
 
	echo "\t\t<TR>\r\n\t\t\t<TD ALIGN=RIGHT COLSPAN=2>\r\n\t\t\t<TABLE CLASS=\"tblAdminInner\" cellpadding=\"5\" cellspacing=\"1\">\r\n";

	echo "\t\t\t<TR>\r\n";
	echo "\t\t\t\t<TD CLASS=\"advSortHeading\" ALIGN=\"left\" VALIGN=\"bottom\">Torrent</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"advSortHeading\" ALIGN=\"center\" VALIGN=\"bottom\">Group</TD>\r\n";

	echo "\t\t\t</TR>\r\n";
	
	/*
	 * Grab the information out of the database
	 */
	$query = "SELECT summary.info_hash, 
			namemap.filename, 
			summary.external_torrent,
			subgrouping.group_id,
			subgrouping.heading FROM summary 
				LEFT JOIN namemap ON summary.info_hash = namemap.info_hash 
				LEFT OUTER JOIN subgrouping ON subgrouping.group_id = namemap.grouping $where ORDER BY subgrouping.groupsort, namemap.sorting";
	

	$recordset = mysql_query($query) or die("Can't do SQL query - ".mysql_error());

	/*
	 * Classes used to alternate background colour in rows
	 */
	$clrRowBG[0] = 'CLASS="advSortOdd"';
	$clrRowBG[1] = 'CLASS="advSortEven"';

	$totalglobaltorrents=0;

	/*
	 * Get the current timestamp for checking whether or not to
	 * hide things
	 */
	$today = time();

	$advancedsortcounter = "";
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"advSortHeading\" ALIGN=\"center\" COLSPAN=25>Ungrouped (orphaned) torrents<BR>[<A HREF=\"bta_advsort_process.php?action=grpautosort&group=0\">Auto sort group by torrent name</A>]</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * Go through recordset, showing torrents
	 */
	while ($row=mysql_fetch_row($recordset)) 	{
		/*
		 * Although there should not be any null values in the database
		 * we will double check it before displaying them
		 */
		if (is_null($row[1])) $row[1] = $row[0];   //filename
		if (strlen($row[1]) == 0) $row[1]=$row[0]; //filename check

		$rowBackground = $clrRowBG[$totalglobaltorrents % 2];

		/*
		 * If this is the first torrent displayed and it does not belong to group 0, then 
		 * there are no orphaned torrents. To be nice, this should be stated.
		 */
		if ($totalglobaltorrents == 0 && $row[3] != 0) {
			echo "\t\t\t<TR>\r\n\t\t\t\t<TD COLSPAN=15 ALIGN=CENTER $rowBackground>No orphaned torrents</TD>\r\n\t\t\t</TR>\r\n";
			$totalglobaltorrents++;
			$rowBackground = $clrRowBG[$totalglobaltorrents % 2];		
		}

		/*
		 * Start a new group heading if needed.
		 */
		if ($row[4] != $advancedsortcounter) {
			echo "\t\t\t<TR>\r\n\t\t\t\t<TD COLSPAN=15 ALIGN=CENTER $rowBackground>Group: <INPUT TYPE=TEXT NAME=hdtitle[$row[3]] SIZE=75 VALUE=\"$row[4]\"><BR>[<A HREF=\"bta_advsort_process.php?action=mvgrp&group=$row[3]\">Move</A>]&nbsp;&nbsp;[<A HREF=\"bta_advsort_process.php?action=grpautosort&group=$row[3]\">Auto sort group by torrent name</A>]&nbsp;&nbsp;[<A HREF=\"bta_advsort_process.php?action=grpdel&group=$row[3]\">Delete</A>]</TD>\r\n\t\t\t</TR>\r\n";
			$advancedsortcounter = $row[4];
			$totalglobaltorrents++;
			$rowBackground = $clrRowBG[$totalglobaltorrents % 2];
		}

		echo "\t\t\t<TR>\r\n";
	
		/*
		 * Torrent name
		 */
		echo "\t\t\t\t<TD $rowBackground>$row[1]&nbsp;&nbsp;<A HREF=\"bta_advsort_process.php?action=mvtorrent&info_hash=$row[0]\">Move...</A></TD>\r\n";

		/*
		 * Grouping information. If the sort group is 0, display a add option.
		 * Otherwise display a remove option
		 */
		echo "\t\t\t\t<TD $rowBackground><SELECT NAME=\"group_action[$row[0]]\">";
		echo "<OPTION VALUE=\"0\">&nbsp;</OPTION>";

		if ($row[3] == 0)
			echo "<OPTION VALUE=\"".ACTION_SORT_GROUP."\">Group...</OPTION>";
		else
			echo "<OPTION VALUE=\"".ACTION_SORT_UNGROUP."\">Ungroup</OPTION>";

		echo "</SELECT></TD>\r\n";
		
		echo "\t\t\t</TR>\r\n";

		$totalglobaltorrents++;
	}

	/*
	 * If no torrents to display, indicate so. If torrents were listed
	 * show summary line if requested
	 */
	if ($totalglobaltorrents == 0)
		echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD $clrRowBG[0] COLSPAN=11 ALIGN=CENTER>No active torrents</TD>\r\n\t\t\t\t</TR>\r\n";

	echo "\t\t\t</TABLE>\r\n\t\t\t</TD>\r\n\t\t</TR>\r\n";

	/*
	 * Tracker summary line with version, etc...
	 */
	echo "\t\t<TR>\r\n\t\t\t<TD ALIGN=LEFT><FONT FACE=\"Arial\" SIZE=\"1\">$phpbttracker_id $phpbttracker_ver using MySQL.</FONT></TD>\r\n";
	echo "\t\t\t<TD ALIGN=RIGHT><FONT FACE=\"Arial\" SIZE=\"1\">Ki = 1024, Mi = 1024 Ki, Gi = 1024 Mi, Ti = 1024 Gi</FONT></TD>\r\n\t\t</TR>\r\n";
	
	echo "\t\t<TR>\r\n\t\t\t<TD COLSPAN=3 ALIGN=\"center\"><INPUT TYPE=\"submit\" NAME=\"processbuttonstep1\" VALUE=\"Process selected torrents...\" CLASS=\"button\"></TD>\r\n";
	echo "\t\t</TR>\r\n";

?>
		</TABLE>
	</TD>
</TR>
<TR>
	<TD ALIGN="center" COLSPAN=15><A HREF="bta_main.php">Return to main administration panel</A><BR><BR>Note 1: Any new group added will not appear immediately on this page. You need to add a torrent to the group before it is displayed here.</TD>
</TR>
</TABLE>
</FORM>
</BODY>
</HTML>
