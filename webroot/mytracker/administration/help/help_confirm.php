<?php

	/*
	 * Module:	help_confirm.php
	 * Description: This module displays help for the main confirmation screen.
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
	<?php echo "<TITLE>Confirm action help - $phpbttracker_id $phpbttracker_ver</TITLE>\r\n"; ?>
</HEAD>
<BODY CLASS="help">
	<P CLASS="help_title"><?php echo "$phpbttracker_id $phpbttracker_ver - Confirm action help"; ?></P>
	
	This page will show the items that were selected on the main administration page. It is broken up in several sections:<BR><BR>
	<ol>
	<li>Unhide torrents</li>
	<li>Hide torrents</li>
	<li>Retire torrents</li>
	<li>Delete torrents</li>
	<li>Revive torrents</li>
	<li>Remove retired torrents</li>
	</ol>
	<BR>
	This will show you the torrent name and current stats on the torrent. Ensure that the selections made are correct,
	then click the <B>Confirm and process</B> button, or if there are mistakes, click the <B>Return to Main Administrative screen</B> 
	hyperlink to go back to the main page without making any changes.
</BODY>
</HTML>