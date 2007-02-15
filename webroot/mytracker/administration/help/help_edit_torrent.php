<?php

	/*
	 * Module:	help_edit_torrent.php
	 * Description: This module displays help for the edit torrent screen.
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
?>
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<LINK REL="stylesheet" HREF="../admin.css" TYPE="text/css" TITLE="Default">
	<?php echo "<TITLE>Edit torrent help - $phpbttracker_id $phpbttracker_ver</TITLE>\r\n"; ?>
</HEAD>
<BODY CLASS="help">
	<P CLASS="help_title"><?php echo "$phpbttracker_id $phpbttracker_ver - Edit torrent help"; ?></P>
	
	This page provides you with a way to edit the torrents that exist on the tracker. Below is a table with the
	fields you can edit and what they mean.<BR><BR>

	<CENTER>
	<TABLE BORDER=1>
	<TR>
		<TH ALIGN=LEFT>Item</TH>
		<TH ALIGN=LEFT>Explanation</TH>
	</TR>
	<TR>
		<TD><B>Info hash</B></TD>
		<TD>The hash value for the torrent. Not editable.</TD>
	</TR>
	<TR>
		<TD><B>File name</B></TD>
		<TD>This is the file name shown on the index page.</TD>
	</TR>
	<TR>
		<TD><B>File size</B></TD>
		<TD>This is the file size, <B>in megabytes</B>.</TD>
	</TR>
	<TR>
		<TD><B>URL to torrent</B></TD>
		<TD>This is the URL to the torrent.</TD>
	</TR>
	<TR>
		<TD><B>Mirror for torrent</B></TD>
		<TD>If you have another website mirroring your torrent, enter the link here, and it will be displayed on the index page.</TD>
	</TR>
	<TR>
		<TD><B>URL to SFV file</B></TD>
		<TD>Enter the url to an sfv (checksum) file here. This link will appear in the CRC column, below the CRC info entered below.</TD>
	</TR>
	<TR>
		<TD><B>URL to MD5 file</B></TD>
		<TD>Enter the url to an md5 (checksum) file here. This link will appear in the CRC column, below the CRC info entered below.</TD>
	</TR>
	<TR>
		<TD><B>Info URL</B></TD>
		<TD>If you have a webpage with info about the release, you can enter it here.</TD>
	</TR>
	<TR>
		<TD><B>Description</B></TD>
		<TD>Enter a short description for the torrent; this will be displayed on the index page.</TD>
	</TR>
	<TR>
		<TD><B>Category</B></TD>
		<TD>Enter the category this torrent falls under. <B>NOTE:</B> This is NOT shown to all users! If it is not shown, a category has been assigned to you and will be used automatically.</TD>
	</TR>
	<TR>
		<TD><B>CRC32 Checksum</B></TD>
		<TD>Enter any CRC info for the torrent; this will be displayed on the index page.</TD>
	</TR>
	<TR>
		<TD><B>Date Added</B></TD>
		<TD>The date the torrent was added. Use the format YYYY-MM-DD.</TD>
	</TR>
	<TR>
		<TD><B>Remove URL</B></TD>
		<TD>If you enter a date, the index page will stop showing the hyperlink to the torrent file. The torrent remains active on the tracker. Use the format YYYY-MM-DD.</TD>
	</TR>
	<TR>
		<TD><B>Hide from index</B></TD>
		<TD>If you enter a date, the index page will hide the torrent completely. The torrent remains active on the tracker. Use the format YYYY-MM-DD.</TD>
	</TR>
	<TR>
		<TD><B>Hide this torrent</B></TD>
		<TD>Check this to manually hide the torrent. It will not be shown on the index page, nor will it be seen in any /scrape output.</TD>
	</TR>
	<TR>
		<TD><B>Remove external status (not reversible!)</B></TD>
		<TD>Check this to remove the external status of the torrent. It will no longer scan external trackers; it will show whatever stats in the tracker database.</TD>
	</TR>
	<TR>
		<TD><B>Reset Date Added to today's date</B></TD>
		<TD>Check this to reset the date torrent was added to the current day.</TD>
	</TR>
	<TR>
		<TD><B>Comment</B></TD>
		<TD>This text can be shown in place of the torrent statistics.</TD>
	</TR>
	<TR>
		<TD><B>Show comment (replaces statistics!)</B></TD>
		<TD>Check this to show the comment above where the torrent statistics are. This replaces the statistics and they will not be shown.</TD>
	</TR>
	</TABLE>
	</CENTER>
</BODY>
</HTML>