<?php
	/*
	 *	Script name: basic_retired_sample.php
	 * Description: This is a *really* basic script to show retired torrents. The only "extra" features are: 
	 *                 - ability to show different categories
	 *                 - show and hide columns in the torrent table
	 *                 - show/hide summary stats line
	 *                 - show/hide the return to main hyperlink
	 *
	 * There are a couple pieces from DeHacked's original mystats script, but this has been mostly rewritten.
	 *
	 * Feel free to customize this script.
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
	 * category options:
	 *   -set the defaultcategory variable to the default category you wish to display if none specified
	 *   -set the catrestrict variable to TRUE if you want to restrict the page to the default ONLY
	 *   -set the allowall variable to TRUE if you want people to be able to view all groups on your tracker. off by default.
	 *
	 *   If you set allowall to false, and a user attempts to use ?category=all, it will stop output with an error.
	 *   If you set catrestrict to true and a user attempts to specify a group with ?category=<cat>, it will stop output with an error.
	 *   If you set the default category to all and set allowall to false, it's not my fault if it doesn't work :-)
	 */
	$defaultcategory= "main";
	$catrestrict = false;
	$allowall = true;

	/*
	 * column options: all the columns available are customizable
	 *   -set the variable to true to show the column; false to hide it
	 *
	 * The columns are as follows:
	 *   Name:      is always shown, and is the name of the torrent
	 *   Size:      the size of the torrent
	 *   crc:       the CRC32 value of the torrent
	 *   completed: the total number of completed downloads during the life of the torrent
	 *   xferred:   the total amount of bandwidth transferred during the life of the torrent
	 *   added:     the date the torrent was added to the tracker
	 *   removed:   the date the torrent was removed from the tracker
	 */
	$showsize = true;
	$showcrc = true;
	$showcompleted = true;
	$showxferred = true;
	$showadded = true;
	$showremoved = true;

	/*
	 * showsummary: set to false if summary should not be displayed at the bottom of the table.
	 */
	$showsummary = true;

	/*
	 * Returning to the main page: set $showreturnlink to true to show a link at the top and bottom of
	 * the retired torrents table
	 */
	$showreturnlink = true;

	/*
	 * colours used to decorate the tracker table
	 * you can use "#000000" - "#FFFFFF" or one of the following:
	 * "black", "white", "red", "blue", "green", "yellow", "orange",
	 * "violet", "purple", "pink", "silver", "gold", "gray", "aqua",
	 * "skyblue", "lightblue", "fuchsia", "khaki"
	 *
	 * Tracker table background
	 */
	$clrTableBG = "black";

	/*
	 * Table heading backgound
	 */
	$clrHeaderBG = "#C0C0C0";

	/*
	 * Rows in the data table
	 */
	$clrRowBG[0] = 'BGCOLOR="#cccccc"';
	$clrRowBG[1] = 'BGCOLOR="#c0c0c0"';

	/*
	 * HTML BODY tag colours
	 */
	$clrBodyBG = "#CCCCCC";
	$clrBodyText = "#000000";
	$clrBodyLink = "#0000FF";
	$clrBodyVlink = "#000080";

	/*
	 * Depending on where you place the scripts, you may need to change
	 * where these are referenced
	 */
	require_once ("version.php");
	require_once ("config.php");

	/*
	 * Don't change anything below this comment unless you know what you
	 * are doing...
	 *
	 * Set the where clause to an empty string to start with.
	 */
	$where = "";

	/*
	 * Get requested category from parameter line if there is one
	 */
	if (isset($_GET["category"])) {
		$requestedcategory = $_GET["category"];
	
		if (strcmp($requestedcategory, $defaultcategory) == 0)
			$usedefcat = true;
		else
			$usedefcat = false;
	}
	else {
		$requestedcategory = $defaultcategory;
		$usedefcat = true;
	}

	/*
	 * Check to make sure that user has permission to
	 * specify a category
	 */
	switch ($requestedcategory) {
		case "all":
			if ($allowall) {
				$categoryparam="category=all";
			}
			else 
				die("Forbidden: You cannot view all the groups.");
			break;
		default:
			if (!$catrestrict) {
				//no restrictions
				$where = $where . " WHERE category = \"" . $requestedcategory . "\"";
				$categoryparam = "category=" . $requestedcategory;
			} else {
				if ($usedefcat) {
					$where = $where . " WHERE category = \"" . $requestedcategory . "\"";
					$categoryparam = "category=" . $requestedcategory;
				} else {
					//it's restricted, stop processing
					die("Forbidden: You cannot specify a category for this page.");
				}
			}
	}
	$where .= " ORDER BY filename "; 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<TITLE>Basic retired torrents page</TITLE>
