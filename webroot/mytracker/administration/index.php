<?php
	/*
	 * Module:	index.php
	 * Description: This is the gateway script that forwards to a JavaScript
	 * 		enabled script if it is detected. JavaScript is required
	 *		to logon to the administrative interface.
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
	 * Include the version of the tracker and the admin functions
	 */
	require_once("../version.php");
	require_once("bta_funcs.php");

	/*
	 * Manually set the refering page
	 */
	$_SESSION['refering_page'] = $_SERVER['PHP_SELF'];

	/*
	 * If the admin username and password are not set, terminate
	 */
	if (!isset($admin_user) || !isset($admin_pass) || strlen($admin_user) == 0 || strlen($admin_pass) == 0) {
		admShowError("Administration root username and/or password not set",
			     "The administration system will not function until you set these in the configuration.",
			     $adm_pageerr_title);
		exit;
	}

	/*
	 * Let's try to stay HTML 4.01 compliant.
	 */
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n";

	echo "<HTML>\r\n<HEAD>\r\n<TITLE>".$phpbttracker_id." ".$phpbttracker_ver." Login Gateway</TITLE>\r\n";
	echo "<META NAME=\"Author\" CONTENT=\"danomac\">\r\n";
	echo "<LINK REL=\"stylesheet\" HREF=\"admin.css\" TYPE=\"text/css\" TITLE=\"Default\">\r\n";
	echo "<SCRIPT LANGUAGE=\"JavaScript\" TYPE=\"text/javascript\">\r\n<!--\r\nwindow.location=\"bta_jslogin.php\";\r\n// -->\r\n</SCRIPT>\r\n</HEAD>\r\n";
?>
<BODY>
<P CLASS="adm_title">You need Javascript to use this logon gateway.</P>
<CENTER>
If you are seeing this message your browser is not JavaScript-enabled.
Javascript is required to use this gateway logon page.<BR>Install JavaScript, and reload this page and you will be redirected to
a JavaScript logon page.
</CENTER>
</BODY>
</HTML>
