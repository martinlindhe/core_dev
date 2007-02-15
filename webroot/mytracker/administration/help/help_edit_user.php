<?php

	/*
	 * Module:	help_edit_user.php
	 * Description: This module displays help for the edit user screen.
	 *
	 * Author:	danomac
	 * Written:	7-June-2004
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

	/*
	 * Group admin: are they actually allowed to view this page?
	 * If not, redirect them back to main
	 */
	if (!($_SESSION["admin_perms"]["usermgmt"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Permissions have been set to deny you access to this page.", $adm_pageerr_title);
		exit;
	}
?>
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<LINK REL="stylesheet" HREF="../admin.css" TYPE="text/css" TITLE="Default">
	<?php echo "<TITLE>Edit user help - $phpbttracker_id $phpbttracker_ver</TITLE>\r\n"; ?>
</HEAD>
<BODY CLASS="help">
	<P CLASS="help_title"><?php echo "$phpbttracker_id $phpbttracker_ver - Edit user help"; ?></P>
	
	This page allows you to edit a user. Make the changes desired and click the <B>Apply changes</B> button<BR><BR>

	As a minimum, you need to specify a username which is at least 8 characters in length, and a category name.
	To reset the password enter the new password in the boxes provided, and click the <B>Apply changes</B> button.<BR><BR>

	Below is a table with more detailed information on each permission shown on this page.<BR>
	<CENTER>
	<TABLE BORDER=1>
	<TR>
		<TH ALIGN=LEFT>Permission</TH>
		<TH ALIGN=LEFT>Explanation</TH>
	</TR>
	<TR>
		<TD>Enabled</TD>
		<TD>Check to enable account; uncheck to disable it.</TD>
	</TR>
	<TR>
		<TD>Disable reason</TD>
		<TD>Message displayed to user when they try to login to account and it is disabled. <B>NOTE</B>: This is <U>required</U> when disabling an account!</TD>
	</TR>
	<TR>
		<TD>Add torrents</TD>
		<TD>This gives the user permission to add torrents to the category they have been restricted to.</TD>
	</TR>
	<TR>
		<TD>Add external torrents</TD>
		<TD>Allows the user to add an external torrent to their category, one that is not active on your tracker. See the README files for more information on this. Some special configuration is required for this to work. <B>NOTE: This will not do anything if the <I>Add torrent</I> permission is denied!</B></TD>
	</TR>
	<TR>
		<TD>Add mirror torrents</TD>
		<TD>Allows the user to add a mirrored torrent to their category, one that is specifies your tracker as a backup tracker. <B>NOTE: This will not do anything if the <I>Add torrent</I> permission is denied!</B></TD>
	</TR>
	<TR>
		<TD>Edit torrents</TD>
		<TD>This gives the user permission to edit torrents that exist in the category that they have been restricted to.</TD>
	</TR>
	<TR>
		<TD>Delete torrents</TD>
		<TD>This gives the user permission to remove torrents from the category they have been restricted to.</TD>
	</TR>
	<TR>
		<TD>Retire torrents</TD>
		<TD>This gives the user permission to retire torrents in the category they have been restricted to.</TD>
	</TR>
	<TR>
		<TD>Unhide torrents</TD>
		<TD>If one of their torrents is hidden, provides the user with a quick way to unhide it.</TD>
	</TR>
	<TR>
		<TD>View peers</TD>
		<TD>Allows the user to view the peers currently on one of their torrents.</TD>
	</TR>
	<TR>
		<TD>View tracker configuration</TD>
		<TD>Shows the current tracker configuration to the user. Usually not needed by the users.</TD>
	</TR>
	<TR>
		<TD>Retired torrent management</TD>
		<TD>This allows the user to manage their own retired torrents. For example, the could reactivate a torrent that has been retired, or just remove them.</TD>
	</TR>
	<TR>
		<TD>Allow IP Banning</TD>
		<TD>This allows the user to ban an IP from the tracker. <B>This is TRACKER-WIDE. Be careful who you give this permission to!</B></TD>
	</TR>
	<TR>
		<TD>Manage users</TD>
		<TD>Allows the user to add, edit and remove users. <B>This is tracker wide, they will be able to change every user!</B></TD>
	</TR>
	<TR>
		<TD>Advanced sorting</TD>
		<TD>Allows the user to group and manually sort torrents.</TD>
	</TR>
	</TABLE>
	</CENTER>
</BODY>
</HTML>