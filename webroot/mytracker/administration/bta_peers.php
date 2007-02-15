<?php
	/*
	 * Module:	bta_seedsum.php
	 * Description: This displays seeder info for a torrent.
	 * 		It does not require users to be logged in to the
	 * 		administration interface.
	 *
	 * Author:	danomac
	 * Written:	31-March-2004
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
	 * set this to the order you want, when none is specified
	 * be sure to leave a space before and after the order
	 * VALID VALUES: " ip ", " clientversion ", " uploaded "
	 */
	$defaultsortorder = " ip ";

	/*
	 * required modules
	 */
	require_once ("../funcsv2.php");
	require_once ("../version.php");
	require_once ("bta_funcs.php");

	/*
	 * Get the name of the current script, for building URLs
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
	if (!($_SESSION["admin_perms"]["peers"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Redirecting to the main administration panel.",
			       $adm_page_title, true, "bta_main.php", 3);
	}

	/*
	 * Get the current time to calculate when in the past
	 * the client updated last.
	 */
	$currentTime = time();

	/*
	 * build ORDER BY clause based on parameter input.
	 * if no order is requested, use the default defined above
	 */
	if (isset($_GET["sort"])) {
		$requestedsort = $_GET["sort"];

		if (strpos($_GET["sort"], " ") !== false) {
			admShowMsg("Invalid sort parameter", "An invalid sort value was passed.", "Invalid sort request", true, "bta_main.php", 5);
		}

		switch ($requestedsort) {
			case "ip":
				$order = " ip ";
				break;
			case "version":
				$order = " clientversion ";
				break;
			case "uploading":
				$order = " uploaded ";
				break;
			case "remaining":
				$order = " bytes ";
				break;
			default:
				die("Invalid sort order specified");
		}
	} else
		/*
		 * No order specified, use the default.
		 */
		$order = $defaultsortorder;

	/*
	 * Let's get the info hash of the torrent to show peer details for. If no hash specified, return
	 * to the main admin screen.
	 */
	if (!isset($_SESSION["info_hash"])) {
		admShowMsg("No info hash specified.", "No hash specified (internal error).", "Error", true, "bta_main.php", 5);
		exit;
	} else {
		/*
		 * retrieve the parameter from the stack and attempt to pull information from mysql.
		 */
		$hash = $_SESSION["info_hash"];

		if (strpos($_SESSION["info_hash"], " ") !== false) {
			admShowMsg("Invalid hash value", "An invalid hash value was passed.", "Invalid hash value", true, "bta_main.php", 5);
		}

		/*
		 * Connect to the database
		 */
		if ($GLOBALS["persist"])
			$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		else
			$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		mysql_select_db($database) or die("Can't open the database.");

		/*
		 * Get information about the torrent itself to display (name, and file size)
		 */
		$recordset = mysql_query("SELECT info_hash, 
					filename, 
					size FROM namemap WHERE info_hash=\"$hash\"") or showError("Database error. Cannot complete request.");

		/*
		 * If nothing returned, it's an error... so stop processing
		 */
		if (mysql_num_rows($recordset) == 0) {
			admShowMsg("No hash value", "Either no hash value was passed, or an invalid value was passed.", "Invalid hash value");
			exit;
		}
		$row = mysql_fetch_row($recordset);

		/*
		 * Save needed information to variables
		 */
		$filename = $row[1];
		$filesize = $row[2];
	
		/*
		 * Get the seeder stats
		 */
		$recordset = mysql_query("SELECT peer_id, 
						ip, 
						port, 
						uploaded,
						clientversion,
						bytes,
						lastupdate,
						status FROM x$hash ORDER BY $order") or showError("Database error. Cannot complete request.");
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
	echo "<TITLE>". $adm_page_title . " - Peer summary</TITLE>\r\n";
?>
</HEAD>

<BODY>
<FORM ACTION="bta_peer_confirm.php" METHOD=POST>

<TABLE CLASS="tblAdminOuter">
<TR>
<?php
	/*
	 * Display the page heading.
	 */
	echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>Peer summary</TD>\r\n";
?>
</TR>
</TR>
<?php admShowURL_Login($ip); ?>
<TR>
<TR>
	<TD CLASS="data" COLSPAN=15 ALIGN="center"><BR>
	   <A HREF="help/help_peer_summary.php" TARGET="_blank">Need help?</A><BR>
	</TD>
</TR>
<TR>
	<TD ALIGN="center" COLSPAN=15>
<?php
	/*
	 * display information on the requested hash
	 */
	echo "\t<BR><STRONG><U>Torrent information</U></STRONG><BR>\r\n";
	echo "\t<TABLE BORDER=0>\r\n";
	echo "\t<TR><TD>Info_hash:</TD><TD>" . $hash . "</TD></TR>\r\n";
	echo "\t<TR><TD><B>File name:</B></TD><TD><B>" . $filename . "</B></TD></TR>\r\n";
	echo "\t<TR><TD>File size:</TD><TD>" . $filesize . " MiB</TD></TR>\r\n</TABLE>\r\n";
?>
	</TD>
</TR>
<TR>
	<TD ALIGN="center" COLSPAN=15><A HREF="bta_main.php">Return to main administration panel</A></TD>
</TR>
<TR>
	<TD ALIGN="center" COLSPAN=15><BR>
	The peer information is listed in the table below.<BR>
	<TABLE CLASS="tblAdminOuter">
	<TR>
		<TD COLSPAN=2>
			<TABLE CLASS="tblAdminInner">
<?php
	/*
	 * build the hyperlinks required for sorting
	 */
	$sortipurl = $scriptname . "?info_hash=" . $hash . "&sort=ip";
	$sortversionurl = $scriptname . "?info_hash=" . $hash . "&sort=version";
	$sortupurl = $scriptname . "?info_hash=" . $hash . "&sort=uploading";
	$sortdownurl = $scriptname . "?info_hash=" . $hash . "&sort=remaining";

	/*
	 * Show the column headers, with the sortable columns hyperlinked
	 */
	echo "\t\t\t<TR>\r\n";
	echo "\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"left\" VALIGN=\"bottom\"><A HREF=\"$sortipurl\">IP Address</A></TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Port</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Status</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"right\" VALIGN=\"bottom\"><A HREF=\"$sortdownurl\">Bytes<BR>Remaining</A></TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"right\" VALIGN=\"bottom\"><A HREF=\"$sortupurl\">Uploaded</A></TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Last Update<SUP>1</SUP></TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"left\" VALIGN=\"bottom\"><A HREF=\"$sortversionurl\">Client Version (User Agent)</A></TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"left\" VALIGN=\"bottom\">Client</TD>\r\n";
	if ($_SESSION["admin_perms"]["ipban"] || $_SESSION["admin_perms"]["root"])
		echo "\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Ban IP?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Delete?</TD>\r\n";
	echo "\t\t\t</TR>\r\n";

	/*
	 * A really small row between the headers and the data, for visual reasons
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD COLSPAN=10 BGCOLOR=\"#ffffff\"></TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * These are the alternating Cascadying Style Sheet classes used for the data.
	 */
	$classRowBGClr[0] = 'CLASS="odd"';
	$classRowBGClr[1] = 'CLASS="even"';

	/*
	 * Initialize running total
	 */
	$totalpeers=0;

	/*
	 * cycle through the recordset and display needed information
	 */
	while ($row=mysql_fetch_row($recordset))
	{
		$peerid = $row[0];

		echo "\t\t\t<TR>\r\n";

		/*
		 * Calculate which background to use for the current row
		 */
		$cellBG = $classRowBGClr[$totalpeers % 2];

		/*
		 * IP Address
		 */
		echo "\t\t\t\t<TD $cellBG>";
		echo $row[1];
		echo "</TD>\r\n";

		/*
		 * Display the port peer is using
		 */
		echo "\t\t\t\t<TD $cellBG ALIGN=\"center\">$row[2]</TD>\r\n";

		/*
		 * Display the status of the peer
		 */
		echo "\t\t\t\t<TD $cellBG ALIGN=\"center\">$row[7]</TD>\r\n";

		/*
		 * Calculate in MiB the amount peer has yet to get,
		 * and show it
		 */
		$bytesremaining = round($row[5]/1048576,2) . " MiB";
		echo "\t\t\t\t<TD $cellBG ALIGN=\"right\">$bytesremaining</TD>\r\n";

		/*
		 * Calculate in MiB the amount peer has uploaded,
		 * and show it
		 */
		$bytesuploaded = round($row[3]/1048576,2) . " MiB";
		echo "\t\t\t\t<TD $cellBG ALIGN=\"right\">$bytesuploaded</TD>\r\n";

		/*
		 * Calculate when the client last updated, and display it
		 *
		 */
		$pastTime = $currentTime - $row[6];
		if ($pastTime > 120)
			$pastTime = round($pastTime / 60, 1) . " minutes ago";
		else
			$pastTime = $pastTime . " second(s) ago";

		$clientTimeStamp = getdate($row[6]);
		$clientTimeString = $clientTimeStamp['weekday'] . ", " . $clientTimeStamp['month'] . " " . $clientTimeStamp['mday'] . ", " . $clientTimeStamp['year'] . " @ " .
								 $clientTimeStamp['hours'] . ":" . $clientTimeStamp['minutes'] . ":" . $clientTimeStamp['seconds'];

		echo "\t\t\t\t<TD $cellBG ALIGN=\"center\">$clientTimeString<BR><B>($pastTime)</B></TD>\r\n";

		/*
		 * if there is a client version reported, remove
		 * escape characters and display it
		 */
		if (is_null($row[4]))
			$row[4]="";
		else
			$row[4] = stripslashes($row[4]);

		echo "\t\t\t\t<TD $cellBG ALIGN=\"left\">$row[4]</TD>\r\n";

		/*
		 * Decode and show the peer_id
		 */
		$decoded_peer_id = decodePeerID($row[0]);

		echo "\t\t\t\t<TD $cellBG ALIGN=\"left\">${decoded_peer_id['full']}</TD>\r\n";

		/*
		 * The action button are all radio buttons that are grouped by
		 * each hash value. This way there is no need to check for duplicate
		 * operations in the confirmation page.
		 *
		 * IP Ban peer radio button
		 */
		if ($_SESSION["admin_perms"]["ipban"] || $_SESSION["admin_perms"]["root"])
			echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><INPUT TYPE=radio NAME=\"process[$peerid]\" VALUE=".ACTION_PBAN.">B</TD>\r\n";

		/*
		 * Delete peer radio button
		 */
		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><INPUT TYPE=radio NAME=\"process[$peerid]\" VALUE=".ACTION_PDELETE.">D</TD>\r\n";

		/*
		 * Keep track of our total count for later
	 	 */
		$totalpeers++;
	
		echo "\t\t\t</TR>\r\n";
	}

	/*
	 * Calculate the background needed for the summary line
	 */
	$cellBG = $classRowBGClr[$totalpeers % 2];

	/*
	 * If there were no peers present indicate it, otherwise show
	 * the total peers present.
	 */
	if ($totalpeers == 0)
		echo "\t\t\t<TR>\r\n\t\t\t\t<TD $classRowBGClr[0] COLSPAN=12 ALIGN=CENTER>No peers present</TD>\r\n\t\t\t</TR>\r\n";
	else
		echo "\t\t\t<TR>\r\n\t\t\t\t<TD $cellBG ALIGN=\"center\" COLSPAN=12>$totalpeers peers displayed.</TD>\r\n\t\t\t</TR>\r\n";

	echo "\t\t\t</TABLE>\r\n\t\t</TD>\r\n\t</TR>\r\n";

	/*
	 * Show the summary of the tracker table
	 */
	echo "\t<TR>\r\n\t\t<TD ALIGN=LEFT CLASS=\"summary\">". $adm_page_title ." using MySQL.</TD>\r\n";
	echo "\t\t<TD ALIGN=RIGHT CLASS=\"summary\">Ki = 1024, Mi = 1024 Ki, Gi = 1024 Mi, Ti = 1024 Gi</TD>\r\n\t</TR>\r\n";

	/*
	 * Get the system time to show in the footnote
	 */
	$systemTimeStamp = getdate();
	$systemTimeString = $systemTimeStamp['weekday'] . ", " . $systemTimeStamp['month'] . " " . $systemTimeStamp['mday'] . ", " . $systemTimeStamp['year'] . "  " .
								 $systemTimeStamp['hours'] . ":" . $systemTimeStamp['minutes'] . ":" . $systemTimeStamp['seconds'];

	/*
	 * Show the footnote regarding current system time
	 */
	echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2 CLASS=\"summary\">";
	echo "<BR><SUP>1</SUP>Current system date/time: $systemTimeString</TD>\r\n\t</TR>\r\n";
?>
<TR>
	<TD ALIGN="center" COLSPAN=15><A HREF="bta_main.php">Return to main administration panel</A></TD>
</TR>
<?php
	echo "\t\t<TR>\r\n\t\t\t<TD>&nbsp</TD>\r\n\t\t</TR>\r\n";
	echo "\t\t<TR>\r\n\t\t\t<TD ALIGN=\"center\"><INPUT TYPE=\"submit\" NAME=\"processbutton\" VALUE=\"Ban/delete selected peers...\" CLASS=\"button\"></TD>\r\n";
	echo "\t\t\t<TD ALIGN=\"center\"><INPUT TYPE=reset VALUE=\"Clear ban/delete selections\" CLASS=\"button\"></TD>\r\n\t\t</TR>\r\n";
	echo "\t\t<TR>\r\n\t\t\t<TD>&nbsp</TD>\r\n\t\t</TR>\r\n";
?>
	</TABLE>
	</TD>
</TR>
</TABLE>
</FORM>
</BODY>
</HTML>