</HEAD>

<?php echo "<BODY BGCOLOR=\"$clrBodyBG\" TEXT=\"$clrBodyText\" LINK=\"$clrBodyLink\" VLINK=\"$clrBodyVlink\">\r\n"; ?>
	<CENTER>
	<H1>Basic retired torrent page</H1><BR>
<?php 
	echo "\t<TABLE BORDER=0>\r\n";

	/*
	 * Show a return to main link if needed
	 */
	if ($showreturnlink)
		echo "\t<TR><TD ALIGN=CENTER COLSPAN=2><BR><A HREF=\"javascript:history.back()\">Return to torrent statistic page</A><BR><BR></TD></TR>\r\n";

	echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT COLSPAN=2>\r\n\t\t\t<TABLE border=\"0\" cellpadding=\"5\" cellspacing=\"1\" bgcolor=\"$clrTableBG\" WIDTH=\"100%\">\r\n";

	// -- Column Headers --
	echo "\t\t\t<TR>\r\n";
	echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"left\" VALIGN=\"bottom\">Name/Info Hash</TH>\r\n";
	if ($showsize) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"center\" VALIGN=\"bottom\">Size</TH>\r\n";
	if ($showcrc) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"right\" VALIGN=\"bottom\">CRC32</TH>\r\n";
	if ($showcompleted) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"center\" VALIGN=\"bottom\">Done</TH>\r\n";
	if ($showxferred) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"center\" VALIGN=\"bottom\">XFER</TH>\r\n";
	if ($showadded) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" WIDTH=\"80\" ALIGN=\"center\" VALIGN=\"bottom\">Date<BR>Added</TH>\r\n";
	if ($showremoved) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" WIDTH=\"80\" ALIGN=\"center\" VALIGN=\"bottom\">Date<BR>Retired</TH>\r\n";
	if ($requestedcategory=="all") echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"center\" VALIGN=\"bottom\">Category</TH>\r\n";
	echo "\t\t\t</TR>\r\n";
	
	/*
	 * Connect to the database server
	 */
	if ($GLOBALS["persist"])
		$db = mysql_pconnect($dbhost, $dbuser, $dbpass) or die("Error: can't connect to database - " . mysql_error());
	else
		$db = mysql_connect($dbhost, $dbuser, $dbpass) or die("Error: can't connect to database - " . mysql_error());

	/*
	 * Open the database
	 */
	mysql_select_db($database) or die("Error: can't open the database - " . mysql_error());

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
	 * Get the required information from database
	 */
	$recordset = mysql_query($query) or die("Query failed - ".mysql_error());

	/*
	 * Keep a running total for the stats line
	 */
	$totaltorrents = 0;
	$totalsize=0;
	$totalcomplete=0;
	$totalxferred=0;

	while ($row=mysql_fetch_row($recordset)) {
		// NULLs are such a pain at times. isset($nullvar) == false
		if (is_null($row[1])) $row[1] = $row[0];	//filename
		if (is_null($row[2])) $row[2] = "";		//url
		if (is_null($row[4])) $row[4] = "";		//info
		if (strlen($row[1]) == 0) $row[1]=$row[0];	//filename check

		/*
		 * Figure out what row colour to use
		 */
		$rowBackground = $clrRowBG[$totaltorrents % 2];

		echo "\t\t\t<TR>\r\n";
		echo "\t\t\t\t<TD $rowBackground>$row[1]</TD>\r\n";

		//calculate file size (show GiB if necessary)
		if ($row[2] > 1024) $fsize=round($row[2]/1024,1) . "<BR>GiB"; else $fsize=round($row[2],1)."<BR>MiB";

		if ($showsize) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$fsize</TD>\r\n";
		if ($showcrc) echo "\t\t\t\t<TD $rowBackground ALIGN=\"right\">$row[3]</TD>\r\n";	
		if ($showcompleted) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$row[5]</TD>\r\n";

		/*
		 * Show TiB for amount transferred if necessary
		 */
		if ($row[6] > 1099511627776)
			$xferred = round($row[6]/1099511627776,2) . "<BR>TiB";
		else
			$xferred = round($row[6]/1073741824,1) . "<BR>GiB";

		if ($showxferred) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$xferred</TD>\r\n";		
		if ($showadded) echo "\t\t\t\t<TD $rowBackground WIDTH=\"80\" ALIGN=\"center\">$row[7]</TD>\r\n";
		if ($showremoved) echo "\t\t\t\t<TD $rowBackground WIDTH=\"80\" ALIGN=\"center\">$row[8]</TD>\r\n";
		if ($requestedcategory=="all") echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$row[4]</TD>\r\n";

		echo "\t\t\t</TR>\r\n";

		/*
		 * Increment totals for the stats line...
		 */
		$totaltorrents++;
		$totalsize += $row[2];
		$totalcomplete += $row[5];
		$totalxferred += $row[6];
	}

	/*
	 * If there is something in the table, add summary line, or display a line if there
	 * are no retired torrents to display
	 */
	if ($totaltorrents == 0)
		/*
		 * Nothing to display!
		 */
		echo "\t\t\t<TR><TD $clrRowBG[0] COLSPAN=9 ALIGN=CENTER>No retired torrents to display</TD></TR>\r\n";
	else {
		/*
		 * Display summary line if requested
		 */
		$rowBackground = $clrRowBG[$totaltorrents % 2];
		if ($showsummary) {
			echo "\t\t\t<TR>\r\n\t\t\t\t<TD $rowBackground ALIGN=\"LEFT\"><B>$totaltorrents retired/inactive torrent(s).</B></TD>\r\n";

			/*
			 * Show gigabytes for total size, if necessary
			 */
			if ($totalsize > 1200)
				$totalsize = round($totalsize / 1024, 2) . "<BR>GiB";
			else
				$totalsize = round($totalsize, 2) . "<BR>MiB";

			/*
			 * Show terabytes for total transferred, if necessary
			 */
			if ($totalxferred > 1099511627776)
				$totalxferred = round($totalxferred/1099511627776,2) . "<BR>TiB";
			else
				$totalxferred = round($totalxferred/1073741824,1) . "<BR>GiB";

			if ($showsize) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalsize</TD>\r\n";
			if ($showcrc) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">---</TD>\r\n";
			if ($showcompleted) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalcomplete</TD>\r\n";
			if ($showxferred) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalxferred</TD>\r\n";
			if ($showadded) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">---</TD>\r\n";
			if ($showremoved) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">---</TD>\r\n";
			if ($requestedcategory=="all") echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">---</TD>\r\n";	
			echo "\t\t\t</TR>\r\n";
		}
	}
