<?php

	/*
	 * Module:	help_ip_bans.php
	 * Description: This module displays help on the IP banning interface.
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
	require_once ("../../config.php");

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
	require_once ("../../version.php");
	require_once ("../bta_funcs.php");

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

	if (!($_SESSION["admin_perms"]["ipban"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Permissions have been set to deny you access to this page.", $adm_pageerr_title);
		exit;
	}
?>
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<LINK REL="stylesheet" HREF="../admin.css" TYPE="text/css" TITLE="Default">
	<?php echo "<TITLE>IP Banning help - $phpbttracker_id $phpbttracker_ver</TITLE>\r\n"; ?>
</HEAD>
<BODY CLASS="help">
	<P CLASS="help_title"><?php echo "$phpbttracker_id $phpbttracker_ver - IP Banning help"; ?></P>
	<H2>Preamble</H2><BR>
	This tracker allows you to ban IP addresses. This will give them an error message if they try to connect. <B>NOTE</B>:
	this tracker needs IP Banning specifically enabled in its configuration file. If you don't you can add addresses
	all you want, but they will not be refused access. <B>This functionality is DISABLED by default</B>, due to processing overhead!<BR><BR>

	<H2>Active ban list</H2><BR>
	
	This page will list all of the bans active on the tracker. It will show the IP address, when they were banned, an expiry date
	(if the ban will expire), the reason for the ban, and whether or not it was an automatic ban (see below.) If client filtering 
	is enabled, certain "bad" clients can be autobanned, and this will be indicated on the main page, with the reason, date of the ban, 
	and the IP banned. If the administrator allows it, IP Bans can be added through the Peer listing screen. To remove a ban,
	click the checkbox at the right of the table that corresponds to the IP address desired. When done, click <B>Process ban requests</B>
	to remove the bans.<BR><BR>

	<H2>Automatic banning</H2>
	
	As mentioned in the section above, this tracker is capable of automatically banning IP addresses based on the type of client reported to
	the tracker. This is indicated in the <I>Automatic Ban?</I> column. These dates usually expire and the date is indicated when the ban
	is automatically lifted. The expired bans are removed daily to reduce clutter in the database by maintenance.php, so make sure you
	crontab it!
	
	<H2>How do I?</H2><BR>
	
	<B>To add a ban</B>, simply enter the IP and the reason for the ban, then click the <B>Process ban requests</B> button. You may also specify
	a ban length if needed (in days.) <B>Wildcards are NOT supported!</B> You need to enter individual IP addresses to ban.<BR><BR>

	<B>To remove a ban</B>: Check the checkbox by the IP you want to remove and click the <B>Process ban requests</B> button.<BR><BR>
	
	<B>To remove all bans:</B> a button is provided at the bottom of the page to do this. There is <U>no confirmation</U> for this action!
</BODY>
</HTML>