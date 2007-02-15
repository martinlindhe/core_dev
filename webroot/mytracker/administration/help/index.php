<?php

	/*
	 * Module:	index.php
	 * Description: This module displays help for the main screen.
	 *
	 * Author:	danomac
	 * Written:	30-May-2004
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
<BR><B>Quick HOWTO:</B><BR>
<B>Add a torrent:</B> Use the URL above, in the <I>Administrative functions</I> section.<BR>
<B>Edit a torrent:</B> Look through the list below, and click the Edit hyperlink in the <I>Status/Admin</I> column.<BR>
<B>Delete a torrent:</B> Locate the torrent in the list below, and check the checkbox in the Delete Column. You can mark several items, then use the button at the bottom of the form.<BR>
<B>Retire a torrent:</B> Locate the torrent in the list below, and check the checkbox in the Retire Column. You can mark several items, then use the button at the bottom of the form.<BR>
<B>Unhide a hidden torrent:</B>  Locate the torrent in the list below, and check the checkbox in the Unhide Column. You can mark several items, then use the button at the bottom of the form.<BR>
<B>Show seeder information:</B> Locate the torrent in the list below, and click the <I>Seeders</I> hyperlink in the <I>Status/Admin</I> column. This information appears in a new window and does not require a person to be logged in to the administrative interface.<BR>
<B>Manage peers:</B> Locate the torrent in the list below, and click the <I>Peers</I> hyperlink in the <I>Status/Admin</I> column. You can view, ban, and kill peers in this section.<BR>
<B>Manage retired torrents:</B> Use the <I>View/Revive/Delete Retired torrents</I> link in the <I>Administrative functions</I> section at the top of this page.<BR>
<B>Manage Bans:</B> Use the <I>IP Banning</I> link in the <I>Administrative functions</I> section at the top of this page.<BR>
<B>Do a database check:</B> Use the <I>Manual database consistency check</I> link in the <I>Administrative functions</I> section at the top of this page.<BR>
<B>Logout:</B> Use the <I>Log off</I> link in the <I>Administrative functions</I> section at the top of this page.<BR>
<BR><BR>
*/
?>
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<LINK REL="stylesheet" HREF="../admin.css" TYPE="text/css" TITLE="Default">
	<?php echo "<TITLE>Main help - $phpbttracker_id $phpbttracker_ver</TITLE>\r\n"; ?>
