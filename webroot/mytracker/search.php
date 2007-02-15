<?php

	/*
	 * search.php - an example on how to search for torrents in the tracker
	 *
	 * Copyright (C) 2004 danomac
	 * 
	 * This is a self contained file, but you can create a search field called
	 * 'searchterms' and set it to POST to this file, and it will show results.
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
	 * Check to see if a form was posted
	 */
	if (isset($_POST["searchterms"])) {
		/*
		 * Yep, let's connect to the database, and do a very basic search using
		 * the LIKE clause in mysql
		 */
		require_once("config.php");
		require_once("funcsv2.php");
		
		/*
		 * Connect to the database server
		 */
		if ($GLOBALS["persist"])
			$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or showError("Tracker error: can't connect to database. Contact the webmaster.");
		else
			$db = @mysql_connect($dbhost, $dbuser, $dbpass) or showError("Tracker error: can't connect to database. Contact the webmaster.");
	
		/*
		 * Open required database
		 */
		@mysql_select_db($database) or showError("Tracker error: can't open database. Contact the webmaster");

		/*
		 * Query the database, preparing the search string first...
		 */
		$search = mysql_real_escape_string($_POST["searchterms"]);
		$rstSearch = mysql_query("SELECT `info_hash`, `filename`, `url`, `mirrorurl`, `info`, `size`, `tsAdded`, `category`, `crc32` FROM `namemap` WHERE `filename` LIKE '%$search%' ORDER BY `tsAdded` DESC") or die("Unable to perform query: " . mysql_error());


		echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n<HTML>\r\n<HEAD>\r\n";
		echo "\t<META NAME=\"Author\" CONTENT=\"danomac\">\r\n";
		echo "\t<LINK REL=\"stylesheet\" HREF=\"tracker.css\" TYPE=\"text/css\" TITLE=\"Default\">\r\n";
		echo "\t<TITLE>Torrent search results</TITLE>\r\n</HEAD>\r\n\r\n<BODY>\r\n";
		echo "<CENTER>\r\n<TABLE CLASS=\"trkOuter\">\r\n\t<TR>\r\n\t\t<TD CLASS=\"mainCells\" COLSPAN=2><H1>Search results</H1><BR>&nbsp;</TD>\r\n\t</TR>\r\n";
		echo "\t<TR>\r\n\t\t<TD CLASS=\"mainCells\" COLSPAN=2>\r\n\t\t\t<TABLE CLASS=\"trkInner\">\r\n";

		/*
		 * Show column headers, and if sorting is allowed, show hyperlinks to sort the columns
		 */
		echo "\t\t\t<TR>\r\n";
		echo "\t\t\t\t<TH CLASS=\"trkHeading colName\">Name/Info Hash</TH>\r\n";
		echo "\t\t\t\t<TH CLASS=\"trkHeading colSize\">Size</TH>\r\n";
		echo "\t\t\t\t<TH CLASS=\"trkHeading colCRC\">CRC32</TH>\r\n";
		echo "\t\t\t\t<TH CLASS=\"trkHeading colAdded\">Date<BR>Added</TH>\r\n"; 
		echo "\t\t\t\t<TH CLASS=\"trkHeading colCategory\">Category</TH>\r\n";
		echo "\t\t\t</TR>\r\n";

		/*
		 * Classes used to alternate background colour in rows
		 */
		$clrRowBG[0] = 'trkOdd';
		$clrRowBG[1] = 'trkEven';

		$totalresults = 0;
		/*
		 * Output the search results
		 */
		while ($row=mysql_fetch_row($rstSearch)) 	{
			$rowBackground = $clrRowBG[$totalresults % 2];

			echo "\t\t\t<TR>\r\n";
			if (strlen($row[2]) > 0) {
				echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colName\"><A HREF=\"$row[2]\">$row[1]</A></TD>\r\n";
			} else {
				echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colName\">$row[1]</TD>\r\n";
			}

			if ($row[5] > 1000) {
				echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colName\">". round($row[5]/1024,2) ." GB</TD>\r\n";
			} else {
				echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colName\">$row[5] MB</TD>\r\n";
			}
			echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colName\">$row[8]</TD>\r\n";
			
			echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colName\">". date("Y-m-d", $row[6]) ."</TD>\r\n";

			echo "\t\t\t\t<TD CLASS=\"$rowBackground trkData colName\">$row[7]</TD>\r\n";

			echo "\t\t\t</TR>\r\n";
			$totalresults++;
		}
//		echo $_POST["searchterms"];	
		echo "\t\t\t</TABLE>\r\n\t\t</TD>\r\n\t</TR>\r\n</TABLE>\r\n";
		echo "</BODY>\r\n</HTML>\r\n";
		exit;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<LINK REL="stylesheet" HREF="tracker.css" TYPE="text/css" TITLE="Default">
	<TITLE>Torrent search</TITLE>
</HEAD>

<BODY>
<CENTER>
<H1>Torrent search</H1>
<FORM ACTION="search.php" METHOD="POST">
	Search for:&nbsp;<INPUT TYPE="text" NAME="searchterms">&nbsp;&nbsp;<INPUT TYPE="submit" VALUE="Search...">
</FORM>
</CENTER>
</BODY>
</HTML>
