<?php
	/*
	 *	Script name: basic_index_sample.php
	 * Description: This is a *really* basic script to show stats. The only "extra" features are: 
	 *                 - ability to show different categories via parameter ( ?category= )
	 *                 - show and hide columns in the torrent table
	 *                 - show/hide summary stats line
	 *                 - show/hide the retired torrents hyperlink
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
	$defaultcategory= "main";
	$catrestrict = false;
	$allowall = true;
	$advancedsort = false;

	/*
	 * Show/hide columns: almost all the columns available are customizable
	 * Set the associated variable to true to show the column; false to hide it
	 *
	 * Here are the columns that can be hidden:
	 *   size:      the size of the torrent
	 *   crc:       the CRC32 value of the torrent
	 *   added:     the date torrent was added to the tracker
	 *   seeds:     the number of seeds on the torrent
	 *   leechers:  the number of leechers on the torrent
	 *   completed: the total number of completed downloads during the life of the torrent
	 *   xferred:   the total amount of bandwidth transferred during the life of the torrent
	 *   avg:       the average progress on the torrent
	 *   spd:       the current speed of the torrent
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
	 * This script will automatically show a link to the retired torrents page
	 * (retired.php) unless you set this to false.
	 */
	$showRetiredLink = true;
	$retiredpage = "retired.php";

	/*
	 * The script can total the data displayed and show it at the bottom of the torrent table.
	 * Set this to false if you don't want a summary.
	 */
	$showsummary = true;

	/*
	 * Colours used to decorate the tracker table.
	 * You can use "#000000" - "#FFFFFF" or one of the following:
	 * "black", "white", "red", "blue", "green", "yellow", "orange",
	 * "violet", "purple", "pink", "silver", "gold", "gray", "aqua",
	 * "skyblue", "lightblue", "fuchsia", "khaki"
	 */
	/*
	 * tracker table background
	 */
	$clrTableBG = "black";

	/*
	 * column headings - BG = background, FG = foreground
	 */
	$clrHeaderBG = "#c0c0c0";

	/*
	 * tracker table background
	 */
	//alternating background colours in the data part of the table
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
	 * You may need to modify the locations on these depending on where you install the scripts.
	 */
	require_once ("version.php");
	require_once ("config.php");

	/*
	 * Don't change anything below this comment unless you know what you
	 * are doing...
	 *
	 *
	 * By default, we want to only show torrents that
	 * are not hidden.
	 */
	$where = " WHERE summary.hide_torrent=\"N\" ";

	if (isset($_GET["category"])) {
		if (strpos($_GET["category"], " ")) die("Invalid parameter passed.");

		$requestedcategory = $_GET["category"];

		if (strcmp($requestedcategory, $defaultcategory) == 0)
			$usedefcat = true;
		else
			$usedefcat = false;
	} else {
		$requestedcategory = $defaultcategory;
		$usedefcat = true;
	}

	switch ($requestedcategory) {
		case "all":
			if ($catrestrict)
				die("Forbidden: You cannot view all the groups.");
			break;
		default:
			if (!$catrestrict) {
				//no restrictions
				$where .= "AND namemap.category = \"" . $requestedcategory . "\" ";
			} else {
				if ($usedefcat) {
					$where .= "AND namemap.category = \"" . $requestedcategory . "\" ";
				} else {
					//it's restricted, stop processing
					die("Forbidden: You cannot specify a category for this page.");
				}
			}
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
	<TITLE>Basic torrent statistics script</TITLE>
</HEAD>

<?php echo"<BODY BGCOLOR=\"$clrBodyBG\" TEXT=\"$clrBodyText\" LINK=\"$clrBodyLink\" VLINK=\"$clrBodyVlink\">"; ?>	
	<CENTER>
	<H1>Basic torrent statistics page</H1><BR>
	<TABLE BORDER=0>
<?php 

	echo "\t<TR>\r\n\t\t<TD ALIGN=RIGHT COLSPAN=2>\r\n\t\t\t<TABLE BORDER=0 CELLPADDING=5 CELLSPACING=1 BGCOLOR=\"$clrTableBG\" WIDTH=\"100%\">\r\n\t\t\t<TR>\r\n";

	/*
	 * Column Headers
	 */
	echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"left\" VALIGN=\"bottom\">Name/Info Hash</TH>\r\n";
	if ($showsize) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"center\" VALIGN=\"bottom\">Size</TH>\r\n";
	if ($showcrc) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"right\"  VALIGN=\"bottom\">CRC32</TH>\r\n";
	if ($showadded) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"center\" VALIGN=\"bottom\">Date<BR>Added</TH>\r\n"; 
	if ($showseeds) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"center\" VALIGN=\"bottom\">UL</TH>\r\n";
	if ($showleechers) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"center\" VALIGN=\"bottom\">DL</TH>\r\n";
	if ($showcompleted) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"center\" VALIGN=\"bottom\">DONE</TH>\r\n";
	if ($showxferred && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"center\" VALIGN=\"bottom\">XFER</TH>\r\n";
	if ($showavg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"center\" VALIGN=\"bottom\">Avg %<BR>Done</TH>\r\n";
	if ($showspd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TH BGCOLOR=\"$clrHeaderBG\" ALIGN=\"center\" VALIGN=\"bottom\">Speed</TH>\r\n";
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

	/*
	 * Query the database for the torrents
	 */
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

	$recordset = mysql_query($query) or die("Can't run query - ".mysql_error());

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
	 * Let's keep external stats seperate
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
	 * Figure out how many columns we need to account for when spanning them 
	 * (used for external torrents)
	 */
	$colspan = 0;
	if ($showseeds) $colspan++;
	if ($showleechers) $colspan++;
	if ($showcompleted) $colspan++;
	if ($showxferred) $colspan++;
	if ($showavg) $colspan++;
	if ($showspd) $colspan++;

	/*
	 * Get the current timestamp for checking whether or not to
	 * hide things
	 */
	$today = time();

	$advancedsortcounter = "";

	/*
	 * Go through the recordset, displaying each as necessary
	 */	
	while ($row=mysql_fetch_row($recordset))	{
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

		if ($advancedsort && !isset($_GET["order"]) && !$GLOBALS["dynamic_torrents"] && !isset($_GET["sort"]) && $row[25] != $advancedsortcounter) {
			echo "\t\t\t<TR>\r\n\t\t\t\t<TD COLSPAN=15 ALIGN=CENTER $rowBackground><B>$row[25]</B></TD>\r\n\t\t\t</TR>\r\n";
			$advancedsortcounter = $row[25];
			$totalglobaltorrents++;
			$rowBackground = $clrRowBG[$totalglobaltorrents % 2];
		}

		echo "\t\t\t<TR>\r\n";
	
		echo "\t\t\t\t<TD $rowBackground>";

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
			echo " (${row[4]})";
	
		/*
		 * Show alternate http link, if any
		 */
		if (strlen($row[3]) > 0)
			echo "<FONT SIZE=-2>&nbsp[</FONT><FONT SIZE=-2><A HREF=\"${row[3]}\"><FONT FACE=\"Arial\">Alternate Link</FONT></A><FONT>]</FONT></FONT>";

		/*
		 * Show information link, if one
		 */
		if (strlen($row[15]) > 0)
			echo "<FONT SIZE=-2>&nbsp[</FONT><FONT SIZE=-2><A HREF=\"${row[15]}\"><FONT FACE=\"Arial\">Info</FONT></A>]</FONT>";

		/*
		 * If this is an updatable external torrent, indicate so
		 */
		if ($row[20] == 'Y' && $row[21] == 'N')
			echo "<FONT SIZE=-1>&nbsp;&nbsp;(&nbsp;External torrent&nbsp;)</FONT>";

		echo "</TD>\r\n";

		/*
		 * Torrent size column
		 */
		if ($row[5] > 1024) $fsize=round($row[5]/1024,1) . "<BR>GiB"; else $fsize=round($row[5],1)."<BR>MiB";
		if ($showsize) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$fsize</TD>\r\n";

		/*
		 * Decision point: show comment??
		 */
		if ($row[22] == 'Y') {
			/*
			 * Show the comment.
			 */
				echo "\t\t\t\t<TD $rowBackground COLSPAN=10 ALIGN=\"center\">$row[23]</TD>";			
		} else {
			/*
			 * Output like normal
			 */

			/*
			 * CRC column
			 */
			if ($showcrc) {
				echo "\t\t\t\t<TD $rowBackground ALIGN=\"right\">$row[6]";
				if (strlen($row[16]) > 0) 
					echo "<BR><FONT SIZE=-2>[<A HREF=\"$row[16]\"><FONT FACE=\"Arial\">SFV File</FONT></A>]</FONT>";
				if (strlen($row[17]) > 0)
					echo "<BR><FONT SIZE=-2>[<A HREF=\"$row[17]\"><FONT FACE=\"Arial\">MD5 File</FONT></A>]</FONT>";
				echo "</TD>\r\n";
			}
	
			/*
			 * Date added column
			 */
			if ($showadded) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\" WIDTH=\"85\">$row[7]</TD>\r\n";

			/*
			 * Decision point: If torrent is an external non-updatable type
			 * indicate so
			 */
			if ($row[21] == 'Y') {
				if ($colspan > 0)
					echo "\t\t\t\t<TD $rowBackground COLSPAN=$colspan ALIGN=\"center\">External torrent - not updatable</TD>\r\n";
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
						if ($showseeds) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$row[8]</TD>\r\n";

						/*
						 * Leechers column
						 */
						if ($row[9] == 0) $row[9] = '?';
						if ($showleechers) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$row[9]</TD>\r\n";

						/*
						 * Number of downloads column
						 */
						if ($row[10] == 0) $row[10] = '?';
						if ($showcompleted) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$row[10]</TD>\r\n";
					} else {
						/*
						 * Seeds column
						 */
						if ($showseeds) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$row[8]</TD>\r\n";

						/*
						 * Leechers column
						 */
						if ($showleechers) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$row[9]</TD>\r\n";

						/*
						 * Number of downloads column
						 */
						if ($showcompleted) {
							if ($row[10]==0) {
								echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">?</TD>\r\n";						
							} else {
								echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$row[10]</TD>\r\n";
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
					if ($showxferred && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$xferred</TD>\r\n";
	
					/*
					 * Average percentage done column
					 */
					$avgdone = round($row[12],1) . "%";
					if ($row[12] == 0) $avgdone = '?';
					if ($showavg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$avgdone</TD>\r\n";

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
					if ($showspd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$speed</TD>\r\n";
				} else {
					/*
					 * Seeds column
					 */
					if ($showseeds) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$row[8]</TD>\r\n";

					/*
					 * Leechers column
					 */
					if ($showleechers) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$row[9]</TD>\r\n";

					/*
					 * Number of downloads column
					 */
					if ($showcompleted) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$row[10]</TD>\r\n";
	
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
					if ($showxferred && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$xferred</TD>\r\n";

					/*
					 * Average percentage done column
					 */
					if ($showavg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">". round($row[12],1) . "%</TD>\r\n";
	
					/*
					 * Show megabytes/sec, kilobytes/sec, or Stalled depending on torrent speed
					 */
					if ($row[13] <= 0)
						$speed = "Stalled";
					else if ($row[13] > 2097152)
						$speed = round($row[13]/1048576,2) . "<BR>MiB/sec";
					else
						$speed = round($row[13] / 1024, 2) . "<BR>KiB/sec";
	
					if ($showspd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$speed</TD>\r\n";
				}
			}
		}

		/*
		 * Category column
		 */
		if ($requestedcategory=="all") echo "\t\t\t\t<TD $rowBackground ALIGN=\"center\">$row[14]</TD>\r\n";
		
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
		echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD $clrRowBG[0] COLSPAN=11 ALIGN=CENTER>No active torrents</TD>\r\n\t\t\t\t</TR>\r\n";
	else {
		if ($showsummary) {
			$rowBackground = $clrRowBG[$totalglobaltorrents % 2];

			if ($totalexttorrents > 0)
				echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD $rowBackground ALIGN=\"LEFT\"><B>Currently tracking $totaltorrents torrent(s)</B>. [$totalexttorrents torrent(s) are external torrents.]</TD>\r\n";
			else
				echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD $rowBackground ALIGN=\"LEFT\"><B>Currently tracking $totaltorrents torrent(s)</B>.</TD>\r\n";

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
					echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalsize<BR>[$totalextsize]</TD>\r\n";
				else
					echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalsize</TD>\r\n";
			}

			if ($showcrc) echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">---</TD>\r\n";
			if ($showadded) echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">---</TD>\r\n";

			if ($showseeds) {
				if ($totalexttorrents > 0)
					echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalseeders<BR>[$totalextseeders]</TD>\r\n";
				else
					echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalseeders</TD>\r\n";
			}

			if ($showleechers) {
				if ($totalexttorrents > 0)
					echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalleechers<BR>[$totalextleechers]</TD>\r\n";	
				else
					echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalleechers</TD>\r\n";	
			}

			if ($showcompleted) {
				if ($totalexttorrents > 0)
					echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalcomplete<BR>[$totalextcomplete]</TD>\r\n";
				else
					echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalcomplete</TD>\r\n";
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
					echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalxferred<BR>[$totalextxferred]</TD>\r\n";
				else
					echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalxferred</TD>\r\n";
			}

			if ($showavg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">---</TD>\r\n";

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
					echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalspeed<BR>[$totalextspeed]</TD>\r\n";
				else
					echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">$totalspeed</TD>\r\n";
			}

			if ($requestedcategory=="all")
				echo "\t\t\t\t\t<TD $rowBackground ALIGN=\"center\">---</TD>\r\n";

			echo "\t\t\t\t</TR>\r\n";
		}

	}
	echo "\t\t\t</TABLE>\r\n\t\t</TD>\r\n\t</TR>\r\n";

	/*
	 * This is the version summary at the end of the torrent table.
	 */
	echo "\t<TR>\r\n\t\t<TD ALIGN=LEFT><FONT FACE=\"Arial\" SIZE=\"1\">$phpbttracker_id $phpbttracker_ver using MySQL.</FONT></TD>\r\n";
	echo "\t\t<TD ALIGN=RIGHT><FONT FACE=\"Arial\" SIZE=\"1\">Ki = 1024, Mi = 1024 Ki, Gi = 1024 Mi, Ti = 1024 Gi</FONT></TD>\r\n\t</TR>\r\n";

	if ($totalexttorrents > 0)
		echo "\t<TR>\r\n\t\t<TD COLSPAN=2 ALIGN=CENTER><FONT FACE=\"Arial\" SIZE=\"1\">Items in square brackets in the summary line indicate external torrent statistics.</FONT></TD>\r\n\t</TR>\r\n";

	/*
	 * Show the refresh values for the speed and average stats
	 */
	if ($showavg || $showspd) {
		if ($showavg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2><FONT FACE=\"Arial\" SIZE=\"1\">Torrent average progress statistics updated every " . round($GLOBALS["avgrefresh"] / 60, 1) . " minute(s).</FONT></TD>\r\n\t</TR>\r\n";
		if ($showspd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2><FONT FACE=\"Arial\" SIZE=\"1\">Torrent speed updated every " . round($GLOBALS["spdrefresh"] / 60, 1) . " minute(s).</FONT></TD>\r\n\t</TR>\r\n";
		echo "\t<TR><TD>&nbsp</TD></TR>\r\n";
	}

	/*
	 * If requested, show the retired torrents hyperlink
	 */
	if ($showRetiredLink)
		echo "\t<TR><TD ALIGN=CENTER COLSPAN=10><A HREF=\"$retiredpage\">Click to see retired/inactive torrents.</A></TD></TR>\r\n";

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
