<?php
	/*
	 * Module: dumpstats.php
	 * Description: Contains two functions for displaying stats. Include this
	 *              file in your script and call either dumpstats() or
	 *              dumpretiredstats(). These will dump stat output into
	 *              a formatted table. See below for optional parameters
	 *              for each function.
	 *
	 * Author: danomac
	 * Date: 12-Sept-04
	 *
	 * HOW TO USE THIS MODULE
	 * ----------------------
	 *
	 * First of all, set $GLOBALS["ds_scriptloc"] below to where your config.php and
	 * version.php are located. THIS SCRIPT WON'T WORK IF YOU SKIP THIS STEP.
	 *
	 * With that out of the way, there are two functions you can use:
	 *   - ds_DumpTorrentStats()        - This will insert a table with tracker 
	 *                                    information whereever you call it in 
	 *                                    your script. See below for
	 *                                    how to customize this.
	 *   - ds_DumpRetiredTorrentStats() - Similar but for retired torrents.
	 *                                    See below for how to customize it.
	 *
	 * Customizing ds_DumpTorrentStats():
	 *    The function has parameters IN THIS ORDER (defaults in square brackets []):
	 *     ConnectToDB [true]          -- Set to false if you make a connection to
	 *                                    the database prior to calling this function
	 *     Category ["main"]           -- Specify here the category to show
	 *     OrderBy ["filename"]*       -- Specify the ordering to be used
	 *     SortDescending ["false"]    -- Set to true to sort in descending order
	 *     ShowRetiredLink ["true"]    -- Set to false to not show the link to the
	 *                                    retired torrents page
	 *     RetiredPage ["retired.php"] -- Set this to the name of the retired torrent
	 *                                    page
	 *     ShowSize [true]             -- Set to false to hide the size column
	 *     ShowCRC [true]              -- Set to false to hide the CRC column
	 *     ShowAdded [true]            -- Set to false to hide the Date Added column
	 *     ShowSeeds [true]            -- Set to false to hide the seeder (UL) column
	 *     ShowLeechers [true]         -- Set to false to hide the leecher (DL) column
	 *     ShowCompleted [true]        -- Set to false to hide the completed (DONE) column
	 *     ShowXferred [true]          -- Set to false to hide the transferred (XFER) column
	 *     ShowAvg [true]              -- Set to false to hide the average done column
	 *     ShowSpd [true]              -- Set to false to hide the speed column
	 *     Advanced Sort [false]       -- Set to true to use torrent grouping/sorting
	 *
	 *   A real basic way to show it is by calling ds_DumpTorrentStats() with no
	 *   parameters, this will use all the defaults. To make it show a different
	 *   category (let's use 'mycategory' as an example) use:
	 *   ds_DumpTorrentStats(true, "mycategory");
	 *
	 *   This will show a similar page to DeHacked's PHPBTTracker:
	 *   ds_DumpTorrentStats(true, "main", "name", false, false, "", true, false, false, true, true, true, true, false, true)
	 * 
	 *   * A list of valid values to sort by:
	 *     - "info_hash"         --> The torrents info hash value (usually not displayed)
	 *     - "filename"          --> The torrent name column [default]
	 *     - "size"              --> The torrent size column
	 *     - "crc32"             --> Pointless, but by the Torrent's CRC32 value
	 *                               (You need to enter this value manually with the Add Torrent screen)
	 *     - "category"          --> category column, provided all categories are shown
	 *     - "completed"         --> the number of completed downloads (DONE column)
	 *     - "transferred"       --> the amount transferred (XFER column)
	 *     - "dateadded"         --> date added column
	 *     - "seeders"           --> seeders column
	 *     - "leechers"          --> leechers column
	 *     - "avgdone"           --> average progress column
	 *     - "speed"             --> speed column
	 *     - "datetoremoveurl"   --> sorts by when the torrent's url should be hidden
	 *     - "datetohidetorrent" --> sort by when the torrent is to be hidden
	 *     - "external_torrent"  --> sorts by whether or not it is an external torrent
	 *                               (usually displayed in the torrent name column)
	 *
	 * Customizing ds_DumpRetiredTorrentStats():
	 *    This works similarly to the above function.
	 *    The function has parameters IN THIS ORDER (defaults in square brackets []):
	 *     ConnectToDB [true]          -- Set to false if you make a connection to
	 *                                    the database prior to calling this function
	 *     Category ["main"]           -- Specify here the category to show
	 *     OrderBy ["filename"]**      -- Specify the ordering to be used
	 *     SortDescending ["false"]    -- Set to true to sort in descending order
	 *     ShowSummary ["true"]        -- Set to false to not display the summary line
	 *     ShowReturnLink ["true"]     -- Set to false to not show the go back a page link
	 *     ShowSize ["true"]           -- Set to false to hide the size column
	 *     ShowCRC ["true"]            -- Set to false to hide the CRC column
	 *     ShowCompleted ["true"]      -- Set to false to hide the completed column
	 *     ShowXferred ["true"]        -- Set to false to hide the xferred column
	 *     ShowAdded ["true"]          -- Set to false to hide the Date Added column
	 *     ShowRetired ["true"]        -- Set to false to hide the Date Retired column
	 *
	 *   A real basic way to show it is by calling ds_DumpRetiredTorrentStats() with no
	 *   parameters, this will use all the defaults. To make it show a different
	 *   category (let's use 'mycategory' as an example) use:
	 *   ds_DumpRetiredTorrentStats(true, "mycategory");
	 *
	 *   ** A list of valid values to sort by:
	 *     - "info_hash"         --> The torrents info hash value (usually not displayed)
	 *     - "filename"          --> The torrent name column [default]
	 *     - "size"              --> The torrent size column
	 *     - "crc32"             --> Pointless, but by the Torrent's CRC32 value
	 *     - "category"          --> category column, provided all categories are shown
	 *     - "completed"         --> the number of completed downloads (DONE column)
	 *     - "transferred"       --> the amount transferred (XFER column)
	 *     - "dateadded"         --> date added column
	 *     - "dateretired"       --> date retired column
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
	 *
	 * Location of config.php and version.php
	 */
	$GLOBALS["ds_scriptloc"] = "./";

	/*
	 * Dumps torrent stats - see comment header at the top of this file
	 */
	function ds_DumpTorrentStats($dsConnectToDB = true,
			$dsCategory = "main",
			$dsOrder = "filename",
			$dsSortDescending = false,
			$dsShowSummary = true,
			$dsShowRetiredLink = true,
			$dsRetiredPage = "retired.php",
			$dsColumnShowSize = true,
			$dsColumnShowCRC = true,
			$dsColumnShowAdded = true,
			$dsColumnShowSeeds = true,
			$dsColumnShowLeechers = true,
			$dsColumnShowCompleted = true,
			$dsColumnShowXferred = true,
			$dsColumnShowAvg = true,
			$dsColumnShowSpd = true,
			$dsAdvancedSort = false) {

		/*
		 * Needed for the version summary line and the database
		 */
		require_once($GLOBALS["ds_scriptloc"] . "version.php");
		require_once($GLOBALS["ds_scriptloc"] . "config.php");

		echo "<!-- Start of torrent stats -->\r\n<CENTER>\r\n<TABLE CLASS=\"trkOuter\">\r\n";
		echo "<TR>\r\n\t<TD ALIGN=CENTER COLSPAN=2>\r\n\t<TABLE CLASS=\"trkInner\">\r\n";

		/*
		 * Show column headers
		 */
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TH CLASS=\"trkHeading colName\">Name/Info Hash</TH>\r\n";
		if ($dsColumnShowSize) echo "\t\t\t<TH CLASS=\"trkHeading colSize\">Size</TH>\r\n";
		if ($dsColumnShowCRC) echo "\t\t\t<TH CLASS=\"trkHeading colCRC\">CRC32</TH>\r\n";
		if ($dsColumnShowAdded) echo "\t\t\t<TH CLASS=\"trkHeading colAdded\">Date<BR>Added</TH>\r\n"; 
		if ($dsColumnShowSeeds) echo "\t\t\t<TH CLASS=\"trkHeading colSeeds\">UL</TH>\r\n";
		if ($dsColumnShowLeechers) echo "\t\t\t<TH CLASS=\"trkHeading colLeechers\">DL</TH>\r\n";
		if ($dsColumnShowCompleted) echo "\t\t\t<TH CLASS=\"trkHeading colDone\">DONE</TH>\r\n";
		if ($dsColumnShowXferred && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t<TH CLASS=\"trkHeading colXfer\">XFER</TH>\r\n";
		if ($dsColumnShowAvg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t\t\t<TH CLASS=\"trkHeading colAvg\">Avg %<BR>Done</TH>\r\n";
		if ($dsColumnShowSpd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t<TH CLASS=\"trkHeading colSpd\">Speed</TH>\r\n";
		if ($dsCategory=="all") echo "\t\t\t<TH CLASS=\"trkHeading colCategory\">Category</TH>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Connect to database server if requested
		 */
		if ($dsConnectToDB) {
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
		}

		/*
		 * Category filtering
		 */
		if ($dsCategory=="all") {
			$dsWhereCategory = "";
		} else {
			if ($dsCategory=="") {
				$dsWhereCategory = "AND  namemap.category=\"main\" ";
			} else {
				$dsWhereCategory = "AND namemap.category=\"$dsCategory\" ";
			}
		}

		/*
		 * Ordering
		 */
		switch ($dsOrder) { 
			case "info_hash":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY info_hash DESC"; else $dsOrderByColumn = "ORDER BY info_hash ";
				break;
			case "filename":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY filename DESC"; else $dsOrderByColumn = "ORDER BY filename ";
				break;
			case "size":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY size DESC, filename "; else $dsOrderByColumn = "ORDER BY size, filename ";
				break;
			case "crc32":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY crc32 DESC"; else $dsOrderByColumn = "ORDER BY crc32 ";
				break;
			case "category":
				if ($dsCategory == "all")
					if ($dsSortDescending) $dsOrderByColumn = "ORDER BY category DESC, filename "; else $dsOrderByColumn = "ORDER BY category, filename ";
				else
					if ($dsSortDescending) $dsOrderByColumn = "ORDER BY filename DESC"; else $dsOrderByColumn = "ORDER BY filename ";
				break;
			case "completed":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY finished DESC, filename "; else $dsOrderByColumn = "ORDER BY finished, filename ";
				break;
			case "transferred":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY dlbytes DESC, filename "; else $dsOrderByColumn = "ORDER BY dlbytes, filename ";
				break;
			case "dateadded":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY dateadded DESC, filename "; else $dsOrderByColumn = "ORDER BY dateadded, filename ";
				break;
			case "seeders":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY seeds DESC, filename "; else $dsOrderByColumn = "ORDER BY seeds, filename ";
				break;
			case "leechers":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY leechers DESC, filename "; else $dsOrderByColumn = "ORDER BY leechers, filename ";
				break;
			case "avgdone":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY avgdone DESC, filename "; else $dsOrderByColumn = "ORDER BY avgdone, filename ";
				break;
			case "speed":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY speed DESC, filename "; else $dsOrderByColumn = "ORDER BY speed, filename ";
				break;
			case "datetoremoveurl":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY DateToRemoveURL DESC, filename "; else $dsOrderByColumn = "ORDER BY DateToRemoveURL, filename ";
				break;
			case "datetohidetorrent":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY DateToHideTorrent DESC, filename "; else $dsOrderByColumn = "ORDER BY DateToHideTorrent, filename ";
				break;
			case "external_torrent":
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY external_torrent DESC, filename "; else $dsOrderByColumn = "ORDER BY external_torrent DESC, filename ";
				break;
			default:
				if ($dsSortDescending) $dsOrderByColumn = "ORDER BY filename DESC"; else $dsOrderByColumn = "ORDER BY filename ";
		}

		/*
		 * Query the database for the torrents
		 */
		if ($dsAdvancedSort && $dsCategory != "all" && !$GLOBALS["dynamic_torrents"]) {
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
					LEFT OUTER JOIN subgrouping ON subgrouping.group_id = namemap.grouping 	
					WHERE summary.hide_torrent=\"N\" $dsWhereCategory AND (namemap.DateToHideTorrent > CURDATE() OR namemap.DateToHideTorrent = \"0000-00-00\")
					ORDER BY subgrouping.groupsort, namemap.sorting";
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
					LEFT JOIN namemap ON summary.info_hash = namemap.info_hash 				
					WHERE summary.hide_torrent=\"N\" $dsWhereCategory AND (namemap.DateToHideTorrent > CURDATE() OR namemap.DateToHideTorrent = \"0000-00-00\")
					$dsOrderByColumn";
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

		$totalglobaltorrents = 0;

		/*
		 * Figure out how many columns we need to account for when spanning them 
		 */
		$colspan = 0;
		if ($dsColumnShowSeeds) $colspan++;
		if ($dsColumnShowLeechers) $colspan++;
		if ($dsColumnShowCompleted) $colspan++;
		if ($dsColumnShowXferred) $colspan++;
		if ($dsColumnShowAvg) $colspan++;
		if ($dsColumnShowSpd) $colspan++;

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

			if ($dsAdvancedSort && $dsCategory != "all" && $advancedsortcounter != $row[25] && !$GLOBALS["dynamic_torrents"]) {
				echo "\t\t\t<TR>\r\n\t\t\t\t<TD COLSPAN=15 CLASS=\"$rowBackground trkData grpHeading\"><B>$row[25]</B></TD>\r\n\t\t\t</TR>\r\n";
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
				echo " (${row[4]})";
	
			/*
			 * Show alternate http link, if any
			 */
			if (strlen($row[3]) > 0)
				echo "<DIV CLASS=\"torrentTag\">&nbsp[<A HREF=\"${row[3]}\">Alternate Link</A>]</DIV>";

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
			if ($dsColumnShowSize) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSize\">$fsize</TD>\r\n";

			/*
			 * Decision point: show comment??
			 */
			if ($row[22] == 'Y') {
				/*
				 * Show the comment.
				 */
					echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colComment\" COLSPAN=10>$row[23]</TD>";			
			} else {
				/*
				 * Output like normal
				 */

				/*
				 * CRC column
				 */
				if ($dsColumnShowCRC) {
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
				if ($dsColumnShowAdded) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colAdded\">$row[7]</TD>\r\n";

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
							if ($dsColumnShowSeeds) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSeeds\">$row[8]</TD>\r\n";

							/*
							 * Leechers column
							 */
							if ($row[9] == 0) $row[9] = '?';
							if ($dsColumnShowLeechers) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colLeechers\">$row[9]</TD>\r\n";

							/*
							 * Number of downloads column
							 */
							if ($row[10] == 0) $row[10] = '?';
							if ($dsColumnShowCompleted) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colDone\">$row[10]</TD>\r\n";
						} else {
							/*
							 * Seeds column
							 */
							if ($dsColumnShowSeeds) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSeeds\">$row[8]</TD>\r\n";

							/*
							 * Leechers column
							 */
							if ($dsColumnShowLeechers) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colLeechers\">$row[9]</TD>\r\n";

							/*
							 * Number of downloads column
							 */
							if ($dsColumnShowCompleted) {
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
						if ($dsColumnShowXferred && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colXfer\">$xferred</TD>\r\n";
	
						/*
						 * Average percentage done column
						 */
						$avgdone = round($row[12],1) . "%";
						if ($row[12] == 0) $avgdone = '?';
						if ($dsColumnShowAvg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colAvg\">$avgdone</TD>\r\n";

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
						if ($dsColumnShowSpd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSpd\">$speed</TD>\r\n";
					} else {
						/*
						 * Seeds column
						 */
						if ($dsColumnShowSeeds) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSeeds\">$row[8]</TD>\r\n";

						/*
						 * Leechers column
						 */
						if ($dsColumnShowLeechers) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colLeechers\">$row[9]</TD>\r\n";

						/*
						 * Number of downloads column
						 */
						if ($dsColumnShowCompleted) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colDone\">$row[10]</TD>\r\n";
	
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
						if ($dsColumnShowXferred && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colXfer\">$xferred</TD>\r\n";

						/*
						 * Average percentage done column
						 */
						if ($dsColumnShowAvg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colAvg\">". round($row[12],1) . "%</TD>\r\n";
	
						/*
						 * Show megabytes/sec, kilobytes/sec, or Stalled depending on torrent speed
						 */
						if ($row[13] <= 0)
							$speed = "Stalled";
						else if ($row[13] > 2097152)
							$speed = round($row[13]/1048576,2) . "<BR>MiB/sec";
						else
							$speed = round($row[13] / 1024, 2) . "<BR>KiB/sec";
	
						if ($dsColumnShowSpd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colSpd\">$speed</TD>\r\n";
					}
				}
			}

			/*
			 * Category column
			 */
			if ($dsCategory=="all") echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colCategory\">$row[14]</TD>\r\n";
		
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
			if ($dsShowSummary) {
				$rowBackground = $clrRowBG[$totalglobaltorrents % 2];

				if ($totalexttorrents > 0)
					echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colName\"><B>Currently tracking $totaltorrents torrent(s)</B>. [Plus $totalexttorrents external torrents.]</TD>\r\n";
				else
					echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colName\"><B>Currently tracking $totaltorrents torrent(s)</B>.</TD>\r\n";

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

				if ($dsColumnShowSize) {
					if ($totalexttorrents > 0)
						echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colSize\">$totalsize<BR>[$totalextsize]</TD>\r\n";
					else
						echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colSize\">$totalsize</TD>\r\n";
				}

				if ($dsColumnShowCRC) echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">---</TD>\r\n";
				if ($dsColumnShowAdded) echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">---</TD>\r\n";

				if ($dsColumnShowSeeds) {
					if ($totalexttorrents > 0)
						echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colSeeds\">$totalseeders<BR>[$totalextseeders]</TD>\r\n";
					else
						echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colSeeds\">$totalseeders</TD>\r\n";
				}

				if ($dsColumnShowLeechers) {
					if ($totalexttorrents > 0)
						echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colLeechers\">$totalleechers<BR>[$totalextleechers]</TD>\r\n";	
					else
						echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colLeechers\">$totalleechers</TD>\r\n";	
				}

				if ($dsColumnShowCompleted) {
					if ($totalexttorrents > 0)
						echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colDone\">$totalcomplete<BR>[$totalextcomplete]</TD>\r\n";
					else
						echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colDone\">$totalcomplete</TD>\r\n";
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


				if ($dsColumnShowXferred && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) {
					if ($totalexttorrents > 0)
						echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colXfer\">$totalxferred<BR>[$totalextxferred]</TD>\r\n";
					else
						echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colXfer\">$totalxferred</TD>\r\n";
				}

				if ($dsColumnShowAvg && !$GLOBALS["heavyload"] && $GLOBALS["doavg"]) echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colSummary\">---</TD>\r\n";

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

				if ($dsColumnShowSpd && !$GLOBALS["heavyload"] && $GLOBALS["countbytes"]) {
					if ($totalexttorrents > 0)
						echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colSpd\">$totalspeed<BR>[$totalextspeed]</TD>\r\n";
					else
						echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colSpd\">$totalspeed</TD>\r\n";
				}

				if ($dsCategory=="all")
					echo "\t\t\t\t\t<TD CLASS=\"$rowBackground trkData colCategory\">---</TD>\r\n";

				echo "\t\t\t\t</TR>\r\n";
			}

		}
		echo "\t\t</TABLE>\r\n\t\t</TD>\r\n\t</TR>\r\n";

		echo "\t<TR>\r\n\t\t<TD CLASS=\"versionLine\">$phpbttracker_id $phpbttracker_ver using MySQL.</TD>\r\n";
		echo "\t\t<TD CLASS=\"scaleLine\">Ki = 1024, Mi = 1024 Ki, Gi = 1024 Mi, Ti = 1024 Gi</TD>\r\n\t</TR>\r\n";

		if ($dsShowRetiredLink)
			echo "\t<TR>\r\n\t\t<TD CLASS=\"retiredLink\" COLSPAN=2><A HREF=\"$dsRetiredPage\">Click to see retired/inactive torrents.</A></TD>\r\n\t</TR>\r\n";


		echo "</TABLE>\r\n</CENTER>\r\n<!-- End of torrent stats -->\r\n";

	}

	/*
	 * Dumps retired torrent stats - see comment header at the top of this file
	 */
	function ds_DumpRetiredTorrentStats($dsConnectToDB = true,
			$dsCategory = "main",
			$dsOrder = "filename",
			$dsSortDescending = false,
			$dsShowSummary = true,
			$dsShowReturnLink = true,
			$dsColumnShowSize = true,
			$dsColumnShowCRC = true,
			$dsColumnShowCompleted = true,
			$dsColumnShowXferred = true,
			$dsColumnShowAdded = true,
			$dsColumnShowRetired = true) {

		/*
		 * Needed for the version summary line and the database
		 */
		require_once($GLOBALS["ds_scriptloc"] . "version.php");
		require_once($GLOBALS["ds_scriptloc"] . "config.php");

		echo "<!-- Start of retired torrent stats -->\r\n<CENTER>\r\n<TABLE CLASS=\"trkOuter\">\r\n";

		/*
		 * Show return to main link if needed
		 */
		if ($dsShowReturnLink)
			echo "\t<TR><TD CLASS=\"retiredLink\" COLSPAN=2><A HREF=\"javascript:history.back()\">Return to torrent statistic page</A><BR><BR></TD></TR>\r\n";

		echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2>\r\n\t\t<TABLE CLASS=\"trkInner\">\r\n";

		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TH CLASS=\"trkHeading trkData colName\">Name/Info Hash</TH>\r\n";
		if ($dsColumnShowSize) echo "\t\t\t<TH CLASS=\"trkHeading trkData colSize\">Size</TH>\r\n";
		if ($dsColumnShowCRC) echo "\t\t\t<TH CLASS=\"trkHeading trkData colCRC\">CRC32</TH>\r\n";
		if ($dsColumnShowCompleted) echo "\t\t\t<TH CLASS=\"trkHeading trkData colDone\">Done</TH>\r\n";
		if ($dsColumnShowXferred) echo "\t\t\t<TH CLASS=\"trkHeading trkData colXfer\">XFER</TH>\r\n";
		if ($dsColumnShowAdded) echo "\t\t\t<TH CLASS=\"trkHeading trkData colAdded\">Date<BR>Added</TH>\r\n";
		if ($dsColumnShowRetired) echo "\t\t\t<TH CLASS=\"trkHeading trkData colAdded\">Date<BR>Retired</TH>\r\n";
		if ($dsCategory=="all") echo "\t\t\t<TH CLASS=\"trkHeading trkData colCategory\">Category</TH>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Connect to database server if requested
		 */
		if ($dsConnectToDB) {
			/*
			 * Make the connection
			 */
			if ($GLOBALS["persist"])
				$db = mysql_pconnect($dbhost, $dbuser, $dbpass) or die("Error: can't connect to database - " . mysql_error());
			else
				$db = mysql_connect($dbhost, $dbuser, $dbpass) or die("Error: can't connect to database - " . mysql_error());

			/*
			 * Open the database
			 */
			mysql_select_db($database) or die("Error: can't open the database - " . mysql_error());
		}

		/*
		 * Category filtering
		 */
		if ($dsCategory=="all") {
			$dsWhereCategory = "";
		} else {
			if ($dsCategory=="") {
				$dsWhereCategory = "WHERE retired.category=\"main\" ";
			} else {
				$dsWhereCategory = "WHERE retired.category=\"$dsCategory\" ";
			}
		}

		/*
		 * Ordering
		 */
		switch ($dsOrder) {
			case "info_hash":
				$dsOrderByColumn = "ORDER BY info_hash ";
				break;
			case "filename":
				$dsOrderByColumn = "ORDER BY filename ";
				break;
			case "size":
				$dsOrderByColumn = "ORDER BY size ";
				break;
			case "crc32":
				$dsOrderByColumn = "ORDER BY crc32 ";
				break;
			case "category":
				if ($dsCategory == "all")
					$dsOrderByColumn = "ORDER BY category ";
				else
					$dsOrderByColumn = "ORDER BY filename ";
				break;
			case "completed":
				$dsOrderByColumn = "ORDER BY completed ";
				break;
			case "transferred":
				$dsOrderByColumn = "ORDER BY transferred ";
				break;
			case "dateadded":
				$dsOrderByColumn = "ORDER BY dateadded ";
				break;
			case "dateretired":
				$dsOrderByColumn = "ORDER BY dateretired ";
				break;
			default:
				$dsOrderByColumn = "ORDER BY filename ";
		}

		if ($dsSortDescending) {
			$dsOrderByColumn .= "DESC ";
		}

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
			 dateretired FROM retired $dsWhereCategory $dsOrderByColumn";

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

			if ($dsColumnShowSize) echo "\t\t\t<TD CLASS=\"$writeout trkData colSize\">$fsize</TD>\r\n";
			if ($dsColumnShowCRC) echo "\t\t\t<TD CLASS=\"$writeout trkdata colCRC\">$row[3]</TD>\r\n";	
			if ($dsColumnShowCompleted) echo "\t\t\t<TD CLASS=\"$writeout trkData colDone\">$row[5]</TD>\r\n";

			/*
			 * Show terabytes if necessary
			 */
			if ($row[6] > 1099511627776)
				$xferred = round($row[6]/1099511627776,2) . "<BR>TiB";
			else
				$xferred = round($row[6]/1073741824,1) . "<BR>GiB";

			if ($dsColumnShowXferred) echo "\t\t\t<TD CLASS=\"$writeout trkData colXfer\">$xferred</TD>\r\n";		
			if ($dsColumnShowAdded) echo "\t\t\t<TD CLASS=\"$writeout trkData colAdded\">$row[7]</TD>\r\n";
			if ($dsColumnShowRetired) echo "\t\t\t<TD CLASS=\"$writeout trkData colAdded\">$row[8]</TD>\r\n";

			if ($dsCategory=="all") echo "\t\t\t<TD CLASS=\"$writeout trkData colCategory\">$row[4]</TD>\r\n";

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
			echo "\t\t<TR>\r\n\t\t\t<TD CLASS=\"$clrRowBG[0] trkData colSummary COLSPAN=9>No retired torrents to display</TD>\r\n\t\t</TR>\r\n";
		else {
			/*
			 * If a summary line is supposed to be displayed, show it
			 */
			if ($dsShowSummary) {
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

				if ($dsColumnShowSize) echo "\t\t\t<TD CLASS=\"$writeout trkData colSize\">$totalsize</TD>\r\n";
				if ($dsColumnShowCRC) echo "\t\t\t<TD CLASS=\"$writeout trkData colSummary\">---</TD>\r\n";
				if ($dsColumnShowCompleted) echo "\t\t\t<TD CLASS=\"$writeout trkData colDone\">$totalcomplete</TD>\r\n";
				if ($dsColumnShowXferred) echo "\t\t\t<TD CLASS=\"$writeout trkData colXfer\">$totalxferred</TD>\r\n";
				if ($dsColumnShowAdded) echo "\t\t\t<TD CLASS=\"$writeout trkData colSummary\">---</TD>\r\n";
				if ($dsColumnShowRetired) echo "\t\t\t<TD CLASS=\"$writeout trkData colSummary\">---</TD>\r\n";
				if ($dsCategory=="all") echo "\t\t\t<TD CLASS=\"$writeout trkData colSummary\">---</TD>\r\n";
			}
		}

		echo "\t\t</TABLE>\r\n\t\t</TD>\r\n\t</TR>\r\n";

		echo "\t<TR>\r\n\t\t<TD CLASS=\"versionLine\">$phpbttracker_id $phpbttracker_ver using MySQL.</TD>\r\n";
		echo "\t\t<TD CLASS=\"scaleLine\">Ki = 1024, Mi = 1024 Ki, Gi = 1024 Mi, Ti = 1024 Gi</TD>\r\n\t</TR>\r\n";

		if ($dsShowReturnLink)
			echo "\t<TR><TD CLASS=\"retiredLink\" COLSPAN=2><A HREF=\"javascript:history.back()\">Return to torrent statistic page</A><BR><BR></TD></TR>\r\n";

		echo "\t</TABLE>\r\n\t</CENTER>\r\n";
	}
?>
