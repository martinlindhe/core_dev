<?php
	/*
	 * Module:	bta_banconfirm.php
	 * Description: This is the confirm operation screen of the IP banning interface.
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
	if (!($_SESSION["admin_perms"]["ipban"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Redirecting to the main administration panel.",
			       $adm_page_title, true, "bta_main.php", 3);
	}

	/*
	 * If delete all bans button pressed, remove all the bans and return
	 */
	if (isset($_POST["delbansbutton"])) {
		/*
		 * Connect to the database
		 */
		if ($GLOBALS["persist"])
			$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		else
			$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		mysql_select_db($database) or sqlErr(mysql_error());

		@mysql_query("TRUNCATE TABLE `ipbans`");

		admShowMsg("All bans purged.","Redirecting to IP Banning administration page.","Redirecting", true, "bta_banlist.php", 3);
		exit;
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
		if (isset($_SESSION["deletelist"])) {
			/*
			 * Go through each value and remove the data and tables
			 */
			foreach ($_SESSION["deletelist"] as $key => $value) {
				@mysql_query("DELETE FROM ipbans WHERE ban_id=".$_SESSION["deletelist"][$key]["ban_id"]."");
			}
			/*
			 * Destroy the variable
			 */
			unset($_SESSION["deletelist"]);
		}

		/*
		 * Process add ban, if something was entered
		 */
		if (isset($_SESSION["addban"])) {
			/*
			 * Hmm. Check to see if it's a permban or not
			 */
			$iplong = ip2long($_SESSION["addban"]["ip"]);
			if (isset($_SESSION['addban']['expiry']))
				@mysql_query("INSERT INTO ipbans (ip, iplong, bandate, reason, autoban, banlength, banautoexpires, banexpiry) 
									VALUES (\"".$_SESSION["addban"]["ip"]."\", 
										$iplong, 
										\"".$_SESSION["addban"]["date"]."\", 
										\"".$_SESSION["addban"]["reason"]."\", 
										\"N\", ".$_SESSION["addban"]["banlength"].", 
										\"Y\", 
										\"".$_SESSION["addban"]["expiry"]."\")");
			else
				@mysql_query("INSERT INTO ipbans (ip, iplong, bandate, reason, autoban) 
									VALUES (\"".$_SESSION["addban"]["ip"]."\", 
										$iplong, 
										\"".$_SESSION["addban"]["date"]."\", 
										\"".$_SESSION["addban"]["reason"]."\", 
										\"N\")");
			/*
			 * Destroy the variable
			 */
			unset($_SESSION["addban"]);
		}

		admShowMsg("Changes applied.","Redirecting to IP Banning administration page.","Redirecting", true, "bta_banlist.php", 3);
		exit;
	}

	/*
	 * Check to make sure something was actually selected.
	 */	
	if (!isset($_POST["process"]) && !isset($_POST["addbanip"])) {
		admShowMsg("Nothing to do.","Redirecting to IP Banning administration page.","Redirecting", true, "bta_banlist.php", 3);
		exit;
	} else
		$processlist = $_POST["process"];

	/*
	 * Connect to the database
	 */
	if ($GLOBALS["persist"])
		$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	else
		$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	mysql_select_db($database) or sqlErr(mysql_error());

	if (isset($processlist)) {
		foreach ($processlist as $ban_id => $action) {
			/*
			 * Go through the list and populate array as required.
			 */
			switch ($action) {
					case ACTION_DELETE:
						/*
						 * Build query string
						 */
						$query = "SELECT ban_id, ip, 
							bandate, 
							reason, 
							autoban FROM ipbans WHERE ban_id = $ban_id";

						/*
						 * Do the query, get the row...
						 */
						$recordset = mysql_query($query) or sqlErr(mysql_error());		
						$row=mysql_fetch_row($recordset);

						$deletelist[] = array('ban_id' => $ban_id,
													'ip' => $row[1],
													'action' => $action,
													'bandate' => $row[2],
													'reason' => stripslashes($row[3]),
													'autoban' => $row[4]);
						break;
			}
		}
	}

	/*
	 * Used to display a message to the user on the
	 * add ban stat
	 */
	$msg = "";

	/*
	 * See if an add IP ban was requested
	 */
	unset($_SESSION['addban']);
	if (isset($_POST['addbanip'])) {
		if (strlen($_POST['addbanip']) > 0) {
			/*
			 * Do a simple check to make sure IP is valid.
			 */
			if (checkIP($_POST['addbanip'])) {
				if (isset($_POST['addbanreason'])) {
					if (strlen($_POST['addbanreason']) > 0) {
						if (isset($_POST['addbanlength'])) {
							if ($_POST['addbanlength'] == 0 || !is_numeric($_POST['addbanlength'])) {
								$_SESSION['addban'] = array('ip' => $_POST['addbanip'],
									'reason' => $_POST['addbanreason'],
									'date' => date("Y-m-d"));
							} else {
								$_SESSION['addban'] = array('ip' => $_POST['addbanip'],
									'reason' => $_POST['addbanreason'],
									'date' => date("Y-m-d"),
									'expiry' => date("Y-m-d", time() + ($_POST['addbanlength'] * 86400)),
									'banlength' => $_POST['addbanlength']);
							}
						} else {
							$_SESSION['addban'] = array('ip' => $_POST['addbanip'],
									'reason' => $_POST['addbanreason'],
									'date' => date("Y-m-d"));
						}
					} else {
						$msg = "You need to specify a ban reason. If you continue, the ban will NOT be added.";
					}
				} else {
						$msg = "You need to specify a ban reason. If you continue, the ban will NOT be added.";
				}
			} else {
						$msg = "You need to specify a valid IP to ban. If you continue, the ban will NOT be added.";
			}
		} else
			$msg = "You have not entered an IP address to ban; no ban will be added.";
	} 
	/*
	 * Okay, they are seperated, now sort the lists,
	 * and put them in a session variable for later use.
	 * First, unset the session variables, in case the screen has been
	 * used previously in the same session.
	 */
	unset($_SESSION['deletelist']);
	if (count($deletelist) > 0)
		$_SESSION['deletelist'] = array_sort($deletelist, "ip");
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
<FORM ENCTYPE="multipart/form-data" METHOD="POST" ACTION="bta_banconfirm.php">
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
	   <A HREF="help/help_confirm_ipban.php" TARGET="_blank">Need help?</A><BR>
	</TD>
</TR>
<TR>
<?php
	echo "\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_banlist.php\">Return to IP Banning Administrative screen (no changes will be made).</A><BR>&nbsp;</TD>\r\n";
?>
</TR>
<TR>
	<TD COLSPAN=15>
		<FONT SIZE=+2>You have elected to:</FONT><BR><BR>
<?php
	/*
	 * Show add ban details, or a message if no IP address entered
	 */
	echo "\t <FONT SIZE=+2><B>ADD A BAN</B>:</FONT>&nbsp;";

	if (strlen($msg) > 0)
		echo "<DIV CLASS=\"status\">$msg</DIV><BR><BR>";
	else {
		if (isset($_SESSION['addban'])) { 
			if (isset($_SESSION['addban']['expiry']))
				echo "IP: <B>". $_SESSION['addban']['ip'] . "</B> that expires on ". $_SESSION['addban']['expiry']." with the following reason: <B>". $_SESSION['addban']['reason']."</B><BR><BR>";
			else
				echo "IP: <B>". $_SESSION['addban']['ip'] . "</B> permanently with the following reason: <B>". $_SESSION['addban']['reason']."</B><BR><BR>";
		}
	}

	/*
	 * These are the alternating Cascadying Style Sheet classes used for the data.
	 */
	$classRowBGClr[0] = 'CLASS="odd"';
	$classRowBGClr[1] = 'CLASS="even"';

	if (isset($_SESSION["deletelist"])) {
		echo "\t\t<FONT SIZE=+2><B>DELETE the following:</B></FONT><BR>";
		echo "\t\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>IP</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>Date of Ban</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Reason for ban</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Automatic Ban?</B></TD>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Use to alternate rows.
		 */
		$rowCount = 0;

		/*
		 * Spit out the contents stored in the session variable.
		 */
		foreach ($_SESSION["deletelist"] as $key => $value) {
			$useRowClass = $classRowBGClr[$rowCount % 2];
			echo "\t\t\t<TD $useRowClass>".$_SESSION["deletelist"][$key]["ip"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass>".$_SESSION["deletelist"][$key]["bandate"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["deletelist"][$key]["reason"]."</TD>\r\n";
			if ($_SESSION["deletelist"][$key]["autoban"] == 'Y')
				echo "\t\t\t<TD $useRowClass ALIGN=\"center\">Yes</TD>\r\n";
			else
				echo "\t\t\t<TD $useRowClass ALIGN=\"center\">&nbsp;</TD>\r\n";
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
	echo "\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_banlist.php\">Return to IP Banning Administrative screen (no changes will be made).</A><BR>&nbsp;</TD>\r\n";
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