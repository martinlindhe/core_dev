<?php
	/*
	 * Module:	scrape_scan.php
	 * Description: This file connects to a provided /scrape url, decodes
	 * 		its output and displays it in a table.
	 *
	 *			PHP CONFIGURATION NOTE: This needs 'allow_url_fopen' to be TRUE in
	 *					php.ini for this module to work!
	 *
	 * Author:	danomac
	 * Written:	4-Sept-2004
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
	require_once ("BDecode.php");
	require_once ("version.php");
	require_once ("config.php");


	if (isset($_POST["decode"])) {
		/*
		 * First, if someone decided to paste the /announce url, replace it with /scrape.
		 */
		if (strcmp(substr($_POST["scrapeurl"], strlen($_POST["scrapeurl"]) - 9, 9), "/announce") == 0)
			$_POST["scrapeurl"] = substr($_POST["scrapeurl"], 0, strlen($_POST["scrapeurl"]) - 9) . "/scrape";

		/*
		 * Try to get the contents from the requested tracker
		 */
		$contents = @file_get_contents($_POST["scrapeurl"]);

		/*
		 * If something was retrieved, attempt to decode it
		 */
		if ($contents != false)
			$details = BDecode($contents);
		else
			/*
			 * Nothing retrived, set to an empty string, so script falls through with an error
			 */
			$details = "";

		/*
		 * HTML output & headers
		 */
		echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n";
		echo "<HTML>\r\n<HEAD>\r\n";
		echo "\t<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=us-ascii\">\r\n";
		echo "\t<META NAME=\"Author\" CONTENT=\"danomac\">\r\n";
		echo "\t<TITLE>$phpbttracker_id $phpbttracker_ver - Scrape output analyzer results</TITLE>\r\n";
		echo "</HEAD>\r\n<BODY>\r\n";
		echo "\t<CENTER><H1>$phpbttracker_id $phpbttracker_ver - Scrape output analyzer results</H1>";
		echo "\t<TABLE BORDER=0 BGCOLOR=\"#000000\">\r\n";
		echo "\t\t<TR>\r\n\t\t\t<TD BGCOLOR=\"#999999\" VALIGN=\"bottom\"><B>Info_hash</B></TD>\r\n";
		echo "\t\t\t<TD BGCOLOR=\"#999999\" VALIGN=\"bottom\"><B>Name</B></TD>\r\n";
		echo "\t\t\t<TD BGCOLOR=\"#999999\" ALIGN=\"center\" VALIGN=\"bottom\"><B>Seeders</B></TD>\r\n";
		echo "\t\t\t<TD BGCOLOR=\"#999999\" ALIGN=\"center\" VALIGN=\"bottom\"><B>Leechers</B></TD>\r\n";
		echo "\t\t\t<TD BGCOLOR=\"#999999\" ALIGN=\"center\" VALIGN=\"bottom\"><B>Complete</B></TD>\r\n";
		echo "\t\t\t<TD BGCOLOR=\"#999999\" ALIGN=\"center\" VALIGN=\"bottom\"><B>Transferred<SUP>*</SUP></B></TD>\r\n";
		echo "\t\t\t<TD BGCOLOR=\"#999999\" ALIGN=\"center\" VALIGN=\"bottom\"><B>Average Done<SUP>*</SUP></B></TD>\r\n";
		echo "\t\t\t<TD BGCOLOR=\"#999999\" ALIGN=\"center\" VALIGN=\"bottom\"><B>Speed<SUP>*</SUP></B></TD>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Check to see if this is a valid tracker response
		 */
		if (isset($details["files"]))	{
			/*
			 * OK, it's a valid response, let's spit out data.
			 */
			$processed = 0;

			$clrRowBG[0] = 'BGCOLOR="#FFFFFF"';
			$clrRowBG[1] =  'BGCOLOR="#C0C0C0"';

			/*
			 * Traverse through the array
			 */
			foreach ($details["files"] as $key => $value) {
				echo "\t\t<TR>\r\n";

				$info_hash = bin2hex(stripslashes($key));
		
				$cellBG = $clrRowBG[$processed % 2];

				echo "\t\t\t<TD $cellBG>" . $info_hash . "</TD>\r\n";	

				/*
				 * Torrent name
				 */
				if (isset($details["files"][$key]["name"]))
					echo "\t\t\t<TD $cellBG>" . $details["files"][$key]["name"] . "</TD>\r\n";
				else
					echo "\t\t\t<TD $cellBG>&nbsp;</TD>\r\n";

				/*
				 * Seeders
				 */
				if (isset($details["files"][$key]["complete"]))
					echo "\t\t\t<TD $cellBG ALIGN=\"center\">" . $details["files"][$key]["complete"] . "</TD>\r\n";
				else
					echo "\t\t\t<TD $cellBG ALIGN=\"center\">&nbsp;</TD>\r\n";

				/*
				 * Leechers
				 */
				if (isset($details["files"][$key]["incomplete"]))
					echo "\t\t\t<TD $cellBG ALIGN=\"center\">" . $details["files"][$key]["incomplete"] . "</TD>\r\n";
				else
					echo "\t\t\t<TD $cellBG ALIGN=\"center\">&nbsp;</TD>\r\n";

				/*
				 * Total number of complete downloads
				 */
				if (isset($details["files"][$key]["downloaded"]))
					echo "\t\t\t<TD $cellBG ALIGN=\"center\">" . $details["files"][$key]["downloaded"] . "</TD>\r\n";
				else
					echo "\t<TD $cellBG ALIGN=\"center\">&nbsp;</TD>\r\n";

				/*
				 * EXTENDED: Amount transferred
				 */
				if (isset($details["files"][$key]["transferred"])) {
					$transferred = round($details["files"][$key]["transferred"], 2);
					if (isset($details["files"][$key]["transferredunit"]))
						$transferred .= " " . $details["files"][$key]["transferredunit"];
					echo "\t\t\t<TD $cellBG ALIGN=\"center\">$transferred</TD>\r\n";
				} else
					echo "\t<TD $cellBG ALIGN=\"center\">&nbsp;</TD>\r\n";

				/*
				 * EXTENDED: Average % done
				 */
				if (isset($details["files"][$key]["averagedone"]))
					echo "\t\t\t<TD $cellBG ALIGN=\"center\">" . round($details["files"][$key]["averagedone"],1) . "%</TD>\r\n";
				else
					echo "\t<TD $cellBG ALIGN=\"center\">&nbsp;</TD>\r\n";

				/*
				 * EXTENDED: Torrent speed
				 */
				if (isset($details["files"][$key]["speed"])) {
					$speed = round($details["files"][$key]["speed"], 2);
					if (isset($details["files"][$key]["speedunit"]))
						$speed .= " " . $details["files"][$key]["speedunit"] . "/sec";
					echo "\t\t\t<TD $cellBG ALIGN=\"center\">$speed</TD>\r\n";
				} else
					echo "\t<TD $cellBG ALIGN=\"center\">&nbsp;</TD>\r\n";
				
				echo "\t\t</TR>\r\n";

				$processed++;
			}
		} else
			/*
			 * Not a valid response, show an error)
			 */
			echo "\t<TD ALIGN=\"center\" BGCOLOR=\"white\" COLSPAN=8><B>Invalid response from tracker OR scrape URL is incorrect (".$_POST["scrapeurl"].")</B></TD>\r\n";	

		/*
		 * Close HTML tags.
		 */
		echo "\t</TABLE>\r\n\t</CENTER>";

		if (isset($processed)) {
			echo "\r\n\t<BR>$processed entries displayed.<BR>";
		}

		echo "<SUP>*</SUP>Extended data (PHPBTTracker+ trackers only, if enabled)<BR>\r\n\tIf data is blank, it isn't being reported by the tracker.</BODY>\r\n</HTML>";

		/*
		 * Terminate the script here.
		 */
		exit;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=us-ascii">
	<META NAME="Author" CONTENT="danomac">
	<TITLE><?php echo $phpbttracker_id . " " . $phpbttracker_ver; ?> - Scrape output analyzer</TITLE>
