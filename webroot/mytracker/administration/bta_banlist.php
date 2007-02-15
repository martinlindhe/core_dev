<?php

	/*
	 * Module:	bta_banlist.php
	 * Description: This is the IP Banning Admin interface.
	 * 		It lists all the IP bans and allows you to 
	 * 		manage them.
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
	 * If set to TRUE, the index page will only display a list of active groups on the tracker.
	 * When a group is chosen, then torrent admin will be displayed.
	 */
	$showCategorySelectionOnly = false;

	/*
	 * default ordering: if no order is specified, use this order (valid values: "ip", "bandate", "reason", "autoban")
	 */
	$defaultorder = "ip";

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
	 * Group admin: are they actually allowed to view this page?
	 * If not, redirect them back to main
	 */
	if (!($_SESSION["admin_perms"]["ipban"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Redirecting to the main administration panel.",
			       $adm_page_title, true, "bta_main.php", 3);
	}

	/*
	 * Get the order from the URL, if one is specified.
	 * If not specified, assume default ordering scheme.
	 */
	if (isset($_GET["order"])) {
		if (strpos($_GET["order"], " ") !== false) {
			admShowMsg("Invalid order parameter", "An invalid order value was passed.", "Invalid order request", true, "bta_banlist.php", 5);
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
		if (strpos($_GET["sort"], " ") !== false) {
			admShowMsg("Invalid sort parameter", "An invalid sort value was passed.", "Invalid sort request", true, "bta_banlist.php", 5);
		}

		$requestedsort = $_GET["sort"];
	
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
	$iphref = $scriptname . "?order=ip";
	$reasonhref = $scriptname . "?order=reason";
	$datehref = $scriptname . "?order=bandate";
	$expiryhref = $scriptname . "?order=banexpiry";
	$autohref = $scriptname . "?order=autoban";

	/*
	 * This switch statments checks which is the CURRENT sort order,
	 * and specifies an option to REVERSE the current sort order.
	 * e.g. If sorting by name in ASCENDING order, the hyperlink will add 
	 *      a parameter to sort the name in DESCENDING order.
	 */
	switch ($requestedorder) {
		case "ip": 
			$order = " ORDER BY ipbinary " . $sortorder;
			if ($sortascending) $iphref = $iphref . "&amp;sort=descending"; else $iphref = $iphref . "&amp;sort=ascending";
			break;
		case "reason":
			$order = " ORDER BY reason " . $sortorder . ", ip ";
			if ($sortascending) $reasonhref = $reasonhref . "&amp;sort=descending"; else $reasonhref = $reasonhref . "&amp;sort=ascending";
			break;
		case "bandate":
			$order = " ORDER BY bandate " . $sortorder . ", ip ";
			if ($sortascending) $datehref = $datehref . "&amp;sort=descending"; else $datehref = $datehref . "&amp;sort=ascending";
			break;
		case "banexpiry":
			$order = " ORDER BY banexpiry " . $sortorder . ", ip ";
			if ($sortascending) $expiryhref = $expiryhref . "&amp;sort=descending"; else $expiryhref = $expiryhref . "&amp;sort=ascending";
			break;
		case "autoban":
			$order = " ORDER BY autoban " . $sortorder . ", ip ";
			if ($sortascending) $autohref = $autohref . "&amp;sort=descending"; else $autohref = $autohref . "&amp;sort=ascending";
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
	echo "<TITLE>". $adm_page_title . " - IP Ban Management</TITLE>\r\n";
?>
</HEAD>

<BODY>
<FORM ACTION="bta_banconfirm.php" METHOD=POST>

<TABLE CLASS="tblAdminOuter">
<TR>
<?php
	/*
	 * Display the page heading.
	 */
	echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>IP Banning Management</TD>\r\n";
?>
</TR>
<?php admShowURL_Login($ip); ?>
<TR>
	<TD CLASS="data" COLSPAN=15 ALIGN="center"><BR>
	   <A HREF="help/help_ip_bans.php" TARGET="_blank">Need help?</A><BR>
	</TD>
</TR>
<TR>
	<TD CLASS="data" COLSPAN=15><BR>
		Welcome to the IP banning interface. <BR><BR>
		Below is a list of current IP bans.<BR>
		<BR><B>Quick HOWTO:</B><BR>
		<B>Add an IP ban:</B> Two choices: 1. Use the text fields below to add a single ban; or 2. Use the <I>Display Peer Information</I> (accessible through the Main Administration screen) for a torrent, and mark the IP address there.<BR>
		<B>Remove an IP ban:</B> Locate the IP address in the list below, and check the checkbox in the Remove Ban Column. You can mark several items, then use the button at the bottom of the form.<BR>
		<BR><BR>
	</TD>
</TR>

<TR>
	<TD ALIGN="center" COLSPAN=3><B><FONT COLOR="red">Wildcards are NOT supported</FONT></B></TD>
</TR>
<TR>
	<TD ALIGN=RIGHT><B>Add new ban:</B> IP address: <INPUT TYPE=TEXT NAME="addbanip" MAXLENGTH=16 SIZE=16></TD><TD> Reason: <INPUT TYPE=TEXT NAME="addbanreason" MAXLENGTH=50 SIZE=30></TD>
</TR>
<TR>
	<TD ALIGN=CENTER COLSPAN=2>Ban Length (days, enter 0 (zero) for an infinite ban): <INPUT TYPE=TEXT NAME="addbanlength" MAXLENGTH=5 SIZE=5 VALUE=0></TD>
</TR>
<?php
	echo "<TR>\r\n\t<TD COLSPAN=5 ALIGN=\"center\"><A HREF=\"bta_main.php\">Return to main administration panel</A></TD>\r\n</TR>\r\n";

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
	echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD COLSPAN=15 CLASS=\"sortheading\">To sort the data, click on the column header hyperlinks.</TD>\r\n\t\t\t\t</TR>\r\n";
	echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"left\" VALIGN=\"bottom\"><A HREF=\"$iphref\">IP Address</A></TD>\r\n";
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\"><A HREF=\"$datehref\">Date of Ban</A></TD>\r\n";
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\"><A HREF=\"$expiryhref\">Expires On</A></TD>\r\n";
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\"><A HREF=\"$reasonhref\">Reason for ban</A></TD>\r\n";
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\"><A HREF=\"$autohref\">Automatic<BR>Ban?</A></TD>\r\n"; 
	echo "\t\t\t\t\t<TD CLASS=\"heading\" ALIGN=\"center\" VALIGN=\"bottom\">Remove<BR>ban?</TD>\r\n";
	echo "\r\n\t\t\t\t</TR>";

	/*
	 * These are the alternating Cascadying Style Sheet classes used for the data.
	 */
	$classRowBGClr[0] = 'CLASS="odd"';
	$classRowBGClr[1] = 'CLASS="even"';

	/*
	 * Build a query string of information we need to get from the database
	 */
	$query = "SELECT ip, 
			bandate, 
			reason, 
			autoban,
			banautoexpires,
			banexpiry, ban_id, INET_ATON(ip) AS ipbinary FROM ipbans $order";

	/*
	 * Get a recordset of the torrents...
	 */
	$recordset = mysql_query($query) or sqlErr(mysql_error());

	/*
	 * Let's keep track of the total # of IPs banned, so we need to initialize a variable
	 */
	$totalbans = 0;

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
		//check for empty or null values
		/*
		 * Make sure the filename isn't NULL or an empty string
		 */
		if (is_null($row[1])) $row[1] = $row[0];	//filename null? if yes, use hash
		if (strlen($row[1]) == 0) $row[1]=$row[0];	//filename empty? if yes, use hash

		/*
		 * Let's set a variable with the current IP adress
		 */
		$currentIP = $row[0];

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
		 * IP Address
		 */
		echo "\t\t\t\t\t<TD $cellBG>";
		echo $row[0];

		echo "</TD>\r\n";

		/*
		 * Date IP was banned
		 */
		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">$row[1]</TD>\r\n";

		/*
		 * Date IP will be unbanned
		 */
		if ($row[4]=='Y')
			echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">$row[5]</TD>\r\n";
		else
			echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">&nbsp;</TD>\r\n";
		/*
		 * Reason for the ban
		 */
		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">$row[2]</TD>\r\n";

		/*
		 * Was this ban automatically inserted?
		 */
		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\">";

		//if the torrent is hidden, make sure it's indicated
		if ($row[3]=='Y')
			echo "Yes";
		else
			echo "&nbsp;";

		echo "</TD>\r\n";

		/*
		 * Remove IP ban
		 */
		echo "\t\t\t\t\t<TD $cellBG ALIGN=\"center\"><INPUT TYPE=checkbox NAME=\"process[$row[6]]\" VALUE=\"".ACTION_DELETE."\"></TD>\r\n";

		echo "\t\t\t\t</TR>\r\n";

		/*
		 * Keep a running total for the stats line... so increment them.
		 */
		$totalbans++;
	}

	/*
	 * If there were no torrents, display a 'No active torrents' message, otherwise
	 * show a summary line.
	 */
	if ($totalbans == 0)
		echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD $classRowBGClr[0] COLSPAN=15 ALIGN=CENTER>Nobody is banned</TD>\r\n\t\t\t\t</TR>";
	else {
		/*
		 * Calculate for odd/even row
		 */
		$cellBG = $classRowBGClr[$totaltorrents % 2];

		/*
		 * Total amount of active bans on the tracker.
		 */
		echo "\t\t\t\t<TR>\r\n\t\t\t\t\t<TD $cellBG ALIGN=\"center\" COLSPAN=7><b>$totalbans IP Address(es) banned</b>.</TD>\r\n";

		echo "\t\t\t\t</TR>\r\n";
	}

	echo "\t\t\t\t</TABLE>\r\n\t\t\t</TD>\r\n\t\t</TR>\r\n";

	// -- Summary at bottom of the torrent table. --
	echo "\t\t<TR>\r\n\t\t\t<TD ALIGN=LEFT CLASS=\"summary\">". $adm_page_title ." using MySQL.</TD>\r\n";
	echo "\t\t\t<TD ALIGN=RIGHT CLASS=\"summary\">Ki = 1024, Mi = 1024 Ki, Gi = 1024 Mi, Ti = 1024 Gi</TD>\r\n\t\t</TR>\r\n";

	echo "<TR>\r\n\t<TD COLSPAN=5 ALIGN=\"center\"><A HREF=\"bta_main.php\">Return to main administration panel</A></TD>\r\n</TR>\r\n";
	echo "\t\t<TR>\r\n\t\t\t<TD>&nbsp</TD>\r\n\t\t</TR>\r\n";
	echo "\t\t<TR>\r\n\t\t\t<TD ALIGN=\"center\"><INPUT TYPE=\"submit\" NAME=\"processbutton\" VALUE=\"Process ban requests...\" CLASS=\"button\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=\"submit\" NAME=\"delbansbutton\" VALUE=\"Remove all bans (no confirmation)\" CLASS=\"button\"></TD>\r\n";
	echo "\t\t\t<TD ALIGN=\"center\"><INPUT TYPE=reset VALUE=\"Clear ban selections\" CLASS=\"button\"></TD>\r\n\t\t</TR>\r\n";
	echo "\t\t<TR>\r\n\t\t\t<TD>&nbsp</TD>\r\n\t\t</TR>\r\n";

	echo "\t\t</TABLE>\r\n\t</TD>\r\n</TR>\r\n";
?>
</TABLE>

</FORM>
</BODY>
</HTML>
