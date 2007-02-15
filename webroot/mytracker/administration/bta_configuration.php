<?php

	/*
	 * Module:	bta_configuration.php
	 * Description: This module shows the tracker configuration
	 * 		in an easy to read format. Beats reading config.php.
	 *
	 * Author:	danomac
	 * Written:	1-April-2004
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
	 * List of the external modules required
	 */
	require_once ("../version.php");
	require_once ("bta_funcs.php");

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
	if (!($_SESSION["admin_perms"]["viewconf"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Redirecting to the main administration panel.",
			       $adm_page_title, true, "bta_main.php", 3);
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
	echo "<TITLE>". $adm_page_title . " - Tracker configuration</TITLE>\r\n";
?>
</HEAD>

<BODY>

<TABLE CLASS="tblAdminOuter">
<TR>
<?php
	/*
	 * Display the page heading.
	 */
	echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>Tracker configuration</TD>\r\n";
?>
</TR>
<?php admShowURL_Login($ip); ?>
<TR>
	<TD ALIGN="center" COLSPAN=15><BR><A HREF="bta_main.php">Return to main administration panel</A></TD>
</TR>
<TR>
	<TD CLASS="data" ALIGN="center" COLSPAN=15>
	<BR><B>Tracker configuration details</B><BR><BR>
		<TABLE BORDER=0>
<?php
	/*
	 * Display information about the trackers configuration.
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\"><B>Item</B></TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\"><B>Value</B></TD>\r\n\t\t\t</TR>\r\n";

	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableheading\" COLSPAN=15><B>Core tracker settings</B></TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * report_interval
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Client MAX announce interval (in minutes):</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["report_interval"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { echo round($GLOBALS["report_interval"] / 60, 2); }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * min_interval
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Client MIN announce interval (in minutes):</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["min_interval"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { echo round($GLOBALS["min_interval"] / 60, 2); }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * maxpeers
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Maximum number of peers to send at any one time:</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["maxpeers"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { echo $GLOBALS["maxpeers"]; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * scrape_min_interval
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Requested client /scrape interval (in minutes):</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["scrape_min_interval"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { echo round($GLOBALS["scrape_min_interval"] / 60, 2); }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * dynamic_torrents
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Torrents need to be authorized?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["dynamic_torrents"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["dynamic_torrents"]) echo "No"; else echo "Yes"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * NAT
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Check to see if client is NAT'ed?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["NAT"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["NAT"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * persist
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Database connection type:</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["persist"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["persist"]) echo "Persistent"; else echo "Normal"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * ip_override
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Allow clients to override IP?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["ip_override"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["ip_override"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * enable_scrape
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Enable scrape output?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["allow_scrape"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["allow_scrape"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * scrape_extras
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Enable extra scrape output (amount uploaded, etc...)?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["scrape_extras"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["scrape_extras"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * peercaching
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Enable peer caching?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["peercaching"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["peercaching"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * countbytes
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Enable collection of bytes downloaded and torrent speed calculations?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["countbytes"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["countbytes"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * doavg
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Enable torrent average progress calculations?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["doavg"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["doavg"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * heavyload
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Enable optimizations for heavily loaded trackers?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["heavyload"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["heavyload"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * compactonly
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Only allow clients that support the compact protocol to connect?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["compactonly"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["compactonly"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableheading\" COLSPAN=15><B>Automatic update intervals</B></TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * spd_refresh
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Speed statistics auto-update interval (in minutes):</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["spdrefresh"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { echo round($GLOBALS["spdrefresh"] / 60, 2); }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * avgrefresh
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Average % done statistic auto-update interval (in minutes):</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["avgrefresh"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { echo round($GLOBALS["avgrefresh"] / 60, 2); }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * external tracker scanning
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">External tracker stats auto-update interval (in minutes):</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["external_refresh"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { echo $GLOBALS["external_refresh"]; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableheading\" COLSPAN=15><B>Tracker mods</B></TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * filter_clients
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Use built-in client filtering?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["filter_clients"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["filter_clients"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * allow_group_admin
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Enable independent group administration?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["allow_group_admin"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["allow_group_admin"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * allow_external_scanning
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Enable external tracker scanning?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["allow_external_scanning"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["allow_external_scanning"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * ext_batch_scrape
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Method used to scan external trackers?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["ext_batch_scrape"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["ext_batch_scrape"]) echo "all (not recommended)"; else echo "individual"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * allow scrape_scan.php script usage?
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Allow scrape_scan.php usage?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["scrape_scanning"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["scrape_scanning"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableheading\" COLSPAN=15><B>Tracker banning engine</B></TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * enable_ip_banning
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Enable tracker IP banning system?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["enable_ip_banning"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["enable_ip_banning"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * allow_unidentified_clients
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Allow clients that don't identify themself?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["allow_unidentified_clients"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["allow_unidentified_clients"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * autobanlength
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Automatic ban lenth (in days):</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["autobanlength"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { echo round($GLOBALS["autobanlength"], 2); }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableheading\" COLSPAN=15><B>Database consistency</B></TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * auto_db_check_scrape
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Automatically check database consistency via /scrape?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["auto_db_check_scrape"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["auto_db_check_scrape"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableheading\" COLSPAN=15><B>Administration interface - Advanced settings</B></TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * webserver_farm
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Webserver part of webserver farm?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["webserver_farm"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["webserver_farm"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * webserver_farm_session_path
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Webserver farm session path:</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["webserver_farm_session_path"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { echo $GLOBALS["webserver_farm_session_path"]; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";


	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableheading\" COLSPAN=15><B>Add torrents - Advanced settings</B></TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * allow_torrent_move
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Enable uploading of torrents?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["allow_torrent_move"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["allow_torrent_move"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * Upload destination - move_to_db
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Copy torrents to:</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["move_to_db"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["move_to_db"]) echo "database"; else echo "web folder"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * torrent folder
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Torrent folder (used when uploading to web folder only):</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["torrent_folder"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { echo $_SERVER["DOCUMENT_ROOT"] . "/" . $GLOBALS["torrent_folder"]; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * maximum torrent size
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Max torrent size for upload (in Kib):</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["max_torrent_size"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { echo round($GLOBALS["max_torrent_size"] / 1024, 2); }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * auto add external torrents
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">Automatically add external torrents for scanning?</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["auto_add_external_torrents"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { if ($GLOBALS["auto_add_external_torrents"]) echo "Yes"; else echo "No"; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

	/*
	 * my announce url
	 */
	echo "\t\t\t<TR>\r\n\t\t\t\t<TD CLASS=\"conftableitem\">My announce URL:</TD>\r\n";
	echo "\t\t\t\t<TD CLASS=\"conftablevalue\">";
	if (!isset($GLOBALS["my_tracker_announce"])) { echo "<FONT COLOR=RED><B>Not set!</B></FONT>"; } else { echo $GLOBALS["my_tracker_announce"]; }
	echo "</TD>\r\n\t\t\t</TR>\r\n";

?>
		</TABLE>
	</TD>
</TR>

<TR>
	<TD COLSPAN=3 ALIGN="center">NOTE: To change these settings, edit config.php.</TD>
</TR>
<TR>
	<TD ALIGN="center" COLSPAN=15><BR><A HREF="bta_main.php">Return to main administration panel</A></TD>
</TR>
</TABLE>
</BODY>
</HTML>