</HEAD>	
<BODY>
<?php
	/*
	 * Is this script disabled?
	 */
	if (!$GLOBALS["scrape_scanning"]) {
		echo "\t<CENTER><H1>$phpbttracker_id $phpbttracker_ver - Scrape output analyzer</H1>This script has been disabled by the administrator.</CENTER>\r\n</BODY>\r\n</HTML>";
		exit;
	}

	/*
	 * There is no point in showing this script if allow_url_fopen is not set in php.ini...
	 */
	if (ini_get("allow_url_fopen") == true) {
?>
	<FORM ACTION="scrape_scan.php" METHOD="post">
		<CENTER><H1><?php echo $phpbttracker_id . " " . $phpbttracker_ver; ?> Scrape output analyzer</H1>This is a tool that can be used to decode 
		and display the output of a tracker's /scrape. <B>DO NOT hammer trackers repeatedly with this!</B><BR><BR>
		Enter the scrape address here (http address): <INPUT TYPE="text" NAME="scrapeurl" SIZE=40><BR>
		<INPUT TYPE="submit" NAME="decode" VALUE="Decode scrape output"></CENTER>
	</FORM>
<?php
	} else {
		echo "<CENTER><H1>$phpbttracker_id $phpbttracker_ver - Scrape output analyzer</H1>This script requires the configuration directive <B>allow_url_fopen</B> to be set in php.ini. Change this directive, restart the web server daemon and try running the script again. If you do not have control over php.ini you will not be able to use this script.";
	}
?>
</BODY>
</HTML>