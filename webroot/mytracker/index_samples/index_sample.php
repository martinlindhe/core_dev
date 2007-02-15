<?php
	/*
	 *	Script name: index_sample.php
	 * Description: This is a script to show stats. The features are: 
	 *                 - ability to show different categories ( ?category= )
	 *                 - show and hide columns in the torrent table
	 *                 - show/hide summary stats line
	 *                 - show/hide the retired torrents hyperlink
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
	 * category options:
	 *   -set the defaultcategory variable to the default category you wish to display if none specified
	 *   -set the catrestrict variable to TRUE if you want to restrict the page to the default ONLY
	 *   -set the allowall variable to TRUE if you want people to be able to view all groups on your tracker. off by default.
	 *
	 *   If you set allowall to false, and a user attempts to use ?category=all, it will stop output with an error.
	 *   If you set catrestrict to true and a user attempts to specify a group with ?category=<cat>, it will stop output with an error.
	 *   If you set the default category to all and set allowall to false, it's not my fault if it doesn't work :-)
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
	
	$defaultcategory= "main";
	$catrestrict = false;
	$allowall = true;
	
	/*
	 * Enables the advanced sorting (torrent grouping and manual sorting) and also
	 * Live feed support. Specify the directory to the rss configuration file so this
	 * script will build the correct URL.
	 */
	$advancedsort = false;
	$show_rss = false;
	$rss_conf_path = "..";
	$rss_path_to_script = "..";
	$rss_path_to_cache = "../rss";

	/*
	 * default ordering: if no order is specified, use this order (valid values: "name", "date", "size", "addeddate", "xfer", "done", and "category")
	 */
	$defaultorder = "name";

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
	$showadded = true;
	$showseeds = true;
	$showleechers = true;
	$showcompleted = true;
	$showxferred = true;
	$showavg = true;
	$showspd = true;

	/*
	 * show retired link: set to false to hide it
	 */
	$showRetiredLink = true;
	$retiredpage = "retired.php";

	/*
	 * allowsorting: turn to false if you don't want people to be able to sort by column.
	 */
	$allowsorting = true;

	/*
	 * showsummary: set to false if summary should not be displayed at the bottom of the table.
	 */
	$showsummary = true;

	/*
	 * Depending on where you install your scripts, you may need to change the references
	 * listed here
	 */
	require_once ("../version.php");
	require_once ("../config.php");

	/*
	 * Don't change anything past this comment unless you know what you are doing...
	 */

	/*
	 * By default, we want to show torrents that are not hidden
	 */
	$where = " WHERE summary.hide_torrent=\"N\"";

	/*
	 * See if the user requested a category, if not,
	 * use the default set in the script above
	 */
	if (isset($_GET["category"])) {
		/*
		 * A category was requested. Also, if it happens
		 * to be the default set in the script, set a boolean indicating so
		 * (used later in the script for checking whether or not user is
		 * allowed to view the page)
		 */
		$requestedcategory = $_GET["category"];

		if (strpos($_GET["category"], " ")) die("Invalid parameter passed.");

		if (strcmp($requestedcategory, $defaultcategory) == 0)
			$usedefcat = true;
		else
			$usedefcat = false;
	} else {
		/*
		 * Nothing requested, use default
		 */
		$requestedcategory = $defaultcategory;
		$usedefcat = true;
	}

	/*
	 * Check if all torrents are to be displayed, and if user
	 * is actually allowed to view the page
	 */
	switch ($requestedcategory) {
		case "all":
			/*
			 * is viewing all torrents allowed?
			 */
			if ($allowall) {
				$categoryparam="category=all";
			}
			else 
				die("Forbidden: You cannot view all the groups.");
			break;
		default:
			/*
			 * if script does not allow user to specify a category, halt;
			 * otherwise set up the where clause for mysql
			 */
			if (!$catrestrict) {
				/*
				 * no script restrictions
				 */
				$where .= " AND namemap.category = \"" . $requestedcategory . "\"";
				if ($usedefcat)
					$categoryparam = "";
				else
					$categoryparam = "category=" . $requestedcategory;
			} else {
				/*
				 * if default is used, ignore script permissions
				 */
				if ($usedefcat) {
					$where .= " AND namemap.category = \"" . $requestedcategory . "\"";
					$categoryparam = "";
				} else {
					/*
					 * script restricted
					 */
					die("Forbidden: You cannot specify a category for this page.");
				}
			}
	}

	/*
	 * This script can sort by the columns displayed;
	 * see if a column was specified otherwise
	 * use the default specified in the script
	 */
	if (isset($_GET["order"])) {
		if (strpos($_GET["order"], " ")) die("Invalid parameter passed.");

		$requestedorder = $_GET["order"];
		if (strcmp($requestedorder, $defaultorder) == 0)
			$defaultorderset = true;
		else
			$defaultorderset = false;
	} else {
		$requestedorder = $defaultorder;
		$defaultorderset = true;
	}

	/*
	 * This script can reverse the sort, if needed
	 * see if a sort order was specified, if none,
	 * use the default specified in the script
	 */
	if (isset($_GET["sort"])) {
		if (strpos($_GET["sort"], " ")) die("Invalid parameter passed.");

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

			if (strcmp($requestedorder, $defaultorder) == 0)
				$defaultsortset = true;
			else
				$defaultsortset = false;
	} else {
		$sortorder = " ";
		$sortascending = true;
	}

	/*
	 * Now we start building the hyperlinks for the column headers
	 * We need to keep track of whether or not we've started specifying
	 * parameters (? is used to start the parameter list in an url, and &
	 * is used to specify multiple parameters)
	 */
	$hrefstarted = false;

	/*
	 * We don't need to specify the category if the default category is
	 * used...
	 */
	if (!$usedefcat) {
		if (strlen($categoryparam) > 0) {
			$hrefparam = $_SERVER["PHP_SELF"] . "?" . $categoryparam;
			$hrefstarted=true;
		}
	}

	/*
	 * We need to start building the individual hyperlinks for the columns now.
	 */
		if ($hrefstarted) {
			$namehref = $hrefparam . "&amp;order=name";
			$sizehref = $hrefparam . "&amp;order=size";
			$datehref = $hrefparam . "&amp;order=date";
			$seedhref = $hrefparam . "&amp;order=seeders";
			$leechhref = $hrefparam . "&amp;order=leechers";
			$donehref = $hrefparam . "&amp;order=completed";
			$xferhref = $hrefparam . "&amp;order=transferred";
			$avghref = $hrefparam . "&amp;order=averagedone";
			$spdhref = $hrefparam . "&amp;order=speed";
			$categoryhref = $hrefparam . "&amp;order=category";
		} else {
			$namehref = $_SERVER["PHP_SELF"] . "?order=name";
			$sizehref = $_SERVER["PHP_SELF"] . "?order=size";
			$datehref = $_SERVER["PHP_SELF"] . "?order=date";
			$seedhref = $_SERVER["PHP_SELF"] . "?order=seeders";
			$leechhref = $_SERVER["PHP_SELF"] . "?order=leechers";
			$donehref = $_SERVER["PHP_SELF"] . "?order=completed";
			$xferhref = $_SERVER["PHP_SELF"] . "?order=transferred";
			$avghref = $_SERVER["PHP_SELF"] . "?order=averagedone";
			$spdhref = $_SERVER["PHP_SELF"] . "?order=speed";
			$categoryhref = $_SERVER["PHP_SELF"] . "?order=category";
		}

	/*
	 * Build the ORDER BY statement for mysql
	 * We will add to the individual column hyperlinks ONLY if a reverse
	 * sort is needed
	 */
	switch ($requestedorder) {
		case "name": 
			$order = " ORDER BY filename " . $sortorder;
				if ($sortascending && !($advancedsort && !isset($_GET["order"]) && !isset($_GET["sort"]) && $usedefcat && $requestedcategory != "all")) $namehref .= "&amp;sort=descending"; else $namehref .= "&amp;sort=ascending";
			break;
		case "size":
			$order = " ORDER BY size " . $sortorder . ", filename ";
			if ($sortascending && !($advancedsort && !isset($_GET["order"]) && !isset($_GET["sort"]) && $usedefcat && $requestedcategory != "all")) $sizehref .= "&amp;sort=descending"; else $sizehref .= "&amp;sort=ascending";
			break;
		case "date":
			$order = " ORDER BY DateAdded " . $sortorder . ", filename ";
			if ($sortascending && !($advancedsort && !isset($_GET["order"]) && !isset($_GET["sort"]) && $usedefcat && $requestedcategory != "all")) $datehref .= "&amp;sort=descending"; else $datehref .= "&amp;sort=ascending";
			break;
		case "seeders":
			$order = " ORDER BY seeds " . $sortorder . ", filename ";
			if ($sortascending && !($advancedsort && !isset($_GET["order"]) && !isset($_GET["sort"]) && $usedefcat && $requestedcategory != "all")) $seedhref .= "&amp;sort=descending"; else $seedhref .= "&amp;sort=ascending";
			break;
		case "leechers":
			$order = " ORDER BY leechers " . $sortorder . ", filename ";
			if ($sortascending && !($advancedsort && !isset($_GET["order"]) && !isset($_GET["sort"]) && $usedefcat && $requestedcategory != "all")) $leechhref .= "&amp;sort=descending"; else $leechhref .= "&amp;sort=ascending";
			break;
		case "completed":
			$order = " ORDER BY finished " . $sortorder . ", filename ";
			if ($sortascending && !($advancedsort && !isset($_GET["order"]) && !isset($_GET["sort"]) && $usedefcat && $requestedcategory != "all")) $donehref .= "&amp;sort=descending"; else $donehref .= "&amp;sort=ascending";
			break;
		case "transferred":
			$order = " ORDER BY dlbytes " . $sortorder . ", filename ";
			if ($sortascending && !($advancedsort && !isset($_GET["order"]) && !isset($_GET["sort"]) && $usedefcat && $requestedcategory != "all")) $xferhref .= "&amp;sort=descending"; else $xferhref .= "&amp;sort=ascending";
			break;
		case "averagedone":
			$order = " ORDER BY avgdone " . $sortorder . ", filename ";
			if ($sortascending && !($advancedsort && !isset($_GET["order"]) && !isset($_GET["sort"]) && $usedefcat && $requestedcategory != "all")) $avghref .= "&amp;sort=descending"; else $avghref .= "&amp;sort=ascending";
			break;
		case "speed":
			$order = " ORDER BY speed " . $sortorder . ", filename ";
			if ($sortascending && !($advancedsort && !isset($_GET["order"]) && !isset($_GET["sort"]) && $usedefcat && $requestedcategory != "all")) $spdhref .= "&amp;sort=descending"; else $spdhref .= "&amp;sort=ascending";
			break;
		case "category":
			/*
			 * if you can't view all the torrents, this is an error
			 */
			if ($categoryparam=="category=all") {
				$order = " ORDER BY category $sortorder, filename ";
				if ($sortascending && !($advancedsort && !isset($_GET["order"]) && !isset($_GET["sort"]) && $usedefcat && $requestedcategory != "all")) $categoryhref .= "&amp;sort=descending"; else $categoryhref .= "&amp;sort=ascending";
			} else {
				die("Invalid sort order specified.");
			}
			break;
		default:
			die("Invalid sort order specified.");
	}

	/*
	 * Filter out torrents and aren't supposed to be displayed
	 */
	$where .= "AND (namemap.DateToHideTorrent > CURDATE() OR namemap.DateToHideTorrent = \"0000-00-00\") ";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<LINK REL="stylesheet" HREF="../tracker.css" TYPE="text/css" TITLE="Default">
	<TITLE>Sample torrent statistics script</TITLE>
