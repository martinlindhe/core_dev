<?php

	/*
	 * Module:	bta_retired.php
	 * Description: This is the retired torrents management screen of the administrative interface.
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
	 *   Size/CRC:      	     	  the size of the torrent, and the CRC value (if any) (optional)
	 *   Dates: 		     		  dates associated with the torrent (optional)
	 *   Category/DONE/XFER:     is always shown, the category, total done, and total transferred
	 *   Revive/Delete:   		  is always shown, more administrative functions
	 */
	$showsizeCRC = true;
	$showdates = true;

	/*
	 * If set to TRUE, the index page will only display a list of active groups on the tracker.
	 * When a group is chosen, then torrent admin will be displayed.
	 */
	$showCategorySelectionOnly = false;

	/*
	 * default category: if none is specified, this category is assumed.
	 */
	$defaultcategory= "all";

	/*
	 * default ordering: if no order is specified, use this order (valid values: "name", "date", "rdate", "size", "xfer", "done", and "category")
	 */
	$defaultorder = "category";

	/*
	 * List of the external modules required
	 */
	require_once ("../funcsv2.php");
	require_once ("../version.php");
	require_once ("bta_funcs.php");

	/*
	 * Set the flag so bta_confirm will return to this page.
	 */
	$_SESSION["retiredadmin"] = true;

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
	if (!($_SESSION["admin_perms"]["retiredmgmt"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Redirecting to the main administration panel.",
			       $adm_page_title, true, "bta_main.php", 3);
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
			admShowMsg("Invalid order parameter", "An invalid order value was passed.", "Invalid order request", true, "bta_retired.php", 5);
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
	 * Alternatively, force the user to view only their torrents...
	 */
	if (!isset($_SESSION["admin_perms"]["category"])) {
		if (isset($_GET["category"])) {
			if (strpos($_GET["category"], " ") !== false) {
				admShowMsg("Invalid category parameter", "An invalid category value was passed.", "Invalid category request", true, "bta_retired.php", 5);
			}

			if ($_GET["category"] == "all") {
				$where = " ";
			} else {
				$where = " WHERE category = \"" . $_GET["category"] . "\"";
			}
			$hrefCategory = "?category=" . $_GET["category"];
		} else
			$hrefCategory = "?category=$defaultcategory";
	} else {
		/*
		 * Show only the indicated category
		 */
		$where = " WHERE category = \"" . $_SESSION["admin_perms"]["category"] . "\"";
		$hrefCategory = "?category=" . $_SESSION["admin_perms"]["category"];
	}

	/*
	 * Get the sort direction from the URL, if one exists.
	 * If not specified, assume ascending order.
	 */
	if (isset($_GET["sort"])) {
		if (strpos($_GET["sort"], " ") !== false) {
			admShowMsg("Invalid sort parameter", "An invalid sort value was passed.", "Invalid sort request", true, "bta_retired.php", 5);
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
	$rdatehref = $scriptname . $hrefCategory . "&amp;order=rdate";
	$donehref = $scriptname . $hrefCategory . "&amp;order=completed";
	$xferhref = $scriptname . $hrefCategory . "&amp;order=transferred";
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
		case "rdate":
			$order = " ORDER BY dateretired " . $sortorder . ", filename ";
			if ($sortascending) $rdatehref = $rdatehref . "&amp;sort=descending"; else $rdatehref = $rdatehref . "&amp;sort=ascending";
			break;
		case "completed":
			$order = " ORDER BY completed " . $sortorder . ", filename ";
			if ($sortascending) $donehref = $donehref . "&amp;sort=descending"; else $donehref = $donehref . "&amp;sort=ascending";
			break;
		case "transferred":
			$order = " ORDER BY transferred " . $sortorder . ", filename ";
			if ($sortascending) $xferhref = $xferhref . "&amp;sort=descending"; else $xferhref = $xferhref . "&amp;sort=ascending";
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
	echo "<TITLE>". $adm_page_title . " - Retired torrent management</TITLE>\r\n";
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
	echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>Retired torrent management";

	/*
	 * Show the comment in the user table if there is one...
	 */
	if (isset($_SESSION["admin_perms"]["comment"]))
		if (strlen($_SESSION["admin_perms"]["comment"]) > 0)
			echo "<BR>for<BR>" . $_SESSION["admin_perms"]["comment"];

	echo "</TD>\r\n";
?>
</TR>
<?php admShowURL_Login($ip); ?>
<TR>
	<TD CLASS="data" COLSPAN=15 ALIGN="center"><BR>
	   <A HREF="help/help_retired_torrents.php" TARGET="_blank">Need help?</A><BR>
	</TD>
</TR>
<TR>
	<TD CLASS="data" COLSPAN=15><BR>
		Welcome to the retired torrent management screen. Below is a list of retired torrents on the tracker. You can either make them active again ("revive") or remove them permanently using this screen.
		<BR><BR>
	</TD>
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
		$query = "SELECT DISTINCT category FROM retired ORDER BY category";

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
				if ($requestedorder == "category" && !$showCategorySelectionOnly)
					if (!isset($_GET["category"]) || $_GET["category"] == "all") {
						echo "<BR><FONT SIZE=-1><A HREF=\"#$row[0]\">Jump To</A></FONT>";
					}
				echo "</TD>\r\n";
				$colCount++;
			} else {
				echo "\t\t\t<TD $cellBG ALIGN=\"center\"><A HREF=\"?category=$row[0]\">$row[0]</A>";
				if ($requestedorder == "category" && !$showCategorySelectionOnly)
					if (!isset($_GET["category"]) || $_GET["category"] == "all") {
						echo "<BR><FONT SIZE=-1><A HREF=\"#$row[0]\">Jump To</A></FONT>";
					}
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
<TR>
	<TD ALIGN="center" COLSPAN=15><A HREF="bta_main.php">Return to main administration panel</A></TD>
</TR>
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
	echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD COLSPAN=15 CLASS=\"sortheading\">To sort the data, click on the column header hyperlinks.</TD>\r\n\t\t\t\t</TR>\r\n";
	echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"left\" VALIGN=\"bottom\"><A HREF=\"$namehref\">Name/Info Hash</A></TD>\r\n";
	if ($showsizeCRC) echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\"><A HREF=\"$sizehref\">Size</A><DIV CLASS=\"crc32\">CRC32</DIV></TD>\r\n";
	if ($showdates) echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Dates<BR><A HREF=\"$datehref\">Added</A><BR><DIV CLASS=\"DateRetired\">[ <A HREF=\"$rdatehref\">Retired</A> ]</DIV></TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\"><A HREF=\"$categoryhref\">Category</A><BR><A HREF=\"$donehref\">DONE</A><BR><A HREF=\"$xferhref\">XFER</A></TD>\r\n";
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Revive?</TD>\r\n";
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Delete?</TD>\r\n";
	echo "\r\n\t\t\t\t</TR>";

	/*
	 * These are the alternating Cascadying Style Sheet classes used for the data.
	 */
	$classRowBGClr[0] = 'CLASS="odd"';
	$classRowBGClr[1] = 'CLASS="even"';

	/*
	 * Build a query string of information we need to get from the database
	 */
	$query = "SELECT info_hash, 
			filename, 
			size, 
			crc32, 
			category,
			completed, 
			transferred, 
			dateadded, 
			dateretired FROM retired $where $order";

	/*
	 * Get a recordset of the torrents...
	 */
	$recordset = mysql_query($query) or sqlErr(mysql_error());

	/*
	 * Let's keep track of totals stats, for the summary line, so we need to initialize a few variables
	 */
	$totaltorrents = 0;
	$totalsize=0;
	$totalcomplete=0;
	$totalxferred=0;

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
		$cellBG = $classRowBGClr[$totaltorrents % 2];

		/*
		 * The file name / info hash
		 */
		echo "\t\t\t\t\t<TD $cellBG>";
		echo $row[1];

		echo "</TD>\r\n";

		/*
		 * Torrent size & CRC Checksums
		 */
		if ($showsizeCRC) {
			if ($row[2] > 1024) $fsize=round($row[2]/1024,1) . " GiB"; else $fsize=round($row[2],1)." MiB";
			echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">$fsize<DIV CLASS=\"crc32\">$row[3]</DIV></TD>\r\n";
		}

		/*
		 * Dates
		 */
		if ($showdates) {
			/*
			 * mysql treats an empty date string as 0000-00-00, so let's check the dates for it.
			 * If they match it, let's not display it. Looks cluttered otherwise.
			 */
			if ($row[7] == "0000-00-00") $row[7] = "";
			if ($row[8] == "0000-00-00") $row[8] = "";

			echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\" WIDTH=\"70\">$row[7]<BR>";
			echo "<DIV CLASS=\"DateRetired\">[$row[8]]</DIV>";
			echo "</TD>\r\n";
		}

		/*
		 * category / total downloads / total transferred
		 */
			//XFER stat: show TiB if necessary, otherwise just show GiB
			if ($row[6] > 1099511627776)
				$xferred = round($row[6]/1099511627776,2) . " TiB";
			else
				$xferred = round($row[6]/1073741824,1) . " GiB";

		if ($requestedorder == "category") {
			/*
			 * Okay, we are sorting by category. Let's spit out some anchors.
			 */
			if ($lastcategory != $row[4]) {
				/*
				 * New category. Build an anchor and memorize the last category.
				 */
				$categoryData = "<A NAME=\"$row[4]\">$row[4]</A>";
				$lastcategory = $row[4];
			} else {
				/*
				 * Not a new category, no anchor is needed
				 */
				$categoryData = "$row[4]";
			}

			//output the data line
			echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><DIV CLASS=\"category\">$categoryData</DIV>$row[5]<BR>$xferred</TD>\r\n";
		} else
			echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><DIV CLASS=\"category\">$row[4]</DIV>$row[5]<BR>$xferred</TD>\r\n";

		/*
		 * The action button are all radio buttons that are grouped by
		 * each hash value. This way there is no need to check for duplicate
		 * operations in the confirmation page.
		 *
		 * Revive torrent radio button
		 */
		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><INPUT TYPE=radio NAME=\"process[$currentHash]\" VALUE=".ACTION_REVIVE.">R</TD>\r\n";

		/*
		 * Delete retired torrent radio button
		 */
		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><INPUT TYPE=radio NAME=\"process[$currentHash]\" VALUE=".ACTION_RDELETE.">D</TD>\r\n";

		echo "\t\t\t\t</TR>\r\n";

		/*
		 * Keep a running total for the stats line... so increment them.
		 */
		$totaltorrents++;
		$totalsize += $row[2];
		$totalcomplete += $row[5];
		$totalxferred += $row[6];
	}

	/*
	 * If there were no torrents, display a 'No active torrents' message, otherwise
	 * show a summary line.
	 */
	if ($totaltorrents == 0)
		echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD $classRowBGClr[0] COLSPAN=15 ALIGN=CENTER>No retired torrents</TD>\r\n\t\t\t\t</TR>";
	else {
		/*
		 * Calculate for odd/even row
		 */
		$cellBG = $classRowBGClr[$totaltorrents % 2];

		/*
		 * Total amount of torrents on the page.
		 */
		echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD $cellBG ALIGN=\"LEFT\"><b>$totaltorrents retired torrent(s)</b>.</TD>\r\n";

		/*
		 * Total size being shared.
		 */
		if ($showsizeCRC) {
			//See if GiB needs to be displayed.
			if ($totalsize > 1200)
				$totalsize = round($totalsize / 1024, 2) . " GiB";
			else
				$totalsize = round($totalsize, 2) . " MiB";

			echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">$totalsize</TD>\r\n";
		}

		/*
		 * Can't total the Dates column!
		 */
		if ($showdates) echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">---</TD>\r\n";

		/*
		 * Total number of seeders / leechers / downloads / transferred
		 */
		//XFER amount: calculate whether TiB or GiB needs to be calculated.
		if ($totalxferred > 1099511627776)
			$totalxferred = round($totalxferred/1099511627776,2) . " TiB";
		else
			$totalxferred = round($totalxferred/1073741824,1) . " GiB";

		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">---<BR>$totalcomplete<BR>$totalxferred</TD>\r\n";

		/*
		 * Can't total the Revive checkbox column!
		 */
		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">---</TD>\r\n";		

		/*
		 * Can't total the Delete checkbox column!
		 */
		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">---</TD>\r\n";

		echo "\t\t\t\t</TR>\r\n";
	}

	echo "\t\t\t\t</TABLE>\r\n\t\t\t</TD>\r\n\t\t</TR>\r\n";

	// -- Summary at bottom of the torrent table. --
	echo "\t\t<TR>\r\n\t\t\t<TD ALIGN=LEFT CLASS=\"summary\">". $adm_page_title ." using MySQL.</TD>\r\n";
	echo "\t\t\t<TD ALIGN=RIGHT CLASS=\"summary\">Ki = 1024, Mi = 1024 Ki, Gi = 1024 Mi, Ti = 1024 Gi</TD>\r\n\t\t</TR>\r\n";

	echo "\t\t<TR>\r\n\t\t\t<TD>&nbsp</TD>\r\n\t\t</TR>\r\n";
	echo "\t\t<TR>\r\n\t\t\t<TD ALIGN=\"center\"><INPUT TYPE=\"submit\" NAME=\"processbutton\" VALUE=\"Revive/delete retired torrents...\" CLASS=\"button\"></TD>\r\n";
	echo "\t\t\t<TD ALIGN=\"center\"><INPUT TYPE=reset VALUE=\"Clear revive/delete selections\" CLASS=\"button\"></TD>\r\n\t\t</TR>\r\n";
	echo "\t\t<TR>\r\n\t\t\t<TD>&nbsp</TD>\r\n\t\t</TR>\r\n";

	echo "\t\t</TABLE>\r\n\t</TD>\r\n</TR>\r\n";
}
?>
<TR>
	<TD ALIGN="center" COLSPAN=15><A HREF="bta_main.php">Return to main administration panel</A></TD>
</TR>
</TABLE>

</FORM>
</BODY>
</HTML>
