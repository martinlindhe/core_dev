<?php
	/*
	 * Module:	bta_uconfirm.php
	 * Description: This is the confirm operation screen for the user administrative interface.
	 * 		This module displays selected users from the user administration screen and asks the
	 * 		user to confirm the operation.
	 *
	 * Author:	danomac
	 * Written:	6-June-2004
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
	require_once ("../funcsv2.php");
	require_once ("../version.php");
	require_once ("bta_funcs.php");

	/*
	 * Get the current script name.
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
	 * Group admin: are they actually allowed to view this page?
	 * If not, redirect them back to main
	 */
	if (!($_SESSION["admin_perms"]["usermgmt"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Redirecting to the main administration panel.",
			       $adm_page_title, true, "bta_main.php", 3);
	}

	/*
	 * If the button to confirm everything was pressed, process
	 * and return to the main page.
	 */
	if (isset($_POST["confirmation"])) {
		/*
		 * Connect to the database
		 */
		if ($GLOBALS["persist"])
			$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		else
			$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		mysql_select_db($database) or sqlErr(mysql_error());

		/*
		 * Process delete list, if some were selected
		 */
		if (isset($_SESSION["udeletelist"])) {
			/*
			 * Go through each value and remove the data and tables
			 */
			foreach ($_SESSION["udeletelist"] as $key => $value) {
				@mysql_query("DELETE FROM adminusers WHERE username=\"".$_SESSION["udeletelist"][$key]["username"]."\"");
			}
			/*
			 * Destroy the variable
			 */
			unset($_SESSION["udeletelist"]);
		}

		admShowMsg("Changes applied.","Redirecting to user administration page.","Redirecting", true, "bta_usermgmt.php", 3);
		exit;
	}

	/*
	 * Check to make sure something was actually selected.
	 */	
	if (!isset($_POST["uprocess"])) {
		admShowMsg("Nothing selected.","Redirecting to user administration page.","Redirecting", true, "bta_usermgmt.php", 3);
		exit;
	} else
		$processlist = $_POST["uprocess"];

	/*
	 * Connect to the database
	 */
	if ($GLOBALS["persist"])
		$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	else
		$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	mysql_select_db($database) or sqlErr(mysql_error());

	if (isset($processlist)) {
		foreach ($processlist as $username => $status) {
			/*
			 * Check to make sure the checkbox was enabled.
			 */
			if (strcmp($status, "enabled")==0) {
				/*
				 * Build query string
				 */
				$query = "SELECT username, 
					category, 
					comment FROM adminusers WHERE username = \"$username\"";

				/*
				 * Do the query, get the row...
				 */
				$recordset = mysql_query($query) or sqlErr(mysql_error());		
				$row=mysql_fetch_row($recordset);
	
				$deletelist[] = array('username' => $username,
											'category' => $row[1],
											'comment' => $row[2]);
			}
		}
	}

	/*
	 * Okay, they are seperated, now sort the lists,
	 * and put them in a session variable for later use.
	 * First, unset the session variables, in case the screen has been
	 * used previously in the same session.
	 */
	unset($_SESSION['udeletelist']);
	if (count($deletelist) > 0)
		$_SESSION['udeletelist'] = array_sort($deletelist, "username");
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
	echo "<TITLE>". $adm_page_title . " - Confirm selections</TITLE>\r\n";
?>
</HEAD>

<BODY>
<FORM ENCTYPE="multipart/form-data" METHOD="POST" ACTION="bta_uconfirm.php">
<TABLE CLASS="tblAdminOuter">
<TR>
<?php
	/*
	 * Display the page heading.
	 */
	echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>Confirm selections</TD>\r\n";
?>
</TR>
<?php admShowURL_Login($ip); ?>
<TR>
	<TD CLASS="data" COLSPAN=15 ALIGN="center"><BR>
	   <A HREF="help/help_confirm_user.php" TARGET="_blank">Need help?</A><BR>
	</TD>
</TR>
<TR>
<?php
	echo "\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_usermgmt.php\">Return to User Administrative screen (no changes will be made).</A><BR>&nbsp;</TD>\r\n";
?>
</TR>
<TR>
	<TD COLSPAN=15>
		<FONT SIZE=+2>You have elected to:</FONT><BR><BR>
<?php
	/*
	 * These are the alternating Cascadying Style Sheet classes used for the data.
	 */
	$classRowBGClr[0] = 'CLASS="odd"';
	$classRowBGClr[1] = 'CLASS="even"';

	if (isset($_SESSION["udeletelist"])) {
		echo "\t\t<FONT SIZE=+2><B>DELETE the following:</B></FONT><BR>";
		echo "\t\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>Username</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>Category</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>Comment</B></TD>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Use to alternate rows.
		 */
		$rowCount = 0;

		/*
		 * Spit out the contents stored in the session variable.
		 */
		foreach ($_SESSION["udeletelist"] as $key => $value) {
			$useRowClass = $classRowBGClr[$rowCount % 2];
			echo "\t\t\t<TD $useRowClass>".$_SESSION["udeletelist"][$key]["username"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass>".$_SESSION["udeletelist"][$key]["category"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["udeletelist"][$key]["comment"]."</TD>\r\n";
			echo "\t\t</TR>";

			$rowCount++;
		}
		echo "\t\t</TABLE><BR><BR>\r\n";
	}
?>
	</TD>
</TR>
<TR>
<?php
	echo "\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_usermgmt.php\">Return to User Administrative screen (no changes will be made).</A><BR>&nbsp;</TD>\r\n";
?>
</TR>
<TR>
	<TD COLSPAN=15 ALIGN="center">If the information above is correct, click the <I>Confirm and process</I> button below to proceed.</TD>
</TR>
<TR>
	<TD COLSPAN=15 ALIGN="center"><INPUT TYPE="submit" NAME="confirmation" VALUE="Confirm and process" CLASS="button"></TD>
</TR>
</TABLE>
</FORM>
</BODY>
</HTML>