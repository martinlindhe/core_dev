<?php
	/*
	 *	Script name: retired_sample.php
	 * Description: This is a script to show retired torrents. The features are: 
	 *                 - ability to show different categories ( ?category= )
	 *                 - show and hide columns in the retired torrent table
	 *                 - show/hide summary stats line
	 *                 - show/hide the return to main page hyperlink
	 *                 - CSS formatting (see the sample tracker.css)
	 *                 - sortable stat columns (including reverse sort)
	 *
	 * There are a couple pieces from DeHacked's original script, but this has been mostly rewritten.
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
	 * Function to generate page load time. It also resides in funcsv2.php, but
	 * this script doesn't need to be dependent on that module.
	 */
	function mtime_float() {
		list($usec, $sec) = explode(" ", microtime());
	   return ((float)$usec + (float)$sec);
	}
	$starttime = mtime_float();

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
	 * Returning to the main page: set $showreturnlink to true to show a link at the top and bottom of
	 * the retired torrents table
	 */
	$showreturnlink = true;

	/*
	 * allowsorting: turn to false if you don't want people to be able to sort by column.
	 */
	$allowsorting = true;

	/*
	 * showsummary: set to false if summary should not be displayed at the bottom of the table.
	 */
	$showsummary = true;

	/*
	 * default ordering: if no order is specified, use this order (valid values: "name", "date", "size", "addeddate", "xfer", "done", and "category")
	 */
	$defaultorder = "name";

	/*
	 * Classes used to alternate row background colours
	 */
	$clrRowBG[0] = 'trkOdd';
	$clrRowBG[1] = 'trkEven';

	/*
	 * Depending on where you install the scripts, you may need to change the
	 * references here
	 */
	require_once ("../version.php");
	require_once ("../config.php");

	/*
	 * Don't modify below this comment unless you know what you are doing!
	 */

	/*
	 * Set the where clause to an empty string to start with (gets built later in the code...)
	 */
	$where = "";

	/*
	 * If a category was requested, get and parse it, otherwise
	 * use the default specified in this script
	 */
	if (isset($_GET["category"])) {
		$requestedcategory = $_GET["category"];

		if (strcmp($requestedcategory, $defaultcategory) == 0)
			$usedefcat = true;
		else
			$usedefcat = false;
	} else {
		$requestedcategory = $defaultcategory;
		$usedefcat = true;
	}

	/*
	 * Check if user allowed to view groups
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
				/*
				 * no restrictions
				 */
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

	/*
	 * If there is a request for sort direction, get and parse it
	 */
	if (isset($_GET["sort"])) {
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
			die("Invalid sort order specified (ascending/descending).");
		}
	} else {
		$sortorder = " ";
		$sortascending = true;
	}

	/*
	 * Start building the hyperlinks for each column
	 */
	$hrefstarted = false;

	/*
	 * We don't need to specify the category if the default category is
	 * used...
	 */
	if (!$usedefcat) {
		if (strlen($categoryparam)>0) {
			$hrefparam = $_SERVER["PHP_SELF"] . "?" . $categoryparam;
			$hrefstarted=true;
		}
	}

	/*
	 * Start building the column hyperlinks
	 */
	if ($hrefstarted) {
		$namehref = $hrefparam . "&amp;order=name";
		$datehref = $hrefparam . "&amp;order=date";
		$adddatehref = $hrefparam . "&amp;order=addeddate";
		$sizehref = $hrefparam . "&amp;order=size";
		$xferhref = $hrefparam . "&amp;order=xfer";
		$donehref = $hrefparam . "&amp;order=done";
		$categoryhref = $hrefparam . "&amp;order=category";
	} else {
		$namehref = $_SERVER["PHP_SELF"] . "?order=name";
		$datehref = $_SERVER["PHP_SELF"] . "?order=date";
		$adddatehref = $_SERVER["PHP_SELF"] . "?order=addeddate";
		$sizehref = $_SERVER["PHP_SELF"] . "?order=size";
		$xferhref = $_SERVER["PHP_SELF"] . "?order=xfer";
		$donehref = $_SERVER["PHP_SELF"] . "?order=done";
		$categoryhref = $_SERVER["PHP_SELF"] . "?order=category";
	}

	/*
	 * Get a requested order if one was specified, otherwise
	 * use the default specified in this script
	 */
	if (isset($_GET["order"])) {
		$requestedorder = $_GET["order"];
		$defaultorderset = false;
	} else {
		$requestedorder = $defaultorder;
		$defaultorderset = true;
	}

	/*
	 * Finish building the hyperlinks, and add reverse sorting to the urls
	 */
	switch ($requestedorder) {
		case "name": 
			$order = " ORDER BY filename " . $sortorder;
			if ($sortascending) $namehref .= "&amp;sort=descending"; else $namehref .= "&amp;sort=ascending";
			break;
		case "date":
			$order = " ORDER BY DateRetired " . $sortorder;
			if ($sortascending) $datehref .= "&amp;sort=descending"; else $datehref .= "&amp;sort=ascending";
			break;
		case "size":
			$order = " ORDER BY size " . $sortorder;	
			if ($sortascending) $sizehref .= "&amp;sort=descending"; else $sizehref .= "&amp;sort=ascending";
			break;
		case "addeddate":
			$order = " ORDER BY DateAdded " . $sortorder;
			if ($sortascending) $adddatehref .= "&amp;sort=descending"; else $adddatehref .= "&amp;sort=ascending";
			break;
		case "xfer":
			$order = " ORDER BY transferred " . $sortorder;
			if ($sortascending) $xferhref .= "&amp;sort=descending"; else $xferhref .= "&amp;sort=ascending";
			break;
		case "done":
			$order = " ORDER BY completed " . $sortorder;
			if ($sortascending) $donehref .= "&amp;sort=descending"; else $donehref .= "&amp;sort=ascending";
			break;
		case "category":
			/*
			 * If you can't view all the torrents, this is an error
			 */
			if ($categoryparam=="category=all") {
				$order = " ORDER BY category, filename ";
				if ($sortascending) $categoryhref .= "&amp;sort=descending"; else $categoryhref .= "&amp;sort=ascending";
			} else
				die("Invalid sort order specified.");
			break;
		default:
			die("Invalid sort order specified.");
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<LINK REL="stylesheet" HREF="../tracker.css" TYPE="text/css" TITLE="Default">
	<TITLE>Sample retired torrents page</TITLE>
</HEAD>

<BODY>
	<CENTER>
	<TABLE CLASS="trkOuter">
	<TR>
		<TD CLASS="mainCells" COLSPAN=2><H1>Sample retired torrents page</H1><BR></TD>
	</TR>
<?php
	/*
	 * Show return to main link if needed
	 */
	if ($showreturnlink)
		echo "\t<TR><TD CLASS=\"retiredLink\" COLSPAN=2><A HREF=\"javascript:history.back()\">Return to torrent statistic page</A><BR><BR></TD></TR>\r\n";

	echo "\t<TR>\r\n\t\t<TD COLSPAN=2>\r\n\t\t<TABLE CLASS=\"trkInner\">\r\n";

	/*
	 * If sorting is enabled, show hyperlinks to perform the sort
	 */
	if ($allowsorting) {
		echo "\t\t<TR>\r\n\t\t\t<TH CLASS=\"trkSortHeading\" COLSPAN=9>To sort the data, click on the column header hyperlinks.</TH>\r\n\t\t</TR>\r\n";
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TH CLASS=\"trkHeading trkData colName\"><A HREF=\"$namehref\">Name/Info Hash</A></TH>\r\n";
		if ($showsize) echo "\t\t\t<TH CLASS=\"trkHeading trkData colSize\"><A HREF=\"$sizehref\">Size</A></TH>\r\n";
		if ($showcrc) echo "\t\t\t<TH CLASS=\"trkHeading trkData colCRC\">CRC32</TH>\r\n";
		if ($showcompleted) echo "\t\t\t<TH CLASS=\"trkHeading trkData colDone\"><A HREF=\"$donehref\">Done</A></TH>\r\n";
		if ($showxferred) echo "\t\t\t<TH CLASS=\"trkHeading trkData colXfer\"><A HREF=\"$xferhref\">XFER</A></TH>\r\n";
		if ($showadded) echo "\t\t\t<TH CLASS=\"trkHeading trkData colAdded\"><A HREF=\"$adddatehref\">Date<BR>Added</A></TH>\r\n";
		if ($showremoved) echo "\t\t\t<TH CLASS=\"trkHeading trkData colAdded\"><A HREF=\"$datehref\">Date<BR>Retired</A></TH>\r\n";
		if ($requestedcategory=="all") echo "\t\t\t<TH CLASS=\"trkHeading trkData colCategory\"><A HREF=\"$categoryhref\">Category</A></TH>\r\n";
		echo "\t\t</TR>\r\n";
	} else {
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TH CLASS=\"trkHeading trkData colName\">Name/Info Hash</TH>\r\n";
		if ($showsize) echo "\t\t\t<TH CLASS=\"trkHeading trkData colSize\">Size</TH>\r\n";
		if ($showcrc) echo "\t\t\t<TH CLASS=\"trkHeading trkData colCRC\">CRC32</TH>\r\n";
		if ($showcompleted) echo "\t\t\t<TH CLASS=\"trkHeading trkData colDone\">Done</TH>\r\n";
		if ($showxferred) echo "\t\t\t<TH CLASS=\"trkHeading trkData colXfer\">XFER</TH>\r\n";
		if ($showadded) echo "\t\t\t<TH CLASS=\"trkHeading trkData colAdded\">Date<BR>Added</TH>\r\n";
		if ($showremoved) echo "\t\t\t<TH CLASS=\"trkHeading trkData colAdded\">Date<BR>Retired</TH>\r\n";
		if ($requestedcategory=="all") echo "\t\t\t<TH CLASS=\"trkHeading trkData colCategory\">Category</TH>\r\n";
		echo "\t\t</TR>\r\n";
	}

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

	/*
	 * get the data
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
	$recordset = mysql_query($query) or die("Can't do SQL query - ".mysql_error());

	/*
	 * Keep track of totals for summary line
	 */
	$totaltorrents = 0;
	$totalsize=0;
	$totalcomplete=0;
	$totalxferred=0;

	/*
	 * Go through the recordset, showing data in table
	 */
	while ($row=mysql_fetch_row($recordset)) {
		/*
		 * Theoretically there shouldn't be any null values in the database
		 * But we'll check for null values anyway...
		 */
		if (is_null($row[1])) $row[1] = $row[0];	//filename
		if (is_null($row[2])) $row[2] = "";		//url
		if (is_null($row[4])) $row[4] = "";		//info
		if (strlen($row[1]) == 0) $row[1] = $row[0];	//filename check

		echo "\t\t<TR>\r\n";
		$writeout = $clrRowBG[$totaltorrents % 2];
		echo "\t\t\t<TD CLASS=\"$writeout trkData colName\">$row[1]</TD>\r\n";

		/*
		 * Show gigabytes if necessary
		 */
		if ($data[2] > 1024) $fsize=round($row[2]/1024,1) . "<BR>GiB"; else $fsize=round($row[2],1)."<BR>MiB";

		if ($showsize) echo "\t\t\t<TD CLASS=\"$writeout trkData colSize\">$fsize</TD>\r\n";
		if ($showcrc) echo "\t\t\t<TD CLASS=\"$writeout trkData colCRC\">$row[3]</TD>\r\n";	
		if ($showcompleted) echo "\t\t\t<TD CLASS=\"$writeout trkData colDone\">$row[5]</TD>\r\n";

		/*
		 * Show terabytes if necessary
		 */
		if ($row[6] > 1099511627776)
			$xferred = round($row[6]/1099511627776,2) . "<BR>TiB";
		else
			$xferred = round($row[6]/1073741824,1) . "<BR>GiB";

		if ($showxferred) echo "\t\t\t<TD CLASS=\"$writeout trkData colXfer\">$xferred</TD>\r\n";		
		if ($showadded) echo "\t\t\t<TD CLASS=\"$writeout trkData colAdded\">$row[7]</TD>\r\n";
		if ($showremoved) echo "\t\t\t<TD CLASS=\"$writeout trkData colAdded\">$row[8]</TD>\r\n";

		if ($requestedcategory=="all") echo "\t\t\t<TD CLASS=\"$writeout trkData colCategory\">$row[4]</TD>\r\n";

		echo "\t\t</TR>\r\n";

		/*
		 * Keep track of totals for summary line
		 */
		$totaltorrents++;
		$totalsize += $row[2];
		$totalcomplete += $row[5];
		$totalxferred += $row[6];
	}

	/*
	 * If there is no retired torrents to show, indicate so
	 */
	if ($totaltorrents == 0)
		echo "\t\t<TR>\r\n\t\t\t<TD CLASS=\"$clrRowBG[0] trkData colSummary\" COLSPAN=9>No retired torrents to display</TD>\r\n\t\t</TR>\r\n";
	else {
		/*
		 * If a summary line is supposed to be displayed, show it
		 */
		if ($showsummary) {
			$writeout = $clrRowBG[$totaltorrents % 2];

			echo "\t\t<TR>\r\n\t\t\t<TD CLASS=\"$writeout trkData colName\"><B>$totaltorrents retired/inactive torrent(s).</B></TD>\r\n";

			/*
			 * Show gigabytes for total size if needed
			 */
			if ($totalsize > 1200)
				$totalsize = round($totalsize / 1024, 2) . "<BR>GiB";
			else
				$totalsize = round($totalsize, 2) . "<BR>MiB";

			/*
			 * Show terabytes for total transferred if needed
			 */
			if ($totalxferred > 1099511627776)
				$totalxferred = round($totalxferred/1099511627776,2) . "<BR>TiB";
			else
				$totalxferred = round($totalxferred/1073741824,1) . "<BR>GiB";

			if ($showsize) echo "\t\t\t<TD CLASS=\"$writeout trkData colSize\">$totalsize</TD>\r\n";
			if ($showcrc) echo "\t\t\t<TD CLASS=\"$writeout trkData colSummary\">---</TD>\r\n";
			if ($showcompleted) echo "\t\t\t<TD CLASS=\"$writeout trkData colDone\">$totalcomplete</TD>\r\n";
			if ($showxferred) echo "\t\t\t<TD CLASS=\"$writeout trkData colXfer\">$totalxferred</TD>\r\n";
			if ($showadded) echo "\t\t\t<TD CLASS=\"$writeout trkData colAdded\">---</TD>\r\n";
			if ($showremoved) echo "\t\t\t<TD CLASS=\"$writeout trkData colAdded\">---</TD>\r\n";
			if ($requestedcategory=="all") echo "\t\t\t<TD CLASS=\"$writeout trkData colCategory\">---</TD>\r\n";
		}
	}
?>
		</TABLE>
		</TD>
	</TR>
<?php	
	echo "\t<TR>\r\n\t\t<TD CLASS=\"versionLine\">$phpbttracker_id $phpbttracker_ver using MySQL.</TD>\r\n";
	echo "\t\t<TD CLASS=\"scaleLine\">Ki = 1024, Mi = 1024 Ki, Gi = 1024 Mi, Ti = 1024 Gi</TD>\r\n\t</TR>\r\n";

	if ($showreturnlink)
		echo "\t<TR><TD CLASS=\"retiredLink\" COLSPAN=2><A HREF=\"javascript:history.back()\">Return to torrent statistic page</A><BR><BR></TD></TR>\r\n";

	/*
	 * Calculate time it took to generate this page.
	 */
	$stoptime = mtime_float();
	$pagegen_time = round($stoptime - $starttime, 4);
	echo "\t<TR>\r\n\t\t<TD CLASS=\"infoTag\" COLSPAN=2>This page was generated in $pagegen_time seconds.</TD>\r\n\t</TR>\r\n";

	/*
	 * This initial script is 4.01 compliant, so show it. If you make modifications, make sure it is still compliant, or remove this!
	 */
	echo "\t<TR><TD>&nbsp</TD></TR>\r\n";
	echo "\t<TR><TD CLASS=\"mainCells\" COLSPAN=11><A HREF=\"http://validator.w3.org/check/referer\"><IMG BORDER=\"0\" SRC=\"http://www.w3.org/Icons/valid-html401\" ALT=\"Valid HTML 4.01!\" height=\"31\" width=\"88\"></A>";
	echo "<A HREF=\"http://jigsaw.w3.org/css-validator/\"><IMG STYLE=\"border:0;width:88px;height:31px\" SRC=\"http://jigsaw.w3.org/css-validator/images/vcss\" ALT=\"Valid CSS!\"></A></TD></TR>\r\n";
?>
	</TABLE>
	</CENTER>
</BODY>
</HTML>
