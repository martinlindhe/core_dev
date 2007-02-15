<?php
	/*
	 * Module:	bta_login_check.php
	 * Description: This is the script that verifies the hashed user/pass
	 * 		against those in config.php.
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
	 * There are some variables defined in these scripts that are needed, such
	 * as the database user/password, and phpbttracker version strings.
	 */
	require_once("../version.php");
	require_once("bta_funcs.php");

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

	/*
	 * We are going to check and see if bta_login was used to start the session.
	 * If not, display an error. Otherwise, make sure that this is accessed only
	 * from bta_jslogin.php.
	 */
	if (!isset($_SESSION['refering_page'])) {
		admShowError("You can't access this page directly. Use bta_login.php to login to the administrative interface.",
			     "This page is never called directly from a web browser.",
			     $adm_pageerr_title);
		exit;
	} else {
		/*
		 * Let's check to see if the referring page is indeed correct
		 * This is rather redundant. I know.
		 */
		$refererArray = explode("/", $_SESSION['refering_page']);
		$refererCount = count($refererArray);

		if ($refererArray[$refererCount-1] != "bta_jslogin.php") {
			admShowError("You have to use admin/index.php to login to the administrative interface.",
				     "If you are trying to access this file from another page you may get this error. Use bta_login.php to login to the administrative interface.",
				     $adm_pageerr_title);
			exit;
		}		
	}

	/*
	 * Check to make sure that the ID from mysql exists. If it doesn't, error out.
	 */
	if (!isset($_POST["id"])) {
		admShowError("You have to use bta_login.php to login to the administrative interface.",
			     "If you are trying to access this file from another page you may get this error. Use bta_login.php to login to the administrative interface.",
			     $adm_pageerr_title);
		exit;
	}

	/*
	 * Make sure that the IP address matches the IP this session was started with, if it doesn't someone might be
	 * trying to steal the session.
	 */
	$browserIP = str_replace("::ffff:", "", $_SERVER["REMOTE_ADDR"]);
	if ($_SESSION["clientIP"] != $browserIP)
		admShowError("There was a problem with your request.",
			     "It appears that you are trying to use a session that isn't yours. Shame on you!",
			     $adm_pageerr_title);

	/*
	 * Check to make sure a username was entered. If not, error out.
	 * This is a two-stage test: A check is made to see if the form sent
	 * the data, and secondly, if it did, it is checked to ensure it isn't
	 * a zero-length string.
	 */	
	if (!isset($_POST["usermd5"])) {
		admShowError("You have to enter a username.",
			     "If you have entered a username and are getting this message, it is likely that JavaScript is not enabled on your machine.",
			     $adm_pageerr_title);
		exit;
	} else {
		if (strlen($_POST["usermd5"]) == 0) {
			admShowError("You have to enter a username.",
				     "If you have entered a username and are getting this message, it is likely that JavaScript is not enabled on your machine.",
				     $adm_pageerr_title);
			exit;
		}
	}

	/*
	 * Check to make sure a password was entered. If not, error out.
	 * This is a two-stage test: A check is made to see if the form sent
	 * the data, and secondly, if it did, it is checked to ensure it isn't
	 * a zero-length string.
	 */	
	if (!isset($_POST["passmd5"])) {
		admShowError("You have to enter a password.",
			     "If you have entered a password and are getting this message, it is likely that JavaScript is not enabled on your machine.",
			     $adm_pageerr_title);
		exit;
	} else {
		if (strlen($_POST["passmd5"]) == 0) {
			admShowError("You have to enter a password.",
				     "If you have entered a password and are getting this message, it is likely that JavaScript is not enabled on your machine.",
				     $adm_pageerr_title);
			exit;
		}
	}		


	/*
	 * Finally, connect to the database and check to see if the hashes match.
	 * This will show an error if there was an error connecting to the database.
	 */
	if ($GLOBALS["persist"])
		$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	else
		$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");

	if (!mysql_select_db($database)) {
		admShowError("Can't connect to the database.",
			     "Database reported this error: " . mysql_error() . ".",
			     $adm_pageerr_title);
		exit;
	} else {
		/*
		 * Load the record from the logins table and make sure IP address matches
		 */
		$recordset = mysql_query("SELECT ipaddr FROM logins WHERE id = '$_POST[id]'") or admShowError("Can't run query.", "mysql reported this error: " . mysql_error(), $adm_pageerr_title);
		$row = mysql_fetch_row($recordset);

		if ($row[0] == $browserIP) {

			/*
			 * Mark this key as used in the logins table.
			 */
			mysql_query("UPDATE logins SET used = 1 WHERE id = '$_POST[id]'");
			if (!mysql_affected_rows()) {
				admShowError("There was an error processing your request.",
					     "Someone attempted a login already with your session.",
					     $adm_pageerr_title);	
				exit;
			}

			/*
			 * Check to see if the hashes match for the username
			 */
			if (hmac_md5($_POST["id"], $admin_user) != $_POST["usermd5"] ) {
				/*
				 * If group admin is enabled, check the database
				 */
				if ($GLOBALS["allow_group_admin"]) {
					/*
					 * If the username doesn't match the root password, query the database
					 * for a list of usernames allowed
					 */
					$query = "SELECT username,
										password,
										category,
										comment,
										perm_add,
										perm_addext,
										perm_edit,
										perm_delete,
										perm_retire,
										perm_unhide,
										perm_peers,
										perm_viewconf,
										perm_retiredmgmt,
										perm_ipban,
										perm_usermgmt,
										perm_mirror,
										enabled,
										disable_reason,
										perm_advsort FROM adminusers";

					$recordset = mysql_query($query);

					/*
					 * If no records, then obviously the person is not allowed
					 */
					if (mysql_num_rows($recordset) == 0) {
						admShowError("Invalid username/password",
							     "Check your username and password and try again.",
						   	  $adm_pageerr_title);
						exit;
					}
					
					/*
					 * Loop through this structure, checking to see if a username is found
					 */
					$userfound = false;
					while ($row=mysql_fetch_row($recordset)) {
						if (hmac_md5($_POST["id"], $row[0]) == $_POST["usermd5"]) {
							if (hmac_md5($_POST["id"], $row[1]) == $_POST["passmd5"]) {
								/*
								 * Gonna do a username/category validity check here.
								 */
								if (strpos($row[0], " ") !== false) {
									admShowError("Internal error",
									     "Your username appears to be invalid, please contact the tracker administrator.<BR><BR>Detail: spaces are not allowed in user names.",
									     $adm_pageerr_title);
									exit;
								}

								if (strpos($row[2], " ") !== false) {
									admShowError("Internal error",
									     "Please contact the tracker administrator.<BR><BR>Detail: spaces are not allowed in category names.",
									     $adm_pageerr_title);
									exit;
								}

								if ($row[16]=="N") {
									admShowError("Your account is disabled.",
									     "Please contact the tracker administrator.<BR><BR>Reason: $row[17].",
									     $adm_pageerr_title);
									exit;
								}

								/*
								 * User found, password matches!
								 */
								$userfound = true;
								$_SESSION["admin_perms"]["user"] = $row[0];

								/*
								 * Set the appropriate permissions in the session variable
								 */
								$_SESSION["admin_perms"]["root"] = false;
								$_SESSION["admin_perms"]["category"] = $row[2];
								$_SESSION["admin_perms"]["comment"] = $row[3];
								if ($row[4] == 'Y') $_SESSION["admin_perms"]["add"] = true; else $_SESSION["admin_perms"]["add"] = false;
								if ($row[5] == 'Y') $_SESSION["admin_perms"]["addext"] = true; else $_SESSION["admin_perms"]["addext"] = false;
								if ($row[15] == 'Y') $_SESSION["admin_perms"]["addmirror"] = true; else $_SESSION["admin_perms"]["addmirror"] = false;
								if ($row[6] == 'Y') $_SESSION["admin_perms"]["edit"] = true; else $_SESSION["admin_perms"]["edit"] = false;
								if ($row[7] == 'Y') $_SESSION["admin_perms"]["delete"] = true; else $_SESSION["admin_perms"]["delete"] = false;
								if ($row[8] == 'Y') $_SESSION["admin_perms"]["retire"] = true; else $_SESSION["admin_perms"]["retire"] = false;
								if ($row[9] == 'Y') $_SESSION["admin_perms"]["unhide"] = true; else $_SESSION["admin_perms"]["unhide"] = false;
								if ($row[10] == 'Y') $_SESSION["admin_perms"]["peers"] = true; else $_SESSION["admin_perms"]["peers"] = false;
								if ($row[11] == 'Y') $_SESSION["admin_perms"]["viewconf"] = true; else $_SESSION["admin_perms"]["viewconf"] = false;
								if ($row[12] == 'Y') $_SESSION["admin_perms"]["retiredmgmt"] = true; else $_SESSION["admin_perms"]["retiredmgmt"] = false;
								if ($row[13] == 'Y') $_SESSION["admin_perms"]["ipban"] = true; else $_SESSION["admin_perms"]["ipban"] = false;
								if ($row[14] == 'Y') $_SESSION["admin_perms"]["usermgmt"] = true; else $_SESSION["admin_perms"]["usermgmt"] = false;
								if ($row[18] == 'Y') $_SESSION["admin_perms"]["advsort"] = true; else $_SESSION["admin_perms"]["advsort"] = false;
								break;
							} else {
								/*
								 * User found, but password didn't match
								 */
								admShowError("Invalid username/password",
								     "Check your username and password and try again.",
								     $adm_pageerr_title);
								exit;
							}
						}	
					}

					if (!$userfound) {
						admShowError("Invalid username/password",
						     "Check your username and password and try again.",
						     $adm_pageerr_title);
						exit;
					}
				} else {
					/*
					 * Group administration is not enabled, terminate
					 */
					admShowError("Invalid username/password",
					     "Check your username and password and try again.",
					     $adm_pageerr_title);
					exit;
				}
			} else {
				/*
				 * The username entered matches the "root" password
				 * Check to see if the hashes match for the password
				 */
				if (hmac_md5($_POST["id"], md5($admin_pass)) != $_POST["passmd5"]) {
					admShowError("Invalid username/password",
						     "Check your username and password and try again.",
					   	  $adm_pageerr_title);
					exit;
				}

				/* 
				 * Okay the root password matches, now set the permission variable
				 */
				$_SESSION["admin_perms"]["root"] = true;
			}
		} else
			admShowError("There was a problem processing your request",
				     "It appears you are trying to steal a session. Shame on you!",
				     $adm_pageerr_title);
	}


	/*
	 * Wow. All the tests pass. There should be a variable set in _SESSION now to verify
	 * that the login was successful. Also, we can now redirect to the "main" administration panel.
	 */
	$_SESSION['refering_page'] = "";
	$_SESSION['authenticated'] = true;

	admShowMsg("Authenticated.", "Redirecting to the main administration panel.",
		       $adm_page_title, true, "bta_main.php");
?>