</HEAD>
<BODY CLASS="help">
	<P CLASS="help_title"><?php echo "$phpbttracker_id $phpbttracker_ver - Main help"; ?></P>

	<H2>Preamble</H2><BR>
	Welcome to the main administration screen help. This page shows all torrents that are active on the tracker and statistics about them. 
	External torrents are included in the list and are listed seperately in the summary line. The statistics themselves are fairly
	straight forward, and are listed below in a table.<BR><BR>

	What you can do through this interface is solely dependent on the permissions the tracker owner has set. All of the functionality
	will be shown in the help screens; if you can't locate something it is likely you do not have access to all of it.<BR><BR>
	
	If you are logged on as the root user, you will see all torrents active on the tracker, and all administrative functions are available
	to you. At the top of the table, a shortcut box lists the groups active on the tracker and hyperlinks to jump to each section.<BR><BR>

	<H2>Administrative functions</H2><BR>
	
	If you look near the top of the page, there is a section titled <B>Administrative functions</B>. What exactly is listed there
	depends on your permissions set by the owner of the tracker. All 7 options are listed below in a table:

	<CENTER>
	<TABLE BORDER=1>
	<TR>
		<TH ALIGN=LEFT>Item</TH>
		<TH ALIGN=LEFT>Description</TH>
	</TR>
	<TR>
		<TD><B>Add new torrent</B></TD>
		<TD>Click to add a new torrent.</TD>
	</TR>
	<TR>
		<TD><B>IP Banning</B></TD>
		<TD>Click to add IP bans, if needed. The tracker owner has to enable banning specifically in the configuration or it will have no effect.</TD>
	</TR>
	<TR>
		<TD><B>View/revive/delete Retired torrents</B></TD>
		<TD>This is a subset of the administration interface that allows you to view, remove, and if you like, make an inactive torrent active again.</TD>
	</TR>
	<TR>
		<TD><B>User management</B></TD>
		<TD>Click to add, edit, and remove users that can access the tracker. The tracker owner has to enable group logins in the configuration file for these users to be able to login.</TD>
	</TR>
	<TR>
		<TD><B>Manual database consistency check</B></TD>
		<TD>If stats are displayed incorrectly (seeders, leechers) this may correct it. Most tracker owners should have this configured automatically to run every few hours, so ask before you use it.</TD>
	</TR>
	<TR>
		<TD><B>Advanced Sorting</B></TD>
		<TD>This provides an alternate means of displaying torrents to the user. This including torrent grouping and manual sorting; these items are shown on the tracker stats pages only and not in the administrative interface. The tracker owner needs to specifically enable this in the scripts and grant usernames access to this screen.</TD>
	</TR>
	<TR>
		<TD><B>Logoff</B></TD>
		<TD>Remember to use this link to log off.</TD>
	</TR>
	</TABLE>
	</CENTER><BR><BR>

	Below the Administrative functions section is a <B>Login Information</B> section, which will display information on where you are logging in from.
	Below the Login Information section, the tracker's current configuration can be displayed. (Not all users are able to see this.)<BR><BR>

	Some of the administration tasks are done on each of the torrents individually. In the <B>Status/Action</B> column, you can Edit the torrent,
	view a list of peers currently on the torrent, or simply view seeders on the torrent (note that you can ONLY edit external torrents.)
	Some tasks can be applied to multiple torrents simultaneously; these commands are listed in the dropdown box for each torrent. 
	<B>NOTE:</B> Not all of these commands will be available to all users.<BR><BR>

	<H2>Torrent list</H2>
	
	What's the information being displayed? Here is a table with description of what's displayed in the main window.<BR>
	<CENTER>
	<TABLE BORDER=1>
	<TR>
		<TH ALIGN=LEFT>Item</TH>
		<TH ALIGN=LEFT>Description</TH>
	</TR>
	<TR>
		<TD><B>Torrent Name / Info Hash</B></TD>
		<TD>In this column, the name of the torrent is displayed as well as the info hash. Note that descriptions and URL links are not displayed on the admin page. Users logged on as root will also be able to see what user added each torrent.</TD>
	</TR>
	<TR>
		<TD><B>Size / CRC32</B></TD>
		<TD>The torrent size and any CRC32 information is displayed in this column. The SFV and MD5 URLs are not displayed.</TD>
	</TR>
	<TR>
		<TD><B>Status / Action</B></TD>
		<TD>This column houses the torrent status (this can be: Active, Hidden, External) and administrative functions you can use on each individual torrent (Edit/Peers/Seeders). Actions that can be applied to multiple torrents are located in the dropdown box (Hide, Unhide, Retire, Delete.)</TD>
	</TR>
	<TR>
		<TD><B>Dates: Added / Remove URL / Hide Torrent</B></TD>
		<TD>This column shows when the torrent was added, when it will hide the URL for torrent download, and when it will hide the torrent completely from the index page.</TD>
	</TR>
	<TR>
		<TD><B>UL / DL / Done / XFER</B></TD>
		<TD>This column is the basic stats on the torrent. It shows active seeders and leechers, how many downloads were completed, and how much transfer happened over the life of the torrent.</TD>
	</TR>
	<TR>
		<TD><B>Category / Speed / Avg % Done</B></TD>
		<TD>This shows more advanced stats, like the current speed of the torrent and the average progress on the torrent. Root users will also see what category the torrent belongs to.</TD>
	</TR>
	</TABLE>
	</CENTER>
	<BR><BR>
	
	<H2>How do I?</H2>

	Here is a question/answer list that pertains to the tracker. This is not a complete list; just frequently asked questions.
	Please remember that you may not have permission to do all the things listed here.<BR><BR>
	
	<CENTER>
	<TABLE BORDER=1>
	<TR>
		<TH ALIGN=LEFT>Question</TH>
		<TH ALIGN=LEFT>Answer</TH>
	</TR>
	<TR>
		<TD><B>Add a torrent</B></TD>
		<TD>Click <B>Add a torrent</B> link under <I>Administrative functions</I>.</TD>
	</TR>
	<TR>
		<TD><B>Edit a torrent</B></TD>
		<TD>Search through the list for the torrent to edit, then click the <B>Edit</B> hyperlink in the <I>Status/Action</I> column.</TD>
	</TR>
	<TR>
		<TD><B>Remove a torrent</B></TD>
		<TD>There are 2 ways: removing permanently and retiring.<BR><BR><U>Removing permanently:</U> Search through the list for the torrent to remove, choose the <I>Delete</I> option in the dropdown, then scroll to the bottom of the list and press the <B>Unhide/hide/retire/delete selected torrents</B> button.<BR><BR><U>Retiring a torrent:</U> Search through the list for the torrent to remove, choose the <I>Retire</I> option in the dropdown, then scroll to the bottom of the list and press the <B>Unhide/hide/retire/delete selected torrents</B> button.</TD>
	</TR>
	<TR>
		<TD><B>Hide a torrent</B></TD>
		<TD>Either check the <B>Hide this torrent</B> checkbox in either the Add torrent or Edit torrent screen, or choose the <I>Hide</I> option in the dropdown, then scroll to the bottom of the list and press the <B>Unhide/hide/retire/delete selected torrents</B> button.</TD>
	</TR>
	<TR>
		<TD><B>Show a hidden torrent</B></TD>
		<TD>Look through the list for the hidden torrent, choose the <I>Unhide</I> option in the dropdown, then scroll to the bottom of the page and click the <B>Unhide/hide/retire/delete selected torrents</B> button.</TD>
	</TR>
	<TR>
		<TD><B>See who's connected to a torrent</B></TD>
		<TD>Look through the list for the torrent you are interested in, and click <B>Peers</B> in the <I>Status/Action</I> column.</TD>
	</TR>
	<TR>
		<TD><B>See seeders on a torrent</B></TD>
		<TD>Look through the list for the torrent you are interested in, and click <B>Seeders</B> in the <I>Status/Action</I> column.</TD>
	</TR>
	<TR>
		<TD><B>Ban/unban an IP from connecting to the tracker</B></TD>
		<TD>Click on the <B>IP Banning</B> link under <I>Administrative functions</I>.</TD>
	</TR>
	<TR>
		<TD><B>Make an inactive torrent active</B></TD>
		<TD>Click on the <B>View/revive/delete Retired torrents</B> link under <I>Administrative functions</I>, and look for help on <I>Reviving</I> torrents.</TD>
	</TR>
	<TR>
		<TD><B>Manually correct stats</B></TD>
		<TD>Click on the <B>Manual database consistency check</B> link under <I>Administrative functions</I>.</TD>
	</TR>
	<TR>
		<TD><B>Add/edit/remove users</B></TD>
		<TD>Click on the <B>User management</B> link under <I>Administrative functions</I>.</TD>
	</TR>
	<TR>
		<TD><B>Group torrents together</B></TD>
		<TD>Click on the <B>Advanced sorting</B> link under <I>Administrative functions</I>.</TD>
	</TR>
	<TR>
		<TD><B>Hide a torrent after a specified time</B></TD>
		<TD>Enter a date in the <B>Hide from index</B> box in either the Add torrent or Edit torrent screen.</TD>
	</TR>
	<TR>
		<TD><B>Hide the torrent's url after a specified time</B></TD>
		<TD>Enter a date in the <B>Remove URL</B> box in either the Add torrent or Edit torrent screen.</TD>
	</TR>
	</TABLE>
	</CENTER>
</BODY>
</HTML>