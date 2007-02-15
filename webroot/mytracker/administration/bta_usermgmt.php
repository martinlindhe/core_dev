<?php

	/*
	 * Module:	bta_usermgmt.php
	 * Description: This is the main screen of the user administrative interface.
	 * 		It lists all the users and allows you to manage them.
	 *
	 * Author:	danomac
	 * Written:	06-June-2004
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
	 * Order to use when not specified
	 */
	$defaultorder = "category";

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
	 * Group admin: are they actually allowed to view this page?
	 * If not, redirect them back to main
	 */
	if (!($_SESSION["admin_perms"]["usermgmt"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Redirecting to the main administration panel.",
			       $adm_page_title, true, "bta_main.php", 3);
	}

	/*
	 * Get the order from the URL, if one is specified.
	 * If not specified, assume default ordering scheme.
	 */
	if (isset($_GET["order"])) {
		if (strpos($_GET["order"], " ") !== false) {
			admShowMsg("Invalid order parameter", "An invalid order value was passed.", "Invalid order request", true, "bta_usermgmt.php", 5);
		}

		$requestedorder = $_GET["order"];
		$defaultorderset = false;
	}
	else {
		$requestedorder = $defaultorder;
		$defaultorderset = true;
	}

	/*
	 * Get the sort direction from the URL, if one exists.
	 * If not specified, assume ascending order.
	 */
	if (isset($_GET["sort"])) {
		$requestedsort = $_GET["sort"];

		if (strpos($_GET["sort"], " ") !== false) {
			admShowMsg("Invalid sort parameter", "An invalid sort value was passed.", "Invalid sort request", true, "bta_usermgmt.php", 5);
		}
	
		switch ($requestedsort) {
			case "ascending":
				$sortorder = " ";
				$sortascending = true;
				break;
		case "descending":
			$sortorder = " DESC ";
			$sortascending = false;
			break;
		default:
			$sortorder = " ";
			$sortascending = true;
		}
	} else {
		$sortorder = " ";
		$sortascending = true;
	}

	/*
	 * Build the hyperlinks needed to specify a sort in the heading.
	 */
	$usernamehref = $scriptname . "?order=username";
	$commenthref = $scriptname . "?order=comment";
	$categoryhref = $scriptname . "?order=category";

	/*
	 * This switch statments checks which is the CURRENT sort order,
	 * and specifies an option to REVERSE the current sort order.
	 * e.g. If sorting by name in ASCENDING order, the hyperlink will add 
	 *      a parameter to sort the name in DESCENDING order.
	 */
	switch ($requestedorder) {
		case "username": 
			$order = " ORDER BY username " . $sortorder;
			if ($sortascending) $usernamehref = $usernamehref . "&amp;sort=descending"; else $usernamehref = $usernamehref . "&amp;sort=ascending";
			break;
		case "comment":
			$order = " ORDER BY comment " . $sortorder;
			if ($sortascending) $commenthref = $commenthref . "&amp;sort=descending"; else $commenthref = $commenthref . "&amp;sort=ascending";
			break;
		case "category":
			$order = " ORDER BY category " . $sortorder . ", username ";
			if ($sortascending) $categoryhref = $categoryhref . "&amp;sort=descending"; else $categoryhref = $categoryhref . "&amp;sort=ascending";
			break;
		default:
			$order = " ";
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
	echo "<TITLE>". $adm_page_title . " - User Management</TITLE>\r\n";
?>
</HEAD>

<BODY>
<FORM ACTION="bta_uconfirm.php" METHOD=POST>

<TABLE CLASS="tblAdminOuter">
<TR>
<?php
	/*
	 * Display the page heading.
	 */
	echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>User Management</TD>\r\n";
?>
</TR>
<?php 
	admShowURL_Login($ip);
	if ($_SESSION["admin_perms"]["viewconf"] || $_SESSION["admin_perms"]["root"])
		echo "<TR>\r\n\t<TD CLASS=\"data\" ALIGN=\"center\" COLSPAN=15>\r\n\t\t<A HREF=\"bta_configuration.php\" TARGET=\"_blank\">Click here for tracker configuration details</A><HR></TD>\r\n</TR>\r\n";
?>
<TR>
	<TD CLASS="data" COLSPAN=15 ALIGN="center"><BR>
		Welcome to the user management screen. Up above you will see hyperlinks that will take you to various parts of the administration interface. Below is a list of users allowed to login to the system.<BR>
	   <A HREF="help/help_user_management.php" TARGET="_blank">Need help?</A><BR>
	</TD>
</TR>
<TR>
	<TD CLASS="data" COLSPAN=15>&nbsp;</TD>
</TR>
<TR>
	<TD ALIGN="center" COLSPAN=15><A HREF="bta_uadd.php">Add a new user</A><BR><BR></TD>
</TR>
<TR>
	<TD ALIGN="center" COLSPAN=15><A HREF="bta_main.php">Return to main administration panel</A></TD>
</TR>
<?php
	/*
	 * Connect to the database
	 */
	if ($GLOBALS["persist"])
		$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	else
		$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	mysql_select_db($database) or sqlErr(mysql_error());

	echo "<TR>\r\n\t<TD COLSPAN=15>\r\n\t\t\t<TABLE CLASS=\"tblAdminOuter\">";
 
	echo "\t\t<TR>\r\n\t\t\t<TD ALIGN=RIGHT COLSPAN=2>\r\n\t\t\t\t<TABLE CLASS=\"tblAdminInner\" cellpadding=\"5\" cellspacing=\"1\">\r\n";

	/*
	 * Output the column headers.
	 */
	echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD COLSPAN=20 CLASS=\"sortheading\">To sort the data, click on the column header hyperlinks.</TD>\r\n\t\t\t\t</TR>\r\n";
	echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"left\" VALIGN=\"bottom\"><A HREF=\"$categoryhref\">Category</A></TD>\r\n";
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"left\" VALIGN=\"bottom\"><A HREF=\"$usernamehref\">Username</A></TD>\r\n";
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Password</TD>\r\n";
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\"><A HREF=\"$commenthref\">Comment</A></TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Enabled</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Add</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Add External<BR>Torrent</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Add Mirror<BR>Torrent</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Edit</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Delete</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Retire</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Unhide</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Manage<BR>Peers</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">View<BR>Config</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Retired Torrent<BR>Management</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">IP<BR>Banning</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">User<BR>Management</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Advanced<BR>Sorting</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Edit</TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Delete?</TD>\r\n"; 
	echo "\r\n\t\t\t\t</TR>";

	/*
	 * These are the alternating Cascadying Style Sheet classes used for the data.
	 */
	$classRowBGClr[0] = 'CLASS="odd"';
	$classRowBGClr[1] = 'CLASS="even"';

	/*
	 * Build a query string of information we need to get from the database
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
						enabled,
						perm_mirror,
						perm_advsort FROM adminusers $order";

	/*
	 * Get a recordset of the torrents...
	 */
	$recordset = mysql_query($query) or sqlErr(mysql_error());

	/*
	 * Let's keep track of totals stats, for the summary line, so we need to initialize a few variables
	 */
	$totalusers = 0;

	/*
	 * let's set anchors for the various categories
	 * to do this we need to know 1. what the last category was and
	 * 2. if we are sorting by category, otherwise it's useless
	 */
	$lastCategory = "";

	/*
	 * Let's parse through the recordset, and show the information.
	 */
	while ($row=mysql_fetch_row($recordset)) {
		/*
		 * Let's set a variable with the current BT hash
		 */
		$currentUser = $row[0];

		/*
		 * Start a table row.
		 */
		echo "\t\t\t\t<TR>\n";

		/*
		 * Okay, do a modulus (%) and figure out whether this is an EVEN or ODD row
		 * then put the information in a variable so we have the right cell class.
		 */
		$cellBG = $classRowBGClr[$totaltorrents % 2];

		/*
		 * Category
		 */
		echo "\t\t\t\t\t<TD $cellBG>";
		echo $row[2];
		echo "</TD>\r\n";

		/*
		 * Username
		 */
		echo "\t\t\t\t\t<TD $cellBG><b>$row[0]</b></TD>\r\n";

		/*
		 * Password
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\"><i>Encrypted</i></TD>\r\n";

		/*
		 * Comment
		 */
		echo "\t\t\t\t\t<TD $cellBG>$row[3]</TD>\r\n";

		/*
		 * Enabled?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[15] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";

		/*
		 * Add permission?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[4] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";

		/*
		 * Add external torrent permission?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[5] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";

		/*
		 * Add mirror torrent permission?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[16] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";

		/*
		 * Edit permission?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[6] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";

		/*
		 * Delete permission?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[7] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";

		/*
		 * Retire permission?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[8] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";

		/*
		 * Unhide permission?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[9] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";

		/*
		 * View peers permission?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[10] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";

		/*
		 * View config permission?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[11] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";

		/*
		 * Retired torrent management permission?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[12] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";

		/*
		 * IP banning permission?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[13] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";

		/*
		 * User management permission?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[14] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";

		/*
		 * Advanced sort permission?
		 */
		echo "\t\t\t\t\t<TD $cellBG align=\"center\">";
		if ($row[17] == 'Y')
			echo "Yes";
		echo "</TD>\r\n";
		
		/*
		 * Edit user hyperlink
		 */
		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><A HREF=\"bta_uedit.php?username=$currentUser\">Edit</A></TD>\r\n";

		/*
		 * Delete user radio button
		 */
		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><INPUT TYPE=checkbox NAME=\"uprocess[$currentUser]\" VALUE=\"enabled\"></TD>\r\n";

		echo "\t\t\t\t</TR>\r\n";

		/*
		 * Keep a running total for the stats line... so increment them.
		 */
		$totalusers++;
	}

	/*
	 * If there were no torrents, display a 'No active torrents' message, otherwise
	 * show a summary line.
	 */
	if ($totalusers == 0)
		echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD $classRowBGClr[0] COLSPAN=21 ALIGN=CENTER>No active users</TD>\r\n\t\t\t\t</TR>";
	else {
		/*
		 * Calculate for odd/even row
		 */
		$cellBG = $classRowBGClr[$totalusers % 2];

		/*
		 * Total amount of users displayed on the page.
		 */
		echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD $cellBG ALIGN=\"center\" COLSPAN=20><b>$totalusers user(s) active</b>.</TD>\r\n\t\t\t\t</TR>\r\n";
	}

	echo "\t\t\t\t</TABLE>\r\n\t\t\t</TD>\r\n\t\t</TR>\r\n";

	// -- Summary at bottom of the torrent table. --
	echo "\t\t<TR>\r\n\t\t\t<TD ALIGN=LEFT CLASS=\"summary\">". $adm_page_title ." using MySQL.</TD>\r\n";
	echo "\t\t\t<TD ALIGN=RIGHT CLASS=\"summary\">&nbsp;</TD>\r\n\t\t</TR>\r\n";

	echo "\t\t<TR>\r\n\t\t\t<TD>&nbsp</TD>\r\n\t\t</TR>\r\n";
	echo "\t\t<TR>\r\n\t\t\t<TD ALIGN=\"center\"><INPUT TYPE=\"submit\" NAME=\"processbutton\" VALUE=\"Delete selected users...\" CLASS=\"button\"></TD>\r\n";
	echo "\t\t\t<TD ALIGN=\"center\"><INPUT TYPE=reset VALUE=\"Clear add/delete selections\" CLASS=\"button\"></TD>\r\n\t\t</TR>\r\n";
	echo "\t\t<TR>\r\n\t\t\t<TD>&nbsp</TD>\r\n\t\t</TR>\r\n";

	echo "\t\t</TABLE>\r\n\t</TD>\r\n</TR>\r\n";
?>
<TR>
	<TD ALIGN="center" COLSPAN=15><A HREF="bta_main.php">Return to main administration panel</A></TD>
</TR>
</TABLE>

</FORM>
</BODY>
</HTML>
