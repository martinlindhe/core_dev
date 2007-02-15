<?php

	/*
	 * Module:	help_advanced_sort.php
	 * Description: This module displays help for the sorting screen.
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
	 * Group admin: are they actually allowed to view this page?
	 * If not, redirect them back to main
	 */
	if (!($_SESSION["admin_perms"]["advsort"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Permissions have been set to deny you access to this page.", $adm_pageerr_title);
		exit;
	}
	
?>
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<LINK REL="stylesheet" HREF="../admin.css" TYPE="text/css" TITLE="Default">
	<?php echo "<TITLE>Advanced sorting help - $phpbttracker_id $phpbttracker_ver</TITLE>\r\n"; ?>
</HEAD>
<BODY CLASS="help">
	<P CLASS="help_title"><?php echo "$phpbttracker_id $phpbttracker_ver - Advanced sorting help"; ?></P>
	
	<H2>Preamble</H2>
	
	This tracker allows you to create logical torrent groups and add torrents to them. It also allows you to manually reorder items in
	the list if needed. This screen allows you to do that.<BR><BR>

	<H2>Concepts</H2>
	
	The tracker is set up so that torrents belong in virtual groups. These groups are defined by the user, and the user can group and
	ungroup torrents. Torrents that do not belong in a group are considered orphaned torrents. These torrents will always display at the 
	top of the statistic pages and the sorting screen.<BR><BR>
	
	You can manually order torrents in groups. You can also manually sort the group headings (neither are required to be in alphabetical
	order, those auto sort links are just for convenience.) There is the odd time when sorting by filename has really odd results and thus
	manual intervention is required.
	
	<BR><BR>
	
	<H2>How do I?</H2>
	
	<B>Add a group</B>: Above the torrent table, enter the new group name and click the <I>Add...</I> button. <FONT COLOR=RED>NOTE: A group will NOT display until a torrent has been added to it!</FONT><BR><BR>
	
	<B>Edit a group name</B>: Scroll down to the group you want to edit, change the name in the editbox, and click the <I>Process selected torrents...</I> button.<BR><BR>

	<B>Delete a group</B>: Scroll down the the group you want to remove and click the <I>Delete</I> hyperlink under the editbox. NOTE: All torrents are ungrouped then the group is removed.<BR><BR>
	
	<B>Group a torrent</B>: Choose <I>Group...</I> in the dropdown next to an orphaned torrent in the list, and click the <I>Process selected torrents...</I> button.<BR><BR>
	
	<B>Ungroup a torrent</B>: Choose <I>Ungroup</I> in the dropdown next to a torrent in an existing group, and click the <I>Process selected torrents...</I> button.<BR><BR>
	
	<B>Sort a group by name</B>: Scroll down and locate the group to be sorted and click the <I>Auto sort group by torrent name</I> hyperlink below the editbox.<BR><BR>
	
	<B>Automatically sort torrents and groups</B>: At the top there are different options for doing this. You can choose to sort the torrents only, both torrents and group headings, or just the group headings only. NOTE: Root users can resort <I>everything</I> from all Categories active on the tracker so be careful which link you are using!<BR><BR>
	
	<B>Manually move a group heading</B>: Locate the group and click the <I>Move...</I> hyperlink below the editbox. The tracker will ask where you wish to move the group to.<BR><BR>
	
	<B>Manually move a torrent within a group</B>: Locate the torrent and click the <I>Move...</I> hyperlink to the right of the torrent name. The tracker will ask you where to move the torrent to.<BR><BR>
</BODY>
</HTML>