<?php
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
	 * If RSS support is enabled, build links for the live feeds...
	 */
	if ($show_rss && $GLOBALS["enable_rss"]) {
		require_once($rss_conf_path . "/rss_conf.php");
	
		if (isset($rss_heading[$defaultcategory])) {
			$rssfriendlyname = $rss_heading[$defaultcategory];
		} else {
			$rssfriendlyname = $defaultcategory;
		}
		
		if ($enable_rss_cache) {
			echo "\t<LINK REL=\"alternate\" TYPE=\"application/rss+xml\" TITLE=\"All torrents\" HREF=\"$rss_path_to_cache/index.xml\">\r\n";
			if ($defaultcategory != "all") {
				echo "\t<LINK REL=\"alternate\" TYPE=\"application/rss+xml\" TITLE=\"$rssfriendlyname\" HREF=\"$rss_path_to_cache/$defaultcategory.xml\">\r\n";		
			}

			if ($advancedsort) {
				$rstSubCat = mysql_query("SELECT `group_id`, `heading` FROM `subgrouping` WHERE `category` = \"$defaultcategory\" ORDER BY `groupsort`") or die("Can't get subcategory list: " . mysql_error());
				
				while ($rowSubCat = mysql_fetch_row($rstSubCat)) {
					echo "\t<LINK REL=\"alternate\" TYPE=\"application/rss+xml\" TITLE=\"$rssfriendlyname/$rowSubCat[1]\" HREF=\"$rss_path_to_cache/$rowSubCat[0].xml\">\r\n";							
				}
			}
		} else {
			echo "\t<LINK REL=\"alternate\" TYPE=\"application/rss+xml\" TITLE=\"All torrents\" HREF=\"$rss_path_to_script/rss.php\">\r\n";
			if ($defaultcategory != "all") {
				echo "\t<LINK REL=\"alternate\" TYPE=\"application/rss+xml\" TITLE=\"$rssfriendlyname\" HREF=\"$rss_path_to_script/rss.php?category=$defaultcategory\">\r\n";							
			}
			if ($advancedsort) {
				$rstSubCat = mysql_query("SELECT `group_id`, `heading` FROM `subgrouping` WHERE `category` = \"$defaultcategory\" ORDER BY `groupsort`") or die("Can't get subcategory list: " . mysql_error());
				
				while ($rowSubCat = mysql_fetch_row($rstSubCat)) {
					echo "\t<LINK REL=\"alternate\" TYPE=\"application/rss+xml\" TITLE=\"$rssfriendlyname/$rowSubCat[1]\" HREF=\"$rss_path_to_cache/$rowSubCat[0].xml\">\r\n";							
				}
			}
		}
	}
