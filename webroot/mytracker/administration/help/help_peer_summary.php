<?php

	/*
	 * Module:	help_peer_summary.php
	 * Description: This module displays help for the peer summary screen.
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

	if (!($_SESSION["admin_perms"]["peers"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Permissions have been set to deny you access to this page.", $adm_pageerr_title);
		exit;
	}
?>
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<LINK REL="stylesheet" HREF="../admin.css" TYPE="text/css" TITLE="Default">
	<?php echo "<TITLE>Peer summary help - $phpbttracker_id $phpbttracker_ver</TITLE>\r\n"; ?>
</HEAD>
<BODY CLASS="help">
	<P CLASS="help_title"><?php echo "$phpbttracker_id $phpbttracker_ver - Peer summary help"; ?></P>
	
	The peer summary page lists all of the people currently connected to the specified torrent. You can IP ban
	(if the permission has been set for you to allow this, if not, it is not shown) and also remove a stale peer
	manually. Most of the items shown are self-explanatory. Here is a quick list of what's displayed:<BR><BR>

	<CENTER>
	<TABLE BORDER=1>
	<TR>
		<TH ALIGN=LEFT>Item</TH>
		<TH ALIGN=LEFT>Meaning</TH>
	</TR>
	<TR>
		<TD>IP Address</TD>
		<TD>The IP address of the person connected to the torrent.</TD>
	</TR>
	<TR>
		<TD>Port</TD>
		<TD>The port their client is using.</TD>
	</TR>
	<TR>
		<TD>Status</TD>
		<TD>Whether or not the are seeding and/or leeching.</TD>
	</TR>
	<TR>
		<TD>Bytes Remaining</TD>
		<TD>This is how much of the torrent that the client is still waiting to get.</TD>
	</TR>
	<TR>
		<TD>Uploaded</TD>
		<TD>How much the client has contributed to the torrent.</TD>
	</TR>
	<TR>
		<TD>Last Update</TD>
		<TD>Last time the client reported to the tracker.</TD>
	</TR>
	<TR>
		<TD>Client Version</TD>
		<TD>The ID string of the version of the client they are using (however, some nasty clients allow you to spoof this.)</TD>
	</TR>
	<TR>
		<TD>IP Ban</TD>
		<TD>Click on the radio button to ban the IP from the tracker. (You can select multiple peers.) When done, click the <B>Ban/delete selected peers...</B> button. <B>THIS IS NOT DISPLAYED IF YOU DO NOT HAVE PERMISSION TO BAN PEERS.</B></TD>
	</TR>
	<TR>
		<TD>Delete</TD>
		<TD>Click on the radio button to forcibly remove the peer. (You can select multiple peers.) When done, click the <B>Ban/delete selected peers...</B> button.</TD>
	</TR>
	</TABLE>
	</CENTER>
</BODY>
</HTML>