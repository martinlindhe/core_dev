<?php
	/*
	 * Module:	bta_edit.php
	 * Description: This is the edit torrent screen of the administrative interface.
	 * 		This module inserts a torrent's details in the database so it can
	 * 		be tracked.
	 *   		Various fields added and changed sql statements to update info.. also added 
	 *			session tracking so you don't have to enter user/pass all the time.
	 *
	 * Author:	danomac
	 * Written:	21-March-2004
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
	require_once ("../BDecode.php");
	require_once ("../BEncode.php");
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
	if (!($_SESSION["admin_perms"]["edit"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Redirecting to the main administration panel.",
			       $adm_page_title, true, "bta_main.php", 3);
	}

	/*
	 * Let's get the details of the torrent to be edited. If no hash specified, return
	 * to the main admin screen.
	 */
	if (!isset($_SESSION["info_hash"])) {
		admShowMsg("No info hash specified.", "No hash specified (internal error).", "Error", true, "bta_main.php", 5);
		exit;
	} else {
		/*
		 * Get the hash value from the session...
		 */
		$hash = $_SESSION["info_hash"];

		/*
		 * Connect to the database
		 */
		if ($GLOBALS["persist"])
			$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		else
			$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		mysql_select_db($database) or die("Can't open the database.");

		/*
		 * Grab info needed
		 */
      $recordset = mysql_query("SELECT namemap.info_hash, 
					namemap.filename,
					namemap.size,
					namemap.url, 
					namemap.mirrorurl, 
					namemap.sfvlink,
					namemap.md5link,
					namemap.infolink,
					namemap.info, 
					namemap.category, 
					namemap.crc32, 
					namemap.DateAdded, 
					namemap.DateToRemoveURL,
					namemap.DateToHideTorrent,
					summary.hide_torrent,
					summary.external_torrent,
					namemap.comment,
					namemap.show_comment FROM summary LEFT JOIN namemap ON summary.info_hash = namemap.info_hash WHERE summary.info_hash=\"$hash\"") or sqlErr("Database error. Cannot complete request.");

		/*
		 * Make sure there was a record returned. If no record, hash must be invalid.
		 */
		if (mysql_num_rows($recordset) == 0) {
			admShowMsg("Invalid info hash specified.", "Invalid hash specified (internal error).", "Error", true, "bta_main.php", 5);
			exit;
		} else {
			/*
			 * There is data; retrieve it, and get the current day.
			 */
			$row = mysql_fetch_row($recordset);
			$today = date("Y-m-d");
		}
	}

	/*
	 * Set the message to an empty string. If there is content in this string, it
	 * is displayed in the table. Also sets an "error" variable so skipping processing
	 * can be done.
	 */
	$statusMsg = "";

	/*
	 * If the "Apply changes" button was pressed, then check the data and
	 * enter it.
	 */
	if (isset($_POST["edittorrent"])) {
		/*
		 * Get and check all the text fields in the form. If something was set,
		 * copy it into a variable.
		 */
		if (isset($_POST["filename"]))
			$filename=$_POST["filename"];
		else
			$filename = "";

		if (isset($_POST["filesize"]))
			$filesize=$_POST["filesize"];
		else
			$filesize = "";

		if (isset($_POST["url"]))
			$url = $_POST["url"];
		else
			$url = "";

		if (isset($_POST["urlmirror"]))
			$urlmirror = $_POST["urlmirror"];
		else
			$urlmirror = "";

		if (isset($_POST["sfv"]))
			$urlsfv = $_POST["sfv"];
		else
			$urlsfv = "";

		if (isset($_POST["md5"]))
			$urlmd5 = $_POST["md5"];
		else
			$urlmd5 = "";

		if (isset($_POST["urlinfo"]))
			$urlinfo = $_POST["urlinfo"];
		else
			$urlinfo = "";

		if (isset($_POST["shortdesc"]))
			$shortdesc = $_POST["shortdesc"];
		else
			$shortdesc = "";

		if (isset($_POST["crcinfo"]))
			$crcinfo = $_POST["crcinfo"];
		else
			$crcinfo = "";	

		if (isset($_POST["category"]))
			$category = $_POST["category"];

		if (isset($_POST["adddate"]))
			$dateadded = $_POST["adddate"];
		else
			$dateadded = "";	

		if (isset($_POST["removeurl"]))
			$removeurldate = $_POST["removeurl"];
		else
			$removeurldate = "";	

		if (isset($_POST["indexhidetorrent"]))
			$hidetorrentdate = $_POST["indexhidetorrent"];
		else
			$hidetorrentdate = "";	

		if (isset($_POST["comment"]))
			$comment = $_POST["comment"];
		else
			$comment = "";	

		/*
		 * Check to see if it the category is a zero length string
		 * If it is, assume "main" as the category.
		 */
		if (strlen($category)==0) 
			$category = "main";

		/*
		 * Check to see if there is a forced category to use, if
		 * there is use it.
	 	 */
		if (isset($_SESSION["admin_perms"]["category"]))
			$category = $_SESSION["admin_perms"]["category"];

		/*
		 * If the option to reset the date added field to todays,
		 * date, do so.
		 */	
		if (isset($_POST["resetdateadded"]))
			if (strcmp($_POST["resetdateadded"], "enabled") == 0)
				$dateadded = $today;

		$editError = false;

		/*
		 * Valid category name check
		 */
		if (strpos($category, " ") !== false) {
			$editError = true;
			$statusMsg = "ERROR: Category names can't contain spaces.";		
		}

		/*
		 * Check the dates to make sure they are legitimate dates, and the url
		 * for an HTML anchor character.
		 * If not, display a warning and don't make the changes.
		 */
		if (!isDate($removeurldate, true) || !isDate($hidetorrentdate, true) || !isDate($dateadded, false) ||	stristr($url, "#")) {
			if ($editError) {
				$statusMsg .= "<BR>ERROR: A date is invalid, or the filename contains an anchor character (#). Please use the format yyyy-mm-dd for dates (ie. '2004-03-24'); The Date Added field is required.";
			} else {
				$statusMsg = "ERROR: A date is invalid, or the filename contains an anchor character (#). Please use the format yyyy-mm-dd for dates (ie. '2004-03-24'); The Date Added field is required.";
			}
			$editError = true;
		} 

		if (isset($_POST["showcomment"])) {
				if (strcmp($_POST["showcomment"], "enabled") == 0 && strlen(trim($_POST["comment"])) == 0) {
					if ($editError) {
						$statusMsg .= "<BR>ERROR: You haven't specified anything in the comment field!";
					} else {
						$statusMsg = "ERROR: You haven't specified anything in the comment field!";
					}
					$editError = true;
				} else {
					if (strcmp($_POST["showcomment"], "enabled") == 0) {
						$show_comment = 'Y';
					} else {
						$show_comment = 'N';
					}
				}
		} else
			$show_comment = 'N';

		if ($editError) {
			/*
			 * Okay, now we have an error, so let's copy the newly entered
			 * data into the $row variable so the user can correct his/her
			 * mistake. Otherwise the fields would show the data pulled
			 * from the database again, which isn't really desireable in this case.
			 */
			$row[1] = $filename;
			$row[2] = $filesize;
			$row[3] = $url;
			$row[4] = $urlmirror;
			$row[5] = $urlsfv;
			$row[6] = $urlmd5;
			$row[7] = $urlinfo;
			$row[8] = $shortdesc;
			$row[9] = $category;
			$row[10] = $crcinfo;
			$row[11] = $dateadded;
			$row[12] = $removeurldate;
			$row[13] = $hidetorrentdate;
			$row[16] = $comment;

			if (isset($_POST["hidetorrent"])) {
				if (strcmp($_POST["hidetorrent"], "enabled") == 0)
					$row[14] = 'Y';
				else
					$row[14] = 'N';
			} else
				$row[14] = 'N';

			if (isset($_POST["showcomment"])) {
				if (strcmp($_POST["showcomment"], "enabled") == 0)
					$row[17] = 'Y';
				else
					$row[17] = 'N';
			} else
				$row[17] = 'N';
		} else {
			/*
			 * Trying to stay HTML compliant is a pain in the ass.
			 */
			$filename = htmlentities(stripslashes($filename));
			$url = htmlentities(stripslashes($url));
			$urlmirror = htmlentities(stripslashes($urlmirror));
			$urlsfv = htmlentities(stripslashes($urlsfv));
			$urlmd5 = htmlentities(stripslashes($urlmd5));
			$urlinfo = htmlentities(stripslashes($urlinfo));
			$shortdesc = htmlentities(stripslashes($shortdesc));
			$crcinfo = htmlentities(stripslashes($crcinfo));
			$comment = htmlentities(stripslashes($comment));

			/*
			 * Build the query string
			 */
			$query = "UPDATE namemap SET filename=\"$filename\", 
													size=\"$filesize\",
													url=\"$url\", 
													mirrorurl=\"$urlmirror\", 
													sfvlink=\"$urlsfv\",
													md5link=\"$urlmd5\",
													infolink=\"$urlinfo\",
													info=\"$shortdesc\", 
													category=\"$category\",
													crc32=\"$crcinfo\", 
													dateadded=\"$dateadded\",
													DateToRemoveURL=\"$removeurldate\",
													DateToHideTorrent=\"$hidetorrentdate\",
													show_comment=\"$show_comment\",
													comment=\"$comment\" WHERE info_hash=\"$hash\"";

			/* 
			 * Do the update
			 */
			quickQuery($query);

			/*
			 * If the user requested the torrent to be hidden, hide it.
			 */
			if (isset($_POST["hidetorrent"])) {
				if (strcmp($_POST["hidetorrent"], "enabled") == 0)
					quickQuery("UPDATE summary SET hide_torrent=\"Y\" WHERE info_hash=\"$hash\"");
			} else
				quickQuery("UPDATE summary SET hide_torrent=\"N\" WHERE info_hash=\"$hash\"");

			/*
			 * Check to see if an external torrent needs to be reverted.
			 */
			if (isset($_POST["ext_off"])) {
				if (strcmp($_POST["ext_off"], "enabled") == 0) {
					/*
					 * Yes, let's reverse it
					 */
					quickQuery("UPDATE `summary` SET `external_torrent`=\"N\" WHERE `info_hash`=\"$hash\"");
					quickQuery("DELETE FROM `trk_ext` WHERE `info_hash`=\"$hash\"");
				}
			}

			/*
			 * Display the status to the user (always success).
			 */
			admShowMsg("Changes applied.", "Returning to the main administration page.", "Redirecting", true, "bta_main.php", 2);
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
	echo "<TITLE>". $adm_page_title . " - Edit torrent</TITLE>\r\n";
?>
</HEAD>

<BODY>
<FORM ENCTYPE="multipart/form-data" METHOD="POST" ACTION="bta_edit.php">
<TABLE CLASS="tblAdminOuter">
<TR>
<?php
	/*
	 * Display the page heading.
	 */
	echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>Edit torrent</TD>\r\n";
?>
</TR>
<?php admShowURL_Login($ip); ?>
<TR>
	<TD CLASS="data" COLSPAN=15 ALIGN="center"><BR>
	   <A HREF="help/help_edit_torrent.php" TARGET="_blank">Need help?</A><BR>
	</TD>
</TR>
<TR>
	<TD ALIGN="center" COLSPAN=15>
	<TABLE BORDER=1>
<?php
	/*
	 * Display the status of the operation, if there is a message.
	 */
	if (strlen($statusMsg) > 0)
		echo "\t<TR>\r\n\t\t<TD ALIGN=\"center\" COLSPAN=15><DIV CLASS=\"status\">$statusMsg</DIV></TD>\r\n\t</TR>";
?>
	<TR>
		<TD COLSPAN=3 ALIGN="center"><A HREF="bta_main.php">Return to Main Administrative screen.</A><BR>&nbsp;</TD>
	</TR>
<?php
	/*
	 * Check to see if this torrent is supposed to be hidden.
	 */
	if ($row[14]=="Y")
		$hidestatus = " CHECKED";
	else
		$hidestatus = "";

	/*
	 * Check to see if this torrent is supposed to be hidden.
	 */
	if ($row[17]=="Y")
		$show_comment_status = " CHECKED";
	else
		$show_comment_status = "";

	/*
	 * Check to see if the dates are "empty"
	 */
	if ($row[11] == "0000-00-00") $row[11] = "";
	if ($row[12] == "0000-00-00") $row[12] = "";
	if ($row[13] == "0000-00-00") $row[13] = "";

	/*
	 * Spit out the editboxes needed
	 */
	echo "\t<TR>\r\n\t\t<TD>Info hash:</TD>\r\n\t\t<TD>$row[0]</TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD>File name:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"filename\" SIZE=40 MAXLENGTH=200 VALUE=\"$row[1]\">&nbsp;&nbsp;If you want to use a different name, enter it here.</TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD>File size:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"filesize\" SIZE=40 MAXLENGTH=20 VALUE=\"$row[2]\">&nbsp;&nbsp;In MiB.</TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD>URL to torrent:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"url\" SIZE=40 MAXLENGTH=200 VALUE=\"$row[3]\">&nbsp;&nbsp;Enter the URL to the torrent.</TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD>Mirror for torrent:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"urlmirror\" SIZE=40 MAXLENGTH=200 VALUE=\"$row[4]\">&nbsp;&nbsp;Enter the URL to the mirror site.</TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD>URL to SFV file:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"sfv\" SIZE=40 MAXLENGTH=200 VALUE=\"$row[5]\">&nbsp;&nbsp;Enter the URL to the SFV file.</TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD>URL to MD5 file:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"md5\" SIZE=40 MAXLENGTH=200 VALUE=\"$row[6]\">&nbsp;&nbsp;Enter the URL to the MD5 file.</TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD>Info URL:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"urlinfo\" SIZE=40 MAXLENGTH=200 VALUE=\"$row[7]\">&nbsp;&nbsp;Enter the URL to an information link.</TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD>Description:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"shortdesc\" SIZE=40 MAXLENGTH=200 VALUE=\"$row[8]\">&nbsp;&nbsp;Enter a short description.</TD>\r\n\t</TR>\r\n";
	if ($_SESSION["admin_perms"]["root"])
		echo "\t<TR>\r\n\t\t<TD>Category:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"category\" SIZE=11 MAXLENGTH=10 VALUE=\"$row[9]\">&nbsp;&nbsp;Enter a category.</TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD>CRC32 checksum:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"crcinfo\" SIZE=40 MAXLENGTH=254 VALUE=\"$row[10]\">&nbsp;&nbsp;Enter CRC32 checksum information.</TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD>Date Added:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"adddate\" SIZE=15 MAXLENGTH=254 VALUE=\"$row[11]\">&nbsp;&nbsp;Date torrent added. Use the format YYYY-MM-DD.</TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD>Remove URL:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"removeurl\" SIZE=15 MAXLENGTH=254 VALUE=\"$row[12]\">&nbsp;&nbsp;Enter a date (format: yyyy-mm-dd) to remove the URL from the index page.</TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD>Hide from index:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"indexhidetorrent\" SIZE=15 MAXLENGTH=254 VALUE=\"$row[13]\">&nbsp;&nbsp;Enter a date (format: yyyy-mm-dd) to hide the URL from the index page. <B>Note: Torrent stays active.</B></TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD>Comment:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"comment\" SIZE=40 MAXLENGTH=254 VALUE=\"$row[16]\">&nbsp;&nbsp;This replaces the statistics!</B></TD>\r\n\t</TR>\r\n";
	echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=\"checkbox\" NAME=\"showcomment\" VALUE=\"enabled\" $show_comment_status> Show comment? (Replaces statistics!)";

	echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=\"checkbox\" NAME=\"hidetorrent\" VALUE=\"enabled\" $hidestatus> Hide this torrent";

	if (!$_SESSION["admin_perms"]["root"])
		echo "\t<INPUT TYPE=\"hidden\" NAME=\"category\" VALUE=\"".$_SESSION["admin_perms"]["category"]."\">\r\n";
	echo "</TD>\r\n\t</TR>";

	if (($_SESSION["admin_perms"]["addext"] || $_SESSION["admin_perms"]["root"]) && $row[15] == "Y")
		echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=\"checkbox\" NAME=\"ext_off\" VALUE=\"enabled\"> Remove external status (not reversible!)</TD>\r\n\t</TR>";

	echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=\"checkbox\" NAME=\"resetdateadded\" VALUE=\"enabled\"> Reset Date Added to today's date</TD>\r\n\t</TR>";
?>
	<TR>
		<TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=submit NAME="edittorrent" VALUE="Apply changes" CLASS="button">&nbsp;&nbsp;<INPUT TYPE=reset VALUE="Clear settings" CLASS="button"></TD>
	</TR>
	<TR>
		<TD COLSPAN=3 ALIGN="center"><BR><A HREF="bta_main.php">Return to Main Administrative screen.</A></TD>
	</TR>
	</TABLE>
	</TD>
</TR>
</TABLE> 
</FORM>
</BODY>
</HTML>