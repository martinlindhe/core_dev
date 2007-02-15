<?php
	/*
	 * Module:	maintenance.php
	 * Description: This file checks automatic bans that expire and removes
	 * 		them if they are no longer needed. (i.e. the current day is
	 *			after the expiry date.)
	 *
	 *       NOTE: This needs to be called from crontab or something at
	 * 				regular intervals. Daily or weekly would be fine.
	 *
	 * Author:	danomac
	 * Written:	13-Oct-2004
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
	require_once ("config.php");
	require_once ("funcsv2.php");

	/*
	 * Log to file? This is really simply logging, basically success or not.
	 */
	$mntLog = false;
	$mntLogFile = "/dev/null";

	$currentTime = date("Y-m-d H:i:s");

	/*
	 * Make a connection to the database
	 */
	if ($GLOBALS["persist"]) {
		$db = @mysql_pconnect($dbhost, $dbuser, $dbpass);
		if (!$db) {
			echo "Script failed. - cannot make a persistent connection to database server. " . mysql_error();
			if ($mntLog) error_log("$currentTime - Can't make persistent connection to database server.\r\n", 3, $mntLogFile);
			exit;
		}
	} else {
		$db = @mysql_connect($dbhost, $dbuser, $dbpass);
		if (!$db) {
			echo "Script failed. - cannot make a connection to database server. " . mysql_error();
			if ($mntLog) error_log("$currentTime - Can't make connection to database server.\r\n", 3, $mntLogFile);
			exit;
		}
	}

	/*
	 * Open the database needed
	 */
	$dbselresult = @mysql_select_db($database);
	if (!$dbselresult) {
		echo "Script failed. - cannot open database required. " . mysql_error();
		if ($mntLog) error_log("$currentTime - Can't open database required.\r\n", 3, $mntLogFile);
		exit;
	}

	/*
	 * Remove the bans that are no longer needed.
	 */
	$recordset = @mysql_query("DELETE FROM ipbans WHERE banautoexpires=\"Y\" AND CURDATE() > banexpiry");

	if ($mntLog) error_log("$currentTime - ". mysql_affected_rows() . " outdated bans have been removed.\r\n", 3, $mntLogFile);

	/*
	 * Run a consistency check
	 */
	consistencyCheck(false, true);
	if ($mntLog) error_log("$currentTime - Ran consistency check.\r\n", 3, $mntLogFile);
?>