<?php

	/*
	 * Module:	bta_do.php
	 * Description: This "switches" between showing peers and editing a torrent.
	 * 		The module stores the hash in a session variable, then redirects
	 * 		to the appropriate page.
	 *
	 * Author:	danomac
	 * Written:	17-March-2004
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
	 * Get the current script name. Used to build HREF strings later on.
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
	 * Set to true if an error is found. Uses to skip processing if an error is found.
	 */
	$redirError = false;

	/*
	 * Check to see if action parameter is set. It's needed to figure out where we are redirecting to.
	 * If there is no parameter it is a soft error and a redirect should be taken back to the main page.
	 */
	if (isset($_GET['action'])) {
		if (strpos($_GET["action"], " ") !== false) {
			admShowMsg("Invalid action parameter", "An invalid action value was passed.", "Invalid action request", true, "bta_main.php", 5);
			exit;
		}
		
		$requestedaction = $_GET['action'];
	} else {
		$redirURL = "bta_main.php";
		$redirDelay = 5;
		$redirMSG = "Invalid parameter.";
		$redirDetail = "No action specified.";
		$redirError = true;
	}

	/*
	 * Get the info has needed, and put it in a session variable, if there were no previous errors
	 * If there is no hash specified it is a soft error and a redirect should be taken back to the main page.
	 */
	if (!$redirError) {
		if (isset($_GET['info_hash'])) {
			if (strpos($_GET["info_hash"], " ") !== false || strlen($_GET["info_hash"]) != 40) {
				admShowMsg("Invalid hash parameter", "An invalid hash value was passed.", "Invalid hash value", true, "bta_main.php", 5);
				exit;
			}
		
			$_SESSION['info_hash'] = $_GET['info_hash'];
		} else {
			$redirDelay = 5;
			$redirError = true;
			$redirMSG = "Invalid parameter.";
			$redirDetail = "Info hash not specified.";
			$redirURL = "bta_main.php";
		}
	}

	/*
	 * This switch statment checks the requested action and sets up redirects.
	 * If the action isn't valid, it's considered a "soft" error and a redirect goes to the main page.
	 * Only parse the action variable if an error was not discovered earlier.
	 */
	if (!$redirError) {
		switch ($requestedaction) {
			case "edit": 
				$redirDelay = 0;
				$redirError = true;
				$redirMSG = "Processing.";
				$redirDetail = "Redirecting...";
				$redirURL = "bta_edit.php";
				break;
			case "peerinfo":
				$redirDelay = 0;
				$redirMSG = "Processing.";
				$redirDetail = "Redirecting...";
				$redirURL = "bta_peers.php";
				break;
			default:
				$redirDelay = 5;
				$redirError = true;
				$redirMSG = "Invalid parameter.";
				$redirDetail = "Action specified is not valid.";
				$redirURL = "bta_main.php";
		}
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
	echo "<TITLE>". $adm_page_title . " - Processing</TITLE>\r\n";
	
	/*
	 * Redirect.
	 */
	echo "<META http-equiv=\"refresh\" content=\"$redirDelay;URL=$redirURL\">\r\n";
?>
</HEAD>

<BODY>
<?php
	echo "<P CLASS=\"adm_title\">$redirMSG</P><CENTER>$redirDetail</CENTER>";
	echo "<BR<BR><CENTER>If the browser doesn't automatically redirect <A HREF=\"$redirURL\">click here.</A></CENTER>\r\n";
?>
</BODY>
</HTML>