?>
</HEAD>

<BODY>
	<CENTER>
	<TABLE CLASS="trkOuter">
	<TR>
		<TD CLASS="mainCells" COLSPAN=2><H1>Sample torrent statistics script</H1><BR>&nbsp;</TD>
	</TR>
	<TR>
		<TD CLASS="mainCells" COLSPAN=2>		
			<TABLE CLASS="trkInner">
<?php 
	/*
	 * Show column headers, and if sorting is allowed, show hyperlinks to sort the columns
	 */
	if ($allowsorting) {
		echo "\t\t\t<TR><TH CLASS=\"trkSortHeading\" COLSPAN=11>To sort the data, click on the column header hyperlinks.</TH></TR>\r\n";
		echo "\t\t\t<TR>\r\n";
		echo "\t\t\t\t<TH CLASS=\"trkHeading colName\"><A HREF=\"$namehref\">Name/Info Hash</A></TH>\r\n";
		if ($showsize) echo "\t\t\t\t<TH CLASS=\"trkHeading colSize\"><A HREF=\"$sizehref\">Size</A></TH>\r\n";
		if ($showcrc) echo "\t\t\t\t<TH CLASS=\"trkHeading colCRC\">CRC32</TH>\r\n";
		if ($showadded) echo "\t\t\t\t<TH CLASS=\"trkHeading colAdded\"><A HREF=\"$datehref\">Date<BR>Added</A></TH>\r\n"; 
		if ($showseeds) echo "\t\t\t\t<TH CLASS=\"trkHeading colSeeds\"><A HREF=\"$seedhref\">UL</A></TH>\r\n";
		if ($showleechers) echo "\t\t\t\t<TH CLASS=\"trkHeading colLeechers\"><A HREF=\"$leechhref\">DL</A></TH>\r\n";
		if ($showcompleted) echo "\t\t\t\t<TH CLASS=\"trkHeading colDone\"><A HREF=\"$donehref\">DONE</A></TH>\r\n";
		if ($showxferred && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TH CLASS=\"trkHeading colXfer\"><A HREF=\"$xferhref\">XFER</A></TH>\r\n";
		if ($showavg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t\t\t\t<TH CLASS=\"trkHeading colAvg\"><A HREF=\"$avghref\">Avg %<BR>Done</A></TH>\r\n";
		if ($showspd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TH CLASS=\"trkHeading colSpd\"><A HREF=\"$spdhref\">Speed</A></TH>\r\n";
		if ($requestedcategory=="all") echo "\t\t\t\t<TH CLASS=\"trkHeading colCategory\"><A HREF=\"$categoryhref\">Category</A></TH>\r\n";
	} else {
		echo "\t\t\t<TR>\r\n";
		echo "\t\t\t\t<TH CLASS=\"trkHeading colName\">Name/Info Hash</TH>\r\n";
		if ($showsize) echo "\t\t\t\t<TH CLASS=\"trkHeading colSize\">Size</TH>\r\n";
		if ($showcrc) echo "\t\t\t\t<TH CLASS=\"trkHeading colCRC\">CRC32</TH>\r\n";
		if ($showadded) echo "\t\t\t\t<TH CLASS=\"trkHeading colAdded\">Date<BR>Added</TH>\r\n"; 
		if ($showseeds) echo "\t\t\t\t<TH CLASS=\"trkHeading colSeeds\">UL</TH>\r\n";
		if ($showleechers) echo "\t\t\t\t<TH CLASS=\"trkHeading colLeechers\">DL</TH>\r\n";
		if ($showcompleted) echo "\t\t\t\t<TH CLASS=\"trkHeading colDone\">DONE</TH>\r\n";
		if ($showxferred && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TH CLASS=\"trkHeading colXfer\">XFER</TH>\r\n";
		if ($showavg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t\t\t\t<TH CLASS=\"trkHeading colAvg\">Avg %<BR>Done</TH>\r\n";
		if ($showspd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TH CLASS=\"trkHeading colSpd\">Speed</TH>\r\n";
		if ($requestedcategory=="all") echo "\t\t\t\t<TH CLASS=\"trkHeading colCategory\">Category</TH>\r\n";
	}

	echo "\t\t\t</TR>\r\n";

	if ($advancedsort && !isset($_GET["order"]) && !isset($_GET["sort"]) && $usedefcat && $requestedcategory != "all" && !$GLOBALS["dynamic_torrents"]) {
		/*
		 * Attempt to do some advanced sorting.
		 */
		$query = "SELECT summary.info_hash, 
			namemap.filename, 
			namemap.url, 
			namemap.mirrorurl, 
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
			namemap.infolink,
			namemap.sfvlink,
			namemap.md5link,
			namemap.DateToRemoveURL,
			namemap.DateToHideTorrent,
			summary.external_torrent,
			summary.ext_no_scrape_update,
			namemap.show_comment,
			namemap.comment,
			subgrouping.group_id,
			subgrouping.heading FROM summary 
				LEFT JOIN namemap ON summary.info_hash = namemap.info_hash 
				LEFT OUTER JOIN subgrouping ON subgrouping.group_id = namemap.grouping $where ORDER BY subgrouping.groupsort, namemap.sorting";
	} else {
		/*
		 * Query the database for the torrents
		 */
		$query = "SELECT summary.info_hash, 
			namemap.filename, 
			namemap.url, 
			namemap.mirrorurl, 
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
			namemap.infolink,
			namemap.sfvlink,
			namemap.md5link,
			namemap.DateToRemoveURL,
			namemap.DateToHideTorrent,
			summary.external_torrent,
			summary.ext_no_scrape_update,
			namemap.show_comment,
			namemap.comment FROM summary 
				LEFT JOIN namemap ON summary.info_hash = namemap.info_hash $where $order";
	}

	$recordset = mysql_query($query) or die("Can't do SQL query - ".mysql_error());

	/*
	 * Classes used to alternate background colour in rows
	 */
	$clrRowBG[0] = 'trkOdd';
	$clrRowBG[1] = 'trkEven';

	/*
	 * Keep track of totals for summary line
	 */
	$totaltorrents = 0;
	$totalsize=0;
	$totalseeders=0;
	$totalleechers=0;
	$totalcomplete=0;
	$totalxferred=0;
	$totalspeed=0;

	/*
	 * Ditto for external torrents
	 */
	$totalexttorrents = 0;
	$totalextsize=0;
	$totalextseeders=0;
	$totalextleechers=0;
	$totalextcomplete=0;
	$totalextxferred=0;
	$totalextspeed=0;

	$totalglobaltorrents=0;

	/*
	 * Figure out how many columns we need to account for when spanning them 
	 */
	$colspan = 0;
	$commentcolspan = 0;
	if ($showcrc) $commentcolspan++;
	if ($showadded) $commentcolspan++;
	if ($showseeds) $colspan++;
	if ($showleechers) $colspan++;
	if ($showcompleted) $colspan++;
	if ($showxferred) $colspan++;
	if ($showavg) $colspan++;
	if ($showspd) $colspan++;

	$commentcolspan += $colspan;
	
	/*
	 * Get the current timestamp for checking whether or not to
	 * hide things
	 */
	$today = time();

	$advancedsortcounter = "";

	/*
	 * Go through recordset, showing torrents
	 */
	while ($row=mysql_fetch_row($recordset)) 	{
		/*
		 * Although there should not be any null values in the database
		 * we will double check it before displaying them
		 */
		if (is_null($row[1])) $row[1] = $row[0];   //filename
		if (is_null($row[2])) $row[2] = "";        //url
		if (is_null($row[4])) $row[4] = "";        //description
		if (is_null($row[15])) $row[15] = "";      //infolink
		if (is_null($row[16])) $row[16] = "";      //sfvlink
		if (is_null($row[17])) $row[17] = "";      //md5link
		if (strlen($row[1]) == 0) $row[1]=$row[0]; //filename check

		$rowBackground = $clrRowBG[$totalglobaltorrents % 2];

		if ($advancedsort && !isset($_GET["order"]) && !isset($_GET["sort"]) && $row[25] != $advancedsortcounter && !$GLOBALS["dynamic_torrents"]) {
			echo "\t\t\t<TR>\r\n\t\t\t\t<TD COLSPAN=15 CLASS=\"$rowBackground trkData grpHeading\">$row[25]</TD>\r\n\t\t\t</TR>\r\n";
			$advancedsortcounter = $row[25];
			$totalglobaltorrents++;
			$rowBackground = $clrRowBG[$totalglobaltorrents % 2];
		}

		echo "\t\t\t<TR>\r\n";
	
		echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colName\">";

		/*
		 * Torrent name, info, alternate links
		 *
		 * Show http link if present and check
		 * to make sure link is not supposed to be hidden.
		 */
		$hideurldatearray = explode("-", $row[18]);
		if (strlen($row[2]) > 0) {
			if ($row[18] == "0000-00-00" || mktime(0,0,0,$hideurldatearray[1], $hideurldatearray[2], $hideurldatearray[0]) > $today) {
				echo "<A HREF=\"${row[2]}\">${row[1]}</A>";
			} else {
				echo "$row[1]";
			}
		}
			else
				echo "$row[1]";
			
		/*
		 * Show description, if one
		 */
		if (strlen($row[4]) > 0)
			echo " <DIV CLASS=\"torrentTag\">(${row[4]})</DIV>";
	
		/*
		 * Show alternate http link, if any
		 */
		if (strlen($row[3]) > 0)
			echo "<DIV CLASS=\"torrentTag\">&nbsp[<A HREF=\"${row[3]}\">Alternate Link</A>]</FONT>";

		/*
		 * Show information link, if one
		 */
		if (strlen($row[15]) > 0)
			echo "<DIV CLASS=\"torrentTag\">&nbsp[<A HREF=\"${row[15]}\">Info</A>]</DIV>";

		/*
		 * If this is an updatable external torrent, indicate so
		 */
		if ($row[20] == 'Y' && $row[21] == 'N')
			echo "<DIV CLASS=\"torrentTag\">&nbsp;&nbsp;(&nbsp;External torrent&nbsp;)</DIV>";

		echo "</TD>\r\n";

		/*
		 * Torrent size column
		 */
		if ($row[5] > 1024) $fsize=round($row[5]/1024,1) . "<BR>GiB"; else $fsize=round($row[5],1)."<BR>MiB";
		if ($showsize) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSize\">$fsize</TD>\r\n";

		/*
		 * Decision point: show comment??
		 */
		if ($row[22] == 'Y') {
			/*
			 * Show the comment.
			 */
				echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colComment\" COLSPAN=$commentcolspan>$row[23]</TD>";			
		} else {
			/*
			 * Output like normal
			 */

			/*
			 * CRC column
			 */
			if ($showcrc) {
				echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colCRC\">$row[6]";
				if (strlen($row[16]) > 0) 
					echo "<DIV CLASS=\"hashTag\">[<A HREF=\"$row[16]\">SFV File</A>]</DIV>";
				if (strlen($row[17]) > 0)
					echo "<DIV CLASS=\"hashTag\">[<A HREF=\"$row[17]\">MD5 File</A>]</DIV>";
				echo "</TD>\r\n";
			}
	
			/*
			 * Date added column
			 */
			if ($showadded) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colAdded\">$row[7]</TD>\r\n";

			/*
			 * Decision point: If torrent is an external non-updatable type
			 * indicate so
			 */
			if ($row[21] == 'Y') {
				if ($colspan > 0)
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSeeds\" COLSPAN=$colspan>External torrent - not updatable</TD>\r\n";
			} else {
				if ($row[20] == 'Y' && $row[21] == 'N') {
					/*
					 * If seeds, leechers, and number of downloads are all 0, torrent wasn't updated
					 */
					if ($row[8] == 0 && $row[9] ==0 && $row[10] == 0) {
						/*
						 * Seeds column
						 */
						if ($row[8] == 0) $row[8] = '?';
						if ($showseeds) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSeeds\">$row[8]</TD>\r\n";

						/*
						 * Leechers column
						 */
						if ($row[9] == 0) $row[9] = '?';
						if ($showleechers) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colLeechers\">$row[9]</TD>\r\n";

						/*
						 * Number of downloads column
						 */
						if ($row[10] == 0) $row[10] = '?';
						if ($showcompleted) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colDone\">$row[10]</TD>\r\n";
					} else {
						/*
						 * Seeds column
						 */
						if ($showseeds) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSeeds\">$row[8]</TD>\r\n";

						/*
						 * Leechers column
						 */
						if ($showleechers) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colLeechers\">$row[9]</TD>\r\n";

						/*
						 * Number of downloads column
						 */
						if ($showcompleted) {
							if ($row[10]==0) {
								echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colDone\">?</TD>\r\n";						
							} else {
								echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colDone\">$row[10]</TD>\r\n";
							}
						}
					}
	
					/*
					 * Show terabytes, if needed
					 */
					if ($row[11] > 1099511627776)
						$xferred = round($row[11]/1099511627776,2) . "<BR>TiB";
					else
						$xferred = round($row[11]/1073741824,1) . "<BR>GiB";

					/*
					 * Amount transferred column
					 */
					if ($row[11] == 0) $xferred = '?';
					if ($showxferred && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colXfer\">$xferred</TD>\r\n";
	
					/*
					 * Average percentage done column
					 */
					$avgdone = round($row[12],1) . "%";
					if ($row[12] == 0) $avgdone = '?';
					if ($showavg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colAvg\">$avgdone</TD>\r\n";

					/*
					 * Show megabytes/sec, kilobytes/sec, or Stalled depending on torrent speed
					 */
					if ($row[13] <= 0)
						$speed = "Stalled";
					else if ($row[13] > 2097152)
						$speed = round($row[13]/1048576,2) . "<BR>MiB/sec";
					else
						$speed = round($row[13] / 1024, 2) . "<BR>KiB/sec";

					if ($row[13] == 0) $speed = '?';		
					if ($showspd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSpd\">$speed</TD>\r\n";
				} else {
					/*
					 * Seeds column
					 */
					if ($showseeds) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSeeds\">$row[8]</TD>\r\n";

					/*
					 * Leechers column
					 */
					if ($showleechers) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colLeechers\">$row[9]</TD>\r\n";

					/*
					 * Number of downloads column
					 */
					if ($showcompleted) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colDone\">$row[10]</TD>\r\n";
	
					/*
					 * Show terabytes, if needed
					 */
					if ($row[11] > 1099511627776)
						$xferred = round($row[11]/1099511627776,2) . "<BR>TiB";
					else
						$xferred = round($row[11]/1073741824,1) . "<BR>GiB";
	
					/*
					 * Amount transferred column
					 */
					if ($showxferred && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colXfer\">$xferred</TD>\r\n";

					/*
					 * Average percentage done column
					 */
					if ($showavg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colAvg\">". round($row[12],1) . "%</TD>\r\n";
	
					/*
					 * Show megabytes/sec, kilobytes/sec, or Stalled depending on torrent speed
					 */
					if ($row[13] <= 0)
						$speed = "Stalled";
					else if ($row[13] > 2097152)
						$speed = round($row[13]/1048576,2) . "<BR>MiB/sec";
					else
						$speed = round($row[13] / 1024, 2) . "<BR>KiB/sec";
	
					if ($showspd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSpd\">$speed</TD>\r\n";
				}
			}
		}

		/*
		 * Category column
		 */
		if ($requestedcategory=="all") echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colCategory\">$row[14]</TD>\r\n";
		
		echo "\t\t\t</TR>\r\n";

		/*
		 * Keep running total for summary line
		 */
		if ($row[20] == 'Y') {
			$totalexttorrents++;
			$totalextsize += $row[5];
			$totalextseeders += $row[8];
			$totalextleechers += $row[9];
			$totalextcomplete += $row[10];
			$totalextxferred += $row[11];
			$totalextspeed += $row[13];
		} else {
			$totaltorrents++;
			$totalsize += $row[5];
			$totalseeders += $row[8];
			$totalleechers += $row[9];
			$totalcomplete += $row[10];
			$totalxferred += $row[11];
			$totalspeed += $row[13];
		}

		$totalglobaltorrents++;
	}

	/*
	 * If no torrents to display, indicate so. If torrents were listed
	 * show summary line if requested
	 */
	if ($totalglobaltorrents == 0)
		echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD CLASS=\"$clrRowBG[0] trkData colSummary\" COLSPAN=11>No active torrents</TD>\r\n\t\t\t\t</TR>\r\n";
	else {
		if ($showsummary) {
			$rowBackground = $clrRowBG[$totalglobaltorrents % 2];

			if ($totalexttorrents > 0)
				echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"$rowBackground trkData colName\"><B>Currently tracking $totaltorrents torrent(s)</B>. [Plus $totalexttorrents external torrents.]</TD>\r\n";
			else
				echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"$rowBackground trkData colName\"><B>Currently tracking $totaltorrents torrent(s)</B>.</TD>\r\n";

			/*
			 * Show gigabytes in total size if necessary
			 */
			if ($totalsize > 1200)
				$totalsize = round($totalsize / 1024, 2) . " GiB";
			else
				$totalsize = round($totalsize, 2) . " MiB";

			/*
			 * Same for external torrents
			 */
			if ($totalextsize > 1200)
				$totalextsize = round($totalextsize / 1024, 2) . " GiB";
			else
				$totalextsize = round($totalextsize, 2) . " MiB";

			if ($showsize) {
				if ($totalexttorrents > 0)
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">$totalsize<BR>[$totalextsize]</TD>\r\n";
				else
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">$totalsize</TD>\r\n";
			}

			if ($showcrc) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">---</TD>\r\n";
			if ($showadded) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">---</TD>\r\n";

			if ($showseeds) {
				if ($totalexttorrents > 0)
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">$totalseeders<BR>[$totalextseeders]</TD>\r\n";
				else
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">$totalseeders</TD>\r\n";
			}

			if ($showleechers) {
				if ($totalexttorrents > 0)
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">$totalleechers<BR>[$totalextleechers]</TD>\r\n";	
				else
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">$totalleechers</TD>\r\n";	
			}

			if ($showcompleted) {
				if ($totalexttorrents > 0)
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">$totalcomplete<BR>[$totalextcomplete]</TD>\r\n";
				else
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">$totalcomplete</TD>\r\n";
			}

			/*
			 * Show terabytes in total transferred if necessary
			 */
			if ($totalxferred > 1099511627776)
				$totalxferred = round($totalxferred/1099511627776,2) . " TiB";
			else
				$totalxferred = round($totalxferred/1073741824,1) . " GiB";

			/*
			 * Same for external torrents
			 */
			if ($totalextxferred > 1099511627776)
				$totalextxferred = round($totalextxferred/1099511627776,2) . " TiB";
			else
				$totalextxferred = round($totalextxferred/1073741824,1) . " GiB";

			if ($totalextxferred == 0) $totalextxferred = "?";


			if ($showxferred && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) {
				if ($totalexttorrents > 0)
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">$totalxferred<BR>[$totalextxferred]</TD>\r\n";
				else
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">$totalxferred</TD>\r\n";
			}

			if ($showavg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">---</TD>\r\n";

			/*
			 * Show megabytes/sec in total speed if necessary
			 */
			if ($totalspeed <= 0) {
				$totalspeed = "0 KiB/sec";
			} elseif ($totalspeed > 2097152) {
				$totalspeed = round($totalspeed/1048576,2) . " MiB/sec";
			} else {
				$totalspeed = round($totalspeed / 1024, 2) . " KiB/sec";
			}

			/*
			 * Again, check external torrents
			 */
			if ($totalextspeed <= 0) {
				$totalextspeed = "0 KiB/sec";
			} elseif ($totalextspeed > 2097152) {
				$totalextspeed = round($totalextspeed/1048576,2) . " MiB/sec";
			} else {
				$totalextspeed = round($totalextspeed / 1024, 2) . " KiB/sec";
			}

			if ($totalextspeed == 0) $totalextspeed = "?";

			if ($showspd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) {
				if ($totalexttorrents > 0)
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">$totalspeed<BR>[$totalextspeed]</TD>\r\n";
				else
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">$totalspeed</TD>\r\n";
			}

			if ($requestedcategory=="all")
				echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">---</TD>\r\n";

			echo "\t\t\t</TR>\r\n";
		}

	}

	echo "\t\t\t</TABLE>\r\n\t\t</TD>\r\n\t</TR>\r\n";

	/*
	 * Tracker summary line with version, etc...
	 */
	echo "\t<TR>\r\n\t\t<TD CLASS=\"versionLine\">$phpbttracker_id $phpbttracker_ver using MySQL.</TD>\r\n";
	echo "\t\t<TD CLASS=\"scaleLine\">Ki = 1024, Mi = 1024 Ki, Gi = 1024 Mi, Ti = 1024 Gi</TD>\r\n\t</TR>\r\n";

	if ($totalexttorrents > 0)
		echo "\t<TR>\r\n\t\t<TD CLASS=\"infoTag\" COLSPAN=2>Items in square brackets in the summary line indicate external torrent statistics.</TD>\r\n\t</TR>\r\n";

	/*
	 * Show the refresh values for the speed and average stats
	 */
	if ($showavg || $showspd) {
		if ($showavg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t<TR>\r\n\t\t<TD CLASS=\"infoTag\" COLSPAN=2>Torrent average progress statistics updated every " . round($GLOBALS["avgrefresh"] / 60, 1) . " minute(s).</TD>\r\n\t</TR>\r\n";
		if ($showspd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t<TR>\r\n\t\t<TD CLASS=\"infoTag\" COLSPAN=2>Torrent speed updated every " . round($GLOBALS["spdrefresh"] / 60, 1) . " minute(s).</TD>\r\n\t</TR>\r\n";
		echo "\t<TR><TD>&nbsp</TD></TR>\r\n";
	}

	/*
	 * Calculate time it took to generate this page.
	 */
	$stoptime = mtime_float();
	$pagegen_time = round($stoptime - $starttime, 4);
	echo "\t<TR>\r\n\t\t<TD CLASS=\"infoTag\" COLSPAN=2>This page was generated in $pagegen_time seconds.</TD>\r\n\t</TR>\r\n";

	/*
	 * If requested show the retired torrent page
	 */
	if ($showRetiredLink)
		echo "\t<TR>\r\n\t\t<TD CLASS=\"retiredLink\" COLSPAN=2><A HREF=\"$retiredpage\">Click to see retired/inactive torrents.</A></TD>\r\n\t</TR>\r\n";

	if ($show_rss && $GLOBALS["enable_rss"]) {
		if ($enable_rss_cache) {
			echo "\t<TR><TD CLASS=\"mainCells\" COLSPAN=2><A HREF=\"$rss_path_to_cache/index.xml\"><IMG BORDER=\"0\" SRC=\"$rss_path_to_script/rss.gif\" ALT=\"RSS\" height=\"15\" width=\"37\"></A></TD></TR>\r\n";
		} else {
			echo "\t<TR><TD CLASS=\"mainCells\" COLSPAN=2><A HREF=\"$rss_path_to_script/rss.php\"><IMG BORDER=\"0\" SRC=\"$rss_path_to_script/rss.gif\" ALT=\"RSS\" height=\"15\" width=\"37\"></A></TD></TR>\r\n";		
		}
	} else {
		echo "\t<TR><TD>&nbsp</TD></TR>\r\n";
	}

	/*
	 * This initial script is 4.01 compliant, so show it. If you make modifications, make sure it is still compliant, or remove this!
	 */	
	echo "\t<TR><TD CLASS=\"mainCells\" COLSPAN=11><A HREF=\"http://validator.w3.org/check/referer\"><IMG BORDER=\"0\" SRC=\"http://www.w3.org/Icons/valid-html401\" ALT=\"Valid HTML 4.01!\" height=\"31\" width=\"88\"></A>";
	echo "<A HREF=\"http://jigsaw.w3.org/css-validator/\"><IMG STYLE=\"border:0;width:88px;height:31px\" SRC=\"http://jigsaw.w3.org/css-validator/images/vcss\" ALT=\"Valid CSS!\"></A></TD></TR>\r\n";
?>
	</TABLE>
	</CENTER>
</BODY>
</HTML>
