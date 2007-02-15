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
	 * column options: all the columns available are customizable
	 *   -set the variable to true to show the column; false to hide it
	 *
	 * The columns are as follows:
	 *   Name/Info Hash:         is always shown, and is the name or the hash of the torrent
	 *   Size/CRC:      	     the size of the torrent, and the CRC value (if any) (optional)
	 *   Status/Admin:           is always shown, administrative functions
	 *   Dates: 		     dates associated with the torrent (optional)
	 *   UL/DL/DONE/XFER:        tracker stats (optional)
	 *   Category/Speed/%:       is always shown, the category, speed and average % done on the torrent
	 *   Unhide/Retire/Delete:   is always shown, more administrative functions
	 */
	$showsizeCRC = true;
	$showdates = true;
	$showseed_leech_done_xfer = true;

	/*
	 * default category: if none is specified, this category is assumed.
	 */
	$defaultcategory= "all";

	/*
	 * If set to TRUE, the index page will only display a list of active groups on the tracker.
	 * When a group is chosen, then torrent admin will be displayed.
	 */
	$showCategorySelectionOnly = false;

	/*
	 * default ordering: if no order is specified, use this order (valid values: "name", "date", "size", "xfer", "done", and "category")
	 */
	$defaultorder = "category";

	/*
	 * List of the external modules required
	 */
	require_once ("../funcsv2.php");
	require_once ("../version.php");
	require_once ("bta_funcs.php");

	/*
	 * If the retired torrents screen was being used, unset the variable
	 * used in bta_confirm.php, so that the return hyperlink points to this
	 * page.
	 */
	if (isset($_SESSION["retiredadmin"]))
		unset($_SESSION["retiredadmin"]);

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
	 * Set the mysql WHERE clause to nothing for now.
	 * This may change later if a category is specified.
	 */
	$where = " ";

	/*
	 * Get the order from the URL, if one is specified.
	 * If not specified, assume default ordering scheme.
	 */
	if (isset($_GET["order"])) {
		if (strpos($_GET["order"], " ") !== false) {
			admShowMsg("Invalid order parameter", "An invalid order value was passed.", "Invalid order request", true, "bta_main.php", 5);
		}

		$requestedorder = $_GET["order"];
		$defaultorderset = false;
	}
	else {
		$requestedorder = $defaultorder;
		$defaultorderset = true;
	}

	/*
	 * Get the requested category if there is one.
	 * If a group is logged in, ignore requests and force them
	 * to view their torrents only...
	 */
	if (!isset($_SESSION["admin_perms"]["category"])) {
		/*
		 * Root user
		 */
		
		/*
		 * Browsing between pages was getting annoying as user root, as it never remembered
		 * what category you were browsing. Added a session variable to fix this; it will
		 * change the $defaultcategory to whatever was browsed last, but ONLY for the user root.
		 */
		if (isset($_SESSION["root_last_cat"])) {
			$defaultcategory = $_SESSION["root_last_cat"];
			$where = " WHERE namemap.category = \"" . $_SESSION["root_last_cat"] . "\"";
		}

		/*
		 * If a new category is specified override the history.
		 */
		if (isset($_GET["category"])) {
			if (strpos($_GET["category"], " ") !== false) {
				admShowMsg("Invalid category parameter", "An invalid category value was passed.", "Invalid category request", true, "bta_main.php", 5);
			}

			if ($_GET["category"] == "all") {
				if (isset($_SESSION["root_last_cat"])) unset($_SESSION["root_last_cat"]);

				$where = " ";
			} else {
				$_SESSION["root_last_cat"] = $_GET["category"];
				$where = " WHERE namemap.category = \"" . $_GET["category"] . "\"";
			}
			$hrefCategory = "?category=" . $_GET["category"];
		} else {
				$hrefCategory = "?category=$defaultcategory";
		}
	} else {
		/*
		 * Okay, force them to view only their torrents...
		 */
		$where = " WHERE namemap.category = \"" . $_SESSION["admin_perms"]["category"] . "\"";
		$hrefCategory = "?category=" . $_SESSION["admin_perms"]["category"];
	}

	/*
	 * Get the sort direction from the URL, if one exists.
	 * If not specified, assume ascending order.
	 */
	if (isset($_GET["sort"])) {
		if (strpos($_GET["sort"], " ") !== false) {
			admShowMsg("Invalid sort parameter", "An invalid sort value was passed.", "Invalid sort request", true, "bta_main.php", 5);
		}

		$requestedsort = $_GET["sort"];
	
		switch ($requestedsort) {
			case "ascending":
				$sortorder = " ";
				$sortascending = true;
				break;
		case "descending":
			$sortorder = " DESC ";
			$sortascending = false;
			break;
		default:
			$sortorder = " ";
			$sortascending = true;
		}
	} else {
		$sortorder = " ";
		$sortascending = true;
	}

	/*
	 * Build the hyperlinks needed to specify a sort in the heading.
	 */
	$namehref = $scriptname . $hrefCategory . "&amp;order=name";
	$sizehref = $scriptname . $hrefCategory . "&amp;order=size";
	$datehref = $scriptname . $hrefCategory . "&amp;order=date";
	$seedhref = $scriptname . $hrefCategory . "&amp;order=seeders";
	$leechhref = $scriptname . $hrefCategory . "&amp;order=leechers";
	$donehref = $scriptname . $hrefCategory . "&amp;order=completed";
	$xferhref = $scriptname . $hrefCategory . "&amp;order=transferred";
	$avghref = $scriptname . $hrefCategory . "&amp;order=averagedone";
	$spdhref = $scriptname . $hrefCategory . "&amp;order=speed";
	$categoryhref = $scriptname . $hrefCategory . "&amp;order=category";

	/*
	 * This switch statments checks which is the CURRENT sort order,
	 * and specifies an option to REVERSE the current sort order.
	 * e.g. If sorting by name in ASCENDING order, the hyperlink will add 
	 *      a parameter to sort the name in DESCENDING order.
	 */
	switch ($requestedorder) {
		case "name": 
			$order = " ORDER BY filename " . $sortorder;
			if ($sortascending) $namehref = $namehref . "&amp;sort=descending"; else $namehref = $namehref . "&amp;sort=ascending";
			break;
		case "size":
			$order = " ORDER BY size " . $sortorder . ", filename ";
			if ($sortascending) $sizehref = $sizehref . "&amp;sort=descending"; else $sizehref = $sizehref . "&amp;sort=ascending";
			break;
		case "date":
			$order = " ORDER BY DateAdded " . $sortorder . ", filename ";
			if ($sortascending) $datehref = $datehref . "&amp;sort=descending"; else $datehref = $datehref . "&amp;sort=ascending";
			break;
		case "seeders":
			$order = " ORDER BY seeds " . $sortorder . ", filename ";
			if ($sortascending) $seedhref = $seedhref . "&amp;sort=descending"; else $seedhref = $seedhref . "&amp;sort=ascending";
			break;
		case "leechers":
			$order = " ORDER BY leechers " . $sortorder . ", filename ";
			if ($sortascending) $leechhref = $leechhref . "&amp;sort=descending"; else $leechhref = $leechhref . "&amp;sort=ascending";
			break;
		case "completed":
			$order = " ORDER BY finished " . $sortorder . ", filename ";
			if ($sortascending) $donehref = $donehref . "&amp;sort=descending"; else $donehref = $donehref . "&amp;sort=ascending";
			break;
		case "transferred":
			$order = " ORDER BY dlbytes " . $sortorder . ", filename ";
			if ($sortascending) $xferhref = $xferhref . "&amp;sort=descending"; else $xferhref = $xferhref . "&amp;sort=ascending";
			break;
		case "averagedone":
			$order = " ORDER BY avgdone " . $sortorder . ", filename ";
			if ($sortascending) $avghref = $avghref . "&amp;sort=descending"; else $avghref = $avghref . "&amp;sort=ascending";
			break;
		case "speed":
			$order = " ORDER BY speed " . $sortorder . ", filename ";
			if ($sortascending) $spdhref = $spdhref . "&amp;sort=descending"; else $spdhref = $spdhref . "&amp;sort=ascending";
			break;
		case "category":
			$order = " ORDER BY category, filename ";
			break;
		default:
			$order = " ";
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
	echo "<TITLE>". $adm_page_title . " - Main screen</TITLE>\r\n";
?>
</HEAD>

<BODY>
<FORM ACTION="bta_confirm.php" METHOD=POST>

<TABLE CLASS="tblAdminOuter">
<TR>
<?php
	/*
	 * Display the page heading.
	 */
	echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>Main administration screen";

	/*
	 * Show the comment in the user table if there is one...
	 */
	if (isset($_SESSION["admin_perms"]["comment"]))
		if (strlen($_SESSION["admin_perms"]["comment"]) > 0)
			echo "<BR>for<BR>" . $_SESSION["admin_perms"]["comment"];
	
	echo "</TD>\r\n";
?>
</TR>
<?php 
	admShowURL_Login($ip);
	if ($_SESSION["admin_perms"]["viewconf"] || $_SESSION["admin_perms"]["root"])
		echo "<TR>\r\n\t<TD CLASS=\"data\" ALIGN=\"center\" COLSPAN=15>\r\n\t\t<A HREF=\"bta_configuration.php\">Click here for tracker configuration details</A><HR></TD>\r\n</TR>\r\n";
?>
<TR>
	<TD CLASS="data" COLSPAN=15 ALIGN="center"><BR>
		Welcome to the main administration screen. Up above you will see hyperlinks that will take you to various parts of the administration interface, some information on the terminal you are logging in from, and the tracker's current configuration.<BR>
	   <A HREF="help/" TARGET="_blank">Need help?</A><BR>
	</TD>
</TR>
<TR>
	<TD CLASS="data" COLSPAN=15>&nbsp;</TD>
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

	/*
	 * Show summary only to root user
	 */
	if ($_SESSION["admin_perms"]["root"]) {
		echo "<TR>\r\n\t<TD CLASS=\"data\" COLSPAN=15 ALIGN=\"center\">Below is a summary of groups that have torrents on this tracker. Click the group name link to show ONLY that group's torrents, or click the \"Jump To\" link to jump to the group on this page.\r\n";
		echo "\tIf the \"Jump To\" link is not being displayed, it is because you are not viewing all the groups that have retired torrents on this tracker OR the data is not sorted by category. If you don't see any torrents being displayed, click one of the links below.<BR>\r\n";
		echo "\t\t<TABLE CLASS=\"tblCategories\">\r\n";

		/*
		 * Build a query string of information we need to get from the database
		 */
		$query = "SELECT DISTINCT category FROM namemap ORDER BY category";

		/*
		 * Get a recordset of the torrents...
		 */
		$recordset = mysql_query($query) or sqlErr(mysql_error());

		/*
		 * Only allow 4 items in one table row
		 */
		$colCount = 1;

		/*
		 * Used to alternate background colour for a checkerbox pattern
		 */
		$itemCount = 1;

		/*
		 * These are the alternating Cascadying Style Sheet classes used for the data.
		 */
		$classRowBGClr[0] = 'CLASS="catodd"';
		$classRowBGClr[1] = 'CLASS="cateven"';

		/*
		 * Use the first value for the background
		 */
		$cellBG = $classRowBGClr[0];

		echo "\t\t<TR>\r\n\t\t\t<TD $cellBG ALIGN=\"center\"><A HREF=\"?category=all\">All Groups</A></TD>\r\n";

		/*
		 * Display all the categories found in the database.
		 */
		while ($row=mysql_fetch_row($recordset)) {
			$cellBG = $classRowBGClr[$itemCount % 2];
			if ($colCount < 6) {
				if ($colCount == 0)
					echo "\t\t<TR>\r\n";
				echo "\t\t\t<TD $cellBG ALIGN=\"center\"><A HREF=\"?category=$row[0]\">$row[0]</A>";
				if ($requestedorder == "category" && !$showCategorySelectionOnly && !isset($_SESSION["root_last_cat"]))
					echo "<BR><FONT SIZE=-1><A HREF=\"#$row[0]\">Jump To</A></FONT>";
				echo "</TD>\r\n";
				$colCount++;
			} else {
				echo "\t\t\t<TD $cellBG ALIGN=\"center\"><A HREF=\"?category=$row[0]\">$row[0]</A>";
				if ($requestedorder == "category" && !$showCategorySelectionOnly && !isset($_SESSION["root_last_cat"]))
					echo "<BR><FONT SIZE=-1><A HREF=\"#$row[0]\">Jump To</A></FONT>";
				echo "</TD>\r\n\t\t</TR>\r\n";
				$colCount = 0;
			}
			$itemCount++;
		}

		/*
		 * Fill the remaining blank spots, if any
		 */
		if ($colCount != 0) {
			while ($colCount <= 6 && $itemCount > 5) {
				$cellBG = $classRowBGClr[$itemCount % 2];
				echo "\t\t\t<TD $cellBG ALIGN=\"center\">&nbsp;</TD>\r\n";
				$colCount++;
				$itemCount++;
			} 
			echo "\t\t</TR>\r\n";
		}

		echo "\t\t</TABLE>\r\n\t</TD>\r\n</TR>\r\n";
	}
?>
<?php

/*
 * Don't show the details if only a group list is requested.
 */
if (!($showCategorySelectionOnly && !isset($_GET["category"]))) {
	echo "<TR>\r\n\t<TD COLSPAN=15>\r\n\t\t\t<TABLE CLASS=\"tblAdminOuter\">";
 
	echo "\t\t<TR>\r\n\t\t\t<TD ALIGN=RIGHT COLSPAN=2>\r\n\t\t\t\t<TABLE CLASS=\"tblAdminInner\" cellpadding=\"5\" cellspacing=\"1\">\r\n";

	/*
	 * Output the column headers.
	 */
	$spdRefresh = round($GLOBALS['spdrefresh']/60, 2);
	$avgRefresh = round($GLOBALS['avgrefresh']/60, 2);
	echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD COLSPAN=15 CLASS=\"sortheading\">To sort the data, click on the column header hyperlinks.</TD>\r\n\t\t\t\t</TR>\r\n";
	echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD COLSPAN=15 CLASS=\"refreshheading\">Average percentage stats are updated automagically every ". $avgRefresh ." minute(s),<BR>Speed stats are updated automagically every ". $spdRefresh ." minute(s).</TD>\r\n\t\t\t\t</TR>\r\n";
	echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"left\" VALIGN=\"bottom\"><A HREF=\"$namehref\">Name/Info Hash</A></TD>\r\n";
	if ($showsizeCRC) echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\"><A HREF=\"$sizehref\">Size</A><DIV CLASS=\"crc32\">CRC32</DIV></TD>\r\n";
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Status / Action</TD>\r\n";
	if ($showdates) echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Dates<BR><A HREF=\"$datehref\">Added</A><BR><DIV CLASS=\"DateToRemoveURL\">Remove URL</DIV><DIV CLASS=\"DateToHideTorrent\">Hide Torrent</DIV></TD>\r\n"; 
	if ($showseed_leech_done_xfer) echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\"><A HREF=\"$seedhref\">UL</A> / <A HREF=\"$leechhref\">DL</A><BR><A HREF=\"$donehref\">DONE</A><BR><A HREF=\"$xferhref\">XFER</A></TD>\r\n";
	if ($_SESSION["admin_perms"]["root"])
		echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\"><A HREF=\"$categoryhref\">Category</A><BR><A HREF=\"$spdhref\">Speed</A><BR><A HREF=\"$avghref\">Avg % Done</A></TD>\r\n";
	else
		echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\"><A HREF=\"$spdhref\">Speed</A><BR><A HREF=\"$avghref\">Avg % Done</A></TD>\r\n";
	echo "\r\n\t\t\t\t</TR>\r\n";

	/*
	 * These are the alternating Cascadying Style Sheet classes used for the data.
	 */
	$classRowBGClr[0] = 'CLASS="odd"';
	$classRowBGClr[1] = 'CLASS="even"';

	/*
	 * Build a query string of information we need to get from the database
	 */
	$query = "SELECT summary.info_hash, 
			namemap.filename, 
			namemap.info, 
			namemap.size, 
			namemap.crc32, 
			namemap.DateAdded, 
			summary.seeds, 
			summary.leechers, 
			summary.finished, 
			summary.dlbytes, 
			summary.avgdone, 
			summary.speed, 
			namemap.category,
			summary.hide_torrent,
			namemap.DateToRemoveURL,
			namemap.DateToHideTorrent,
			summary.external_torrent,
			summary.ext_no_scrape_update,
			namemap.addedby FROM summary LEFT JOIN namemap ON summary.info_hash = namemap.info_hash $where $order";

	/*
	 * Get a recordset of the torrents...
	 */
	$recordset = mysql_query($query) or sqlErr(mysql_error());

	/*
	 * Let's keep track of totals stats, for the summary line, so we need to initialize a few variables
	 */
	$totaltorrents = 0;
	$totalsize=0;
	$totalseeders=0;
	$totalleechers=0;
	$totalcomplete=0;
	$totalxferred=0;
	$totalspeed=0;

	/*
	 * External torrent counterparts...
	 */
	$totalexttorrents = 0;
	$totalextsize=0;
	$totalextseeders=0;
	$totalextleechers=0;
	$totalextcomplete=0;
	$totalextxferred=0;
	$totalextspeed=0;

	$totalglobaltorrents = 0;

	/*
	 * let's set anchors for the various categories
	 * to do this we need to know 1. what the last category was and
	 * 2. if we are sorting by category, otherwise it's useless
	 */
	$lastCategory = "";

	/*
	 * Let's parse through the recordset, and show the information.
	 */
	while ($row=mysql_fetch_row($recordset)) {
		//check for empty or null values
		/*
		 * Make sure the filename isn't NULL or an empty string
		 */
		if (is_null($row[1])) $row[1] = $row[0];	//filename null? if yes, use hash
		if (strlen($row[1]) == 0) $row[1]=$row[0];	//filename empty? if yes, use hash

		/*
		 * Let's set a variable with the current BT hash
		 */
		$currentHash = $row[0];

		/*
		 * Start a table row.
		 */
		echo "\t\t\t\t<TR>\n";

		/*
		 * Okay, do a modulus (%) and figure out whether this is an EVEN or ODD row
		 * then put the information in a variable so we have the right cell class.
		 */
		$cellBG = $classRowBGClr[$totalglobaltorrents % 2];

		/*
		 * The file name / info hash
		 */
		echo "\t\t\t\t\t<TD $cellBG>";
		echo $row[1] . "<BR>Info Hash: " . $currentHash;

		/*
		 * If user is root, show who added the torrent
		 */
		if ($_SESSION["admin_perms"]["root"]) {
			echo "<BR>Added by: " . $row[18];
		}

		echo "\t\t\t\t\t</TD>\r\n";

		/*
		 * Torrent size / CRC Checksums
		 */
		if ($showsizeCRC) {
			if ($row[3] > 1024) $fsize=round($row[3]/1024,1) . " GiB"; else $fsize=round($row[3],1)." MiB";
			echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">$fsize<DIV CLASS=\"crc32\">$row[4]</DIV></TD>\r\n";
		}


		/*
		 * Administrative functions / torrent status
		 */
		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">";

		//if the torrent is hidden, make sure it's indicated
		if ($row[13]=='Y')
			echo "<DIV CLASS=\"specialtag\">HIDDEN</DIV>";
		if ($row[16]=='Y')
			echo "<DIV CLASS=\"specialtag\">EXTERNAL</DIV>";
		if ($row[16]=='N' && $row[13]=='N')
			echo "<FONT SIZE=-1>Active</FONT><BR>";

		if ($_SESSION["admin_perms"]["edit"] || $_SESSION["admin_perms"]["root"])
			echo "<FONT SIZE=-1>[<A HREF=\"bta_do.php?action=edit&amp;info_hash=$currentHash\">Edit</A>]</FONT>&nbsp;&nbsp;";
		if ($_SESSION["admin_perms"]["peers"] || $_SESSION["admin_perms"]["root"] && $row[16]=='N')
			echo "<FONT SIZE=-1>[<A HREF=\"bta_do.php?action=peerinfo&amp;info_hash=$currentHash\">Peers</A>]</FONT>&nbsp;&nbsp;";
		if ($row[16]=='N')
			echo "<FONT SIZE=-1>[<A HREF=\"bta_seedsum.php?info_hash=$currentHash\" TARGET=\"_blank\">Seeders</A>]</FONT>";

		echo "<BR>\r\n\t\t\t\t\t<SELECT CLASS=\"action\" NAME=\"process[$currentHash]\">\r\n";
		echo "\t\t\t\t\t\t<OPTION VALUE=\"0\">.: Choose action :.</OPTION>\r\n";

		/*
		 * Hide / unhide torrent
		 */
		if ($_SESSION["admin_perms"]["unhide"] || $_SESSION["admin_perms"]["root"]) {
			if ($row[13]=='N') {
					echo "\t\t\t\t\t\t<OPTION VALUE=".ACTION_HIDE.">Hide</OPTION>\r\n";
			} else {
					echo "\t\t\t\t\t\t<OPTION VALUE=".ACTION_UNHIDE.">Unhide</OPTION>\r\n";			
			}
		}

		/*
		 * Retire torrent 
		 */
		if ($_SESSION["admin_perms"]["retire"] || $_SESSION["admin_perms"]["root"]) {
			if ($row[16]=='N')
				echo "\t\t\t\t\t\t<OPTION VALUE=".ACTION_RETIRE.">Retire</OPTION>\r\n";
		}

		/*
		 * Delete torrent
		 */
		if ($_SESSION["admin_perms"]["delete"] || $_SESSION["admin_perms"]["root"])
			echo "\t\t\t\t\t\t<OPTION VALUE=".ACTION_DELETE.">Delete</OPTION>\r\n";

		
		echo "\t\t\t\t\t</SELECT>\r\n";

		
		echo "\t\t\t\t\t</TD>\r\n";

		/*
		 * Dates
		 */
		if ($showdates) {
			/*
			 * mysql treats an empty date string as 0000-00-00, so let's check the dates for it.
			 * If they match it, let's not display it. Looks cluttered otherwise.
			 */
			if ($row[5] == "0000-00-00") $row[5] = "";
			if ($row[14] == "0000-00-00") $row[14] = "";
			if ($row[15] == "0000-00-00") $row[15] = "";

			echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\" WIDTH=\"70\">$row[5]<BR>";
			echo "<DIV CLASS=\"DateToRemoveURL\">[$row[14]]</DIV>";
			echo "<DIV CLASS=\"DateToHideTorrent\">[$row[15]]</DIV>";
			echo "</TD>\r\n";
		}

		/*
		 * Seeders / Leechers / Completed Downloads / Total Transferred
		 */
		if ($showseed_leech_done_xfer) {
			//XFER stat: show TiB if necessary, otherwise just show GiB
			if ($row[9] > 1099511627776)
				$xferred = round($row[9]/1099511627776,2) . " TiB";
			else
				$xferred = round($row[9]/1073741824,2) . " GiB";


			echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">";

			/*
			 * If countbytes is off or the heavyload param is set,
			 * we need to indicate the stats aren't being tracked...
			 */
			if (!$GLOBALS["countbytes"] || $GLOBALS["heavyload"]) {
				$xferred = "N/A";
			}

			/*
			 * if the torrent is external, make sure it's indicated
			 * also, show a '?' for ul/dl/done if they are 0
			 */
			if ($row[16]=='Y') {
				if ($xferred == 0) $xferred = "?";
				if ($row[6] == 0) $row[6] = "?";
				if ($row[7] == 0) $row[7] = "?";
				if ($row[8] == 0) $row[8] = "?";
			}

			/*
			 * If not a scrape enabled external torrent, say so
			 * in place of the stats.
			 */
			if ($row[17]=='Y') {
				echo "Not a scrape<BR>enabled external<BR>torrent</TD>\r\n";
			} else {
				echo "$row[6] / $row[7]<BR>$row[8]<BR>$xferred</TD>\r\n";
			}
		}


		/*
		 * Average % done / speed / category
		 */
		//speed calculations
		if ($row[11] == 0)
			$speed = "Stalled";
        	else if ($row[11] < 0)
       	        	$speed = "Stalled";
	        else if ($row[11] > 1397152)
       		        $speed = round($row[11]/1048576,2) . " MiB/sec";
	        else
       		        $speed = round($row[11] / 1024, 2) . " KiB/sec";

			/*
			 * If countbytes is off or the heavyload param is set,
			 * we need to indicate the stats aren't being tracked...
			 */
			if (!$GLOBALS["countbytes"] || $GLOBALS["heavyload"]) {
				$speed = "N/A";
			}

		if ($requestedorder == "category") {
			/*
			 * Okay, we are sorting by category. Let's spit out some anchors.
			 */
			if ($lastcategory != $row[12]) {
				/*
				 * New category. Build an anchor and memorize the last category.
				 */
				$categoryData = "<A NAME=\"$row[12]\">$row[12]</A>";
				$lastcategory = $row[12];
			} else {
				/*
				 * Not a new category, no anchor is needed
				 */
				$categoryData = "$row[12]";
			}

			/*
			 * if the torrent is external, show the fields we can't calculate
			 * spd/avg will always be a '?' as we can't calculate it...
			 */
			if ($row[16]=='Y') {
				if ($row[11] == 0) $speed = "?%";
				if ($row[10] == 0) $averagedone = "?"; else $averagedone = $row[10] . "%";
			} else
				$averagedone = round($row[10],1) . "%";

			/*
			 * If doavg is off or the heavyload param is set,
			 * we need to indicate the stats aren't being tracked...
			 */
			if (!$GLOBALS["doavg"] || $GLOBALS["heavyload"]) {
				$averagedone = "N/A";
			}

			/*
			 * If not a scrape enabled external torrent, simply
			 * don't show the stats.
			 */
			if ($row[17]=='Y') {
				$speed = "";
				$averagedone = "";
			}

			//output the data line
			if ($_SESSION["admin_perms"]["root"]) {
				echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><DIV CLASS=\"category\">$categoryData</DIV><DIV CLASS=\"speed\">$speed</DIV>". $averagedone . "</TD>\r\n";
			} else {
				echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><DIV CLASS=\"speed\">$speed</DIV>". $averagedone . "</TD>\r\n";
			}
		} else {
			if ($_SESSION["admin_perms"]["root"]) {
				echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><DIV CLASS=\"category\">$row[12]</DIV><DIV CLASS=\"speed\">$speed</DIV>". $averagedone . "</TD>\r\n";
			} else {
				echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><DIV CLASS=\"speed\">$speed</DIV>". $averagedone . "</TD>\r\n";
			}
		}

		/*
		 * Keep a running total for the stats line... so increment them.
		 */
		/*
		 * only add to the totals if the torrents are NOT external
		 */
		if ($row[16]=='N') {
			$totaltorrents++;
			$totalsize += $row[3];
			$totalseeders += $row[6];
			$totalleechers += $row[7];
			$totalcomplete += $row[8];
			$totalxferred += $row[9];
			$totalspeed += $row[11];
		} else {
			$totalexttorrents++;
			$totalextsize += $row[3];
			$totalextseeders += $row[6];
			$totalextleechers += $row[7];
			$totalextcomplete += $row[8];
			$totalextxferred += $row[9];
			$totalextspeed += $row[11];
		}

		$totalglobaltorrents++;
	}

	/*
	 * If there were no torrents, display a 'No active torrents' message, otherwise
	 * show a summary line.
	 */
	if ($totaltorrents == 0)
		echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD $classRowBGClr[0] COLSPAN=15 ALIGN=CENTER>No active torrents</TD>\r\n\t\t\t\t</TR>";
	else {
		/*
		 * Calculate for odd/even row
		 */
		$cellBG = $classRowBGClr[$totalglobaltorrents % 2];

		/*
		 * Total amount of torrents on the page.
		 */
		if ($totalexttorrents > 0)
			echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD $cellBG ALIGN=\"LEFT\"><b>Currently tracking $totaltorrents torrent(s)</b>. [Plus $totalexttorrents external torrent(s).]</TD>\r\n";
		else
			echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD $cellBG ALIGN=\"LEFT\"><b>Currently tracking $totaltorrents torrent(s)</b>.</TD>\r\n";

		/*
		 * Total size being shared.
		 */
		if ($showsizeCRC) {
			//See if GiB needs to be displayed.
			if ($totalsize > 1200)
				$totalsize = round($totalsize / 1024, 2) . " GiB";
			else
				$totalsize = round($totalsize, 2) . " MiB";

			//Ditto for external torrents
			if ($totalextsize > 1200)
				$totalextsize = round($totalextsize / 1024, 2) . " GiB";
			else
				$totalextsize = round($totalextsize, 2) . " MiB";

			if ($totalexttorrents > 0)
				echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">$totalsize<BR>[$totalextsize]</TD>\r\n";
			else
				echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">$totalsize</TD>\r\n";
		}

		/*
		 * Can't total the Admin column!
		 */
		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">---</TD>\r\n";

		/*
		 * Can't total the Dates column!
		 */
		if ($showdates) echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">---</TD>\r\n";

		/*
		 * Total number of seeders / leechers / downloads / transferred
		 */
		if ($showseed_leech_done_xfer) {
			//XFER amount: calculate whether TiB or GiB needs to be calculated.
			if ($totalxferred > 1099511627776)
				$totalxferred = round($totalxferred/1099511627776,2) . " TiB";
			else
				$totalxferred = round($totalxferred/1073741824,1) . " GiB";

			//again, check external totals
			if ($totalextxferred > 1099511627776)
				$totalextxferred = round($totalextxferred/1099511627776,2) . " TiB";
			else
				$totalextxferred = round($totalextxferred/1073741824,1) . " GiB";

			if ($totalextxferred == 0) $totalextxferred = "?";

			if ($totalexttorrents > 0)
				echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">$totalseeders [$totalextseeders] / $totalleechers [$totalextleechers]<BR>$totalcomplete [$totalextcomplete]<BR>$totalxferred [$totalextxferred]</TD>\r\n";
			else
				echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">$totalseeders / $totalleechers<BR>$totalcomplete<BR>$totalxferred</TD>\r\n";
		}


		/*
		 * The category / Speed / average % done column
		 */
		//Does KiB or MiB need to be displayed?
		if ($totalspeed > 1397152)
			$totalspeed = round($totalspeed/1048576,2) . " MiB/sec";
		else
			$totalspeed = round($totalspeed / 1024, 2) . " KiB/sec";

		//Check external torrents as well...
		if ($totalextspeed > 1397152)
			$totalextspeed = round($totalextspeed/1048576,2) . " MiB/sec";
		else
			$totalextspeed = round($totalextspeed / 1024, 2) . " KiB/sec";

		if ($totalextspeed == 0) $totalextspeed = "?";

		if ($totalexttorrents > 0)
			echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><DIV CLASS=\"speed\">$totalspeed<BR>[$totalextspeed]</DIV></TD>\r\n";
		else
			echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><DIV CLASS=\"speed\">$totalspeed</DIV></TD>\r\n";

		echo "\t\t\t\t</TR>\r\n";
	}

	echo "\t\t\t\t</TABLE>\r\n\t\t\t</TD>\r\n\t\t</TR>\r\n";

	// -- Summary at bottom of the torrent table. --
	echo "\t\t<TR>\r\n\t\t\t<TD ALIGN=LEFT CLASS=\"summary\">". $adm_page_title ." using MySQL.</TD>\r\n";
	echo "\t\t\t<TD ALIGN=RIGHT CLASS=\"summary\">Ki = 1024, Mi = 1024 Ki, Gi = 1024 Mi, Ti = 1024 Gi</TD>\r\n\t\t</TR>\r\n";

	if ($totalexttorrents > 0)
		echo "\t\t<TR>\r\n\t\t\t<TD COLSPAN=10 ALIGN=CENTER CLASS=\"summary\">Items in square brackets in the summary line indicate external torrent statistics.</TD>\r\n\t\t</TR>\r\n";

	echo "\t\t<TR>\r\n\t\t\t<TD>&nbsp</TD>\r\n\t\t</TR>\r\n";
	echo "\t\t<TR>\r\n\t\t\t<TD ALIGN=\"center\"><INPUT TYPE=\"submit\" NAME=\"processbutton\" VALUE=\"Unhide/hide/retire/delete selected torrents...\" CLASS=\"button\"></TD>\r\n";
	echo "\t\t\t<TD ALIGN=\"center\"><INPUT TYPE=reset VALUE=\"Clear retire/delete selections\" CLASS=\"button\"></TD>\r\n\t\t</TR>\r\n";
	echo "\t\t<TR>\r\n\t\t\t<TD>&nbsp</TD>\r\n\t\t</TR>\r\n";

	echo "\t\t</TABLE>\r\n\t</TD>\r\n</TR>\r\n";
}
?>
</TABLE>

</FORM>
</BODY>
</HTML>