?>
			</TABLE>
		</TD>
	</TR>
	<TR>
<?php
	echo "\t\t<TD ALIGN=LEFT><FONT FACE=\"Arial\" SIZE=\"1\">$phpbttracker_id $phpbttracker_ver using MySQL.</FONT></TD>\r\n";
	echo "\t\t<TD ALIGN=RIGHT><FONT FACE=\"Arial\" SIZE=\"1\">Ki = 1024, Mi = 1024 Ki, Gi = 1024 Mi, Ti = 1024 Gi</FONT></TD>\r\n\t</TR>\r\n";

	if ($showreturnlink)
		echo "\t<TR><TD ALIGN=CENTER COLSPAN=2><BR><A HREF=\"javascript:history.back()\">Return to torrent statistic page</A><BR></TD></TR>\r\n";

	/*
	 * This initial script is 4.01 compliant, so show it. If you make modifications, make sure it is still compliant, or remove this!
	 */
	echo "\t<TR><TD>&nbsp</TD></TR>\r\n";
	echo "\t<TR><TD ALIGN=\"center\" COLSPAN=11><A HREF=\"http://validator.w3.org/check/referer\"><IMG BORDER=\"0\" SRC=\"http://www.w3.org/Icons/valid-html401\" ALT=\"Valid HTML 4.01!\" height=\"31\" width=\"88\"></A>";
	echo "<A HREF=\"http://jigsaw.w3.org/css-validator/\"><IMG STYLE=\"border:0;width:88px;height:31px\" SRC=\"http://jigsaw.w3.org/css-validator/images/vcss\" ALT=\"Valid CSS!\"></A></TD></TR>\r\n";
?>
	</TABLE>
	</CENTER>
</BODY>
</HTML>
