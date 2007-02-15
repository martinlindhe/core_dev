<?php
	/*
	 * Module:	bta_logout.php
	 * Description: This is the script that logs out of the Administrative interface
	 * 		and destroys session data.
	 *
	 * Author:	danomac
	 * Written:	14-Febrary-2004
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
	 * There are some variables defined in this script that are needed, such as the
	 * phpbttracker version strings.
	 */
	require_once("bta_funcs.php");

	/*
	 * Let's try to stay HTML 4.01 compliant.
	 */
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n";

	/*
	 * Let's see if the bta_login.php page was used by checking to see if there
	 * is a session variable set with the page referrer.
	 * If yes, then destroy the session and log out.
	 */
	if (!isset($_SESSION['authenticated'])) {
		admShowError("You are not logged in.", 
			     "Common sense states that before you attempt to log out, you should be logged in to the interface first!",
			     $adm_pageerr_title);
	} else {
		//okay, destroy the session.
		admKillSession();
	
		//ensure the session was destroyed, and display a message
		if (!isset($_SESSION['authenticated'])) {
			admShowMsg("You are now logged off.", "You will need to logon again to use the Administrative interface.",
				       $adm_page_title . " - Logout");
		} else {
			admShowError("ERROR: You are not logged off.",
				     "Could not logoff for an unknown reason.",
				     $adm_pageerr_title);
		}
	}
?>