<?php

	/*
	 * Module:	help_add_torrent.php
	 * Description: This module displays help for the add torrent screen
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

	if (!($_SESSION["admin_perms"]["add"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Permissions have been set to deny you access to this page.", $adm_pageerr_title);
		exit;
	}
?>
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<LINK REL="stylesheet" HREF="../admin.css" TYPE="text/css" TITLE="Default">
	<?php echo "<TITLE>Add torrent help - $phpbttracker_id $phpbttracker_ver</TITLE>\r\n"; ?>
</HEAD>
<BODY CLASS="help">
	<P CLASS="help_title"><?php echo "$phpbttracker_id $phpbttracker_ver - Add torrent help"; ?></P>
	
	This tracker stores different kinds of information about the torrent you are adding. Most of this "extra"
	information is not required. At the minimum, you need a torrent file. If you do not specify a category, "main"
	is assumed, unless you have had a category assigned to you.<BR><BR>

	Below is a table with all the fields used in the Add Torrent screen and an explanation of what they do. Items in <FONT COLOR=RED>red</FONT> are required. Please note
	that only the Torrent file OR both the hash and file size are required. Simply put, if it isn't shown to you, it isn't required!<BR>
	<CENTER>
	<TABLE BORDER=1>
	<TR>
		<TH ALIGN=LEFT>Item</TH>
		<TH ALIGN=LEFT>Explanation</TH>
	</TR>
	<TR>
		<TD><FONT COLOR=RED><B>Torrent File</B></FONT></TD>
		<TD>If you have a torrent file, click <I>Browse</I> and select your torrent to be added. <B>NOTE:</B> This is not displayed if the tracker cannot handle decoding the metadata.</TD>
	</TR>
	<TR>
		<TD><FONT COLOR=RED><B>Info hash</B></FONT></TD>
		<TD>Enter the info hash value manually here. <B>NOTE:</B> This is only displayed when the tracker is unable to decode metadata files.</TD>
	</TR>
	<TR>
		<TD><FONT COLOR=RED><B>File size</B></FONT></TD>
		<TD>Enter the file size manually, <B>in bytes</B>. <B>NOTE:</B> This is only displayed when the tracker is unable to decode metadata files.</TD>
	</TR>
	<TR>
		<TD><B>File name</B></TD>
		<TD>If you want to use a different filename, enter it here. <B>NOTE:</B> If <I>Fill in fields using data from the torrent file</I> is checked, whatever you enter here is overridden!</TD>
	</TR>
	<TR>
		<TD><B>URL to torrent</B></TD>
		<TD>This is the URL to the torrent. <B>NOTE:</B> If you check <I>Copy torrent to torrent folder</I> below this will be entered for you!</TD>
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
		<TD><B>CRC Info</B></TD>
		<TD>Enter any CRC info for the torrent; this will be displayed on the index page.</TD>
	</TR>
	<TR>
		<TD><B>Remove URL</B></TD>
		<TD>If you enter a date, the index page will stop showing the hyperlink to the torrent file. The torrent remains active on the tracker.</TD>
	</TR>
	<TR>
		<TD><B>Hide from index</B></TD>
		<TD>If you enter a date, the index page will hide the torrent completely. The torrent remains active on the tracker.</TD>
	</TR>
	<TR>
		<TD><B>Add to group</B></TD>
		<TD>If the tracker administrator has allowed this, choose the torrent group you wish to add this torrent to.</TD>
	</TR>
	<TR>
		<TD><B>Hide this torrent</B></TD>
		<TD>Check this to manually hide the torrent. It will not be shown on the index page, nor will it be seen in any /scrape output.</TD>
	</TR>
	<TR>
		<TD><B>This is a backup torrent from another tracker</B></TD>
		<TD>Check this to add torrent as a backup. It will not be treated as an external torrent and it will verify the tracker is in the backup tracker list specified in the torrent.</TD>
	</TR>
	<TR>
		<TD><B>Copy torrent to webserver?</B></TD>
		<TD>Check this to copy the torrent to the webserver. <B>NOTE:</B> This is not displayed if the tracker cannot handle decoding the metadata, or if the tracker owner disables it.</TD>
	</TR>
	<TR>
		<TD><B>Copy torrent to webserver only</B></TD>
		<TD>Check this to only copy the torrent to the webserver. Tables required for operation of the torrent will not be created. <B>NOTE:</B> This is not displayed if the tracker cannot handle decoding the metadata, or if the tracker owner disables it.</TD>
	</TR>
	<TR>
		<TD><B>Add as an external reference (if applicable)</B></TD>
		<TD>Check this to add the torrent as an external torrent if needed. <B>NOTE:</B> This may not be displayed. The tracker owner may disable it or have it automatically add external torrents. In either of those cases, this won't be shown.</TD>
	</TR>
	<TR>
		<TD><B>Fill in fields using data from the torrent file</B></TD>
		<TD>Check this to use the torrent name and size from information in the uploaded torrent file. <B>NOTE:</B> This is not displayed if the tracker cannot handle decoding the metadata.</TD>
	</TR>
	</TABLE>
	</CENTER>
</BODY>
</HTML>