<?php
	/*
	 * Module:	bta_confirm.php
	 * Description: This is the confirm operation screen of the administrative interface.
	 * 		This module displays selected torrents from the main admin screen and asks the
	 * 		user to confirm the operation.
	 *
	 * Author:	danomac
	 * Written:	24-March-2004
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
	require_once ("bta_funcs.php");

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
	 * Upgrade check: Torrents in database
	 */
	if (!isset($GLOBALS["move_to_db"])) {
		$GLOBALS["move_to_db"] = false;
	}

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
	 * In this case, if any of unhide/retire/delete are enabled, they
	 * have permission.
	 * If not, redirect them back to main
	 */
	if (!($_SESSION["admin_perms"]["delete"] || $_SESSION["admin_perms"]["unhide"] || $_SESSION["admin_perms"]["retire"] || $_SESSION["admin_perms"]["root"])) {
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
		 * Process unhide list, if some were selected
		 */
		if (isset($_SESSION["unhidelist"])) {
			/*
			 * Go through each value and unhide them
			 */
			foreach ($_SESSION["unhidelist"] as $key => $value) {
				@mysql_query("UPDATE summary SET hide_torrent=\"N\" WHERE info_hash=\"".$_SESSION['unhidelist'][$key]['hash']."\"");
			}
			/*
			 * Destroy the variable
			 */
			unset($_SESSION["unhidelist"]);
		}

		/*
		 * Process hide list, if some were selected
		 */
		if (isset($_SESSION["hidelist"])) {
			/*
			 * Go through each value and unhide them
			 */
			foreach ($_SESSION["hidelist"] as $key => $value) {
				@mysql_query("UPDATE summary SET hide_torrent=\"Y\" WHERE info_hash=\"".$_SESSION['hidelist'][$key]['hash']."\"");
			}
			/*
			 * Destroy the variable
			 */
			unset($_SESSION["hidelist"]);
		}

		/*
		 * Process retire list, if some were selected
		 */
		if (isset($_SESSION["retirelist"])) {
			/*
			 * The date retired would be today, so get the date
			 */
			$today = date("Y-m-d");
	
			/*
			 * Go through each value and unhide them
			 */
			foreach ($_SESSION["retirelist"] as $key => $value) {
				/*
				 * Retiring torrents is a little more complicated. Most of
				 * the info needed is in the session variable, but a few fields
				 * are missing, so we'll query the database for the missing data.
				 */
        		$recordset = mysql_query("SELECT namemap.crc32, 
														summary.finished, 
														summary.dlbytes FROM summary 
															LEFT JOIN namemap ON summary.info_hash = namemap.info_hash 
															WHERE summary.info_hash=\"".$_SESSION["retirelist"][$key]["hash"]."\"") or die("Database error. Cannot complete request.");
				$row = mysql_fetch_row($recordset);

				/*
				 * Now put the relevant data into the retired torrents table...
				 */
				@mysql_query("INSERT INTO retired (info_hash, 
																filename, 
																size, 
																crc32, 
																category, 
																completed, 
																transferred, 
																dateadded, 
																dateretired) VALUES 
																	(\"".$_SESSION["retirelist"][$key]["hash"]."\", 
																	\"".$_SESSION["retirelist"][$key]["name"]."\", 
																	\"".$_SESSION["retirelist"][$key]["size"]."\", 
																	\"$row[0]\", 
																	\"".$_SESSION["retirelist"][$key]["category"]."\", 
																	$row[1], 
																	\"$row[2]\", 
																	\"".$_SESSION["retirelist"][$key]["date"]."\", 
																	\"$today\")");

				/*
				 * ... and delete the torrent.
				 */
				@mysql_query("DELETE FROM summary WHERE info_hash=\"".$_SESSION["retirelist"][$key]["hash"]."\"");
				@mysql_query("DELETE FROM namemap WHERE info_hash=\"".$_SESSION["retirelist"][$key]["hash"]."\""); 
				@mysql_query("DELETE FROM timestamps WHERE info_hash=\"".$_SESSION["retirelist"][$key]["hash"]."\""); 
				@mysql_query("DROP TABLE x".$_SESSION["retirelist"][$key]["hash"]);
				@mysql_query("DROP TABLE y".$_SESSION["retirelist"][$key]["hash"]);

				/*
				 * Time to reorder the remaining torrents, ignoring the return error (seeing as there isn't much we can do
				 * if it decides to not work...
				 */
				advSortDelete($_SESSION["retirelist"][$key]["category"], $_SESSION["retirelist"][$key]["grouping"], $_SESSION["retirelist"][$key]["sorting"]);
				
				/*
				 * Delete the torrent from the database if enabled.
				 */
				if ($GLOBALS["move_to_db"]) {
					@mysql_query("DELETE FROM torrents WHERE info_hash=\"".$_SESSION["retirelist"][$key]["hash"]."\""); 
				}
			}
			/*
			 * Destroy the variable
			 */
			unset($_SESSION["retirelist"]);
		}

		/*
		 * Process delete list, if some were selected
		 */
		if (isset($_SESSION["deletelist"])) {
			/*
			 * Go through each value and remove the data and tables
			 */
			foreach ($_SESSION["deletelist"] as $key => $value) {
				@mysql_query("DELETE FROM summary WHERE info_hash=\"".$_SESSION["deletelist"][$key]["hash"]."\"");
				@mysql_query("DELETE FROM namemap WHERE info_hash=\"".$_SESSION["deletelist"][$key]["hash"]."\""); 
				@mysql_query("DELETE FROM timestamps WHERE info_hash=\"".$_SESSION["deletelist"][$key]["hash"]."\""); 
				@mysql_query("DELETE FROM trk_ext WHERE info_hash=\"".$_SESSION["deletelist"][$key]["hash"]."\""); 
				@mysql_query("DROP TABLE x".$_SESSION["deletelist"][$key]["hash"]);
				@mysql_query("DROP TABLE y".$_SESSION["deletelist"][$key]["hash"]);

								/*
				 * Time to reorder the remaining torrents, ignoring the return error (seeing as there isn't much we can do
				 * if it decides to not work...
				 */
				advSortDelete($_SESSION["deletelist"][$key]["category"], $_SESSION["deletelist"][$key]["grouping"], $_SESSION["deletelist"][$key]["sorting"]);

				/*
				 * Delete the torrent from the database if enabled.
				 */
				if ($GLOBALS["move_to_db"]) {
					@mysql_query("DELETE FROM torrents WHERE info_hash=\"".$_SESSION["deletelist"][$key]["hash"]."\""); 
				}
			}
			/*
			 * Destroy the variable
			 */
			unset($_SESSION["deletelist"]);
		}

		/*
		 * Process retired torrent revive list, if some were selected
		 */
		if (isset($_SESSION["revivelist"])) {
			/*
			 * Go through each value and restore the data and tables
			 */
			foreach ($_SESSION["revivelist"] as $key => $value) {
				/*
				 * Recreate the table to track the peers
				 */
				$status = makeTorrent($_SESSION["revivelist"][$key]["hash"], true);

				/*
				 * If the creation was successful, put the completed and total transferred values back,
				 * add into namemap and then finally remove it from the retired table
				 */
				if ($status) {
					/*
					 * Now the fun part, we need to take into account advanced sorting... which means adding to
					 * group 0 for the specified category and adding it at the end of the sort list. The code below
					 * asks the database for this information and then figures out what to do.
					 */
					$rstAdvSort = @mysql_query("SELECT MAX(`sorting`) FROM `namemap` WHERE `grouping` = 0 and `category` = \"".$_SESSION["revivelist"][$key]["category"]."\"");
					if ($rstAdvSort === false) {
						$advsort = 1;
					} else {
						$lastval = mysql_result($rstAdvSort, 0, 0);
						if ($lastval === false) {
							$advsort = 1;
						} else {
							$advsort = $lastval + 1;
						}
					}
				
					$query = "INSERT INTO namemap (info_hash, 
									filename, 
									size, 
									crc32, 
									dateadded, 
									category,
									grouping,
									sorting,
									tsAdded) 
									VALUES (\"" . $_SESSION["revivelist"][$key]["hash"] . "\", 
										\"" . $_SESSION["revivelist"][$key]["name"] . "\", 
										" . $_SESSION["revivelist"][$key]["size"] . ", 
										\"" . $_SESSION["revivelist"][$key]["crc"] . "\", 
										\"" . $_SESSION["revivelist"][$key]["date"] . "\", 
										\"" . $_SESSION["revivelist"][$key]["category"] . "\",
										0,
										$advsort, UNIX_TIMESTAMP())";

					quickQuery($query);

					@mysql_query("UPDATE summary SET dlbytes=\"$data[6]\", finished=\"$data[5]\" WHERE info_hash=\"" . $_SESSION["revivelist"][$key]["hash"] . "\"");
					@mysql_query("DELETE FROM retired WHERE info_hash=\"" . $_SESSION["revivelist"][$key]["hash"] . "\"");
				}
			}
			/*
			 * Destroy the variable
			 */
			unset($_SESSION["revivelist"]);
		}

		/*
		 * Process retired delete list, if some were selected
		 */
		if (isset($_SESSION["rdeletelist"])) {
			/*
			 * Go through each value and remove the data from the retired table
			 */
			foreach ($_SESSION["rdeletelist"] as $key => $value) {
				@mysql_query("DELETE FROM retired WHERE info_hash=\"".$_SESSION["rdeletelist"][$key]["hash"]."\"");
			}
			/*
			 * Destroy the variable
			 */
			unset($_SESSION["rdeletelist"]);
		}
		if (isset($_SESSION["retiredadmin"]))
			admShowMsg("Changes applied.","Redirecting to retired torrents administration page.","Redirecting", true, "bta_retired.php", 3);
		else
			admShowMsg("Changes applied.","Redirecting to main administration page.","Redirecting", true, "bta_main.php", 3);
		exit;
	}

	/*
	 * Check to make sure something was actually selected.
	 */	
	if (!isset($_POST["process"])) {
		admShowMsg("Nothing selected.","Redirecting to main administration page.","Redirecting", true, "bta_main.php", 3);
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

	foreach ($processlist as $hash => $action) {
		/*
		 * Design choice: I decided to split up the operations by action. So,
		 * tables will be presented below with the actions grouped together. (i.e.
		 * there will be a table listing items selected for retired, a seperate table
		 * for listing items to be deleted.)
		 *
		 * To do this, they will be broken up into seperate arrays and populated
		 * with items grabbed from mysql. The arrays will be sorted by name using
		 * a function I threw together to sort multidimensional arrays.
		 */
		switch ($action) {
				case ACTION_DELETE:
					/*
					 * Build query string
					 */
					$query = "SELECT summary.info_hash, 
						namemap.filename, 
						namemap.size, 
						namemap.DateAdded, 
						summary.seeds, 
						summary.leechers, 
						namemap.category,
						summary.hide_torrent,
						summary.external_torrent,
						namemap.grouping,
						namemap.sorting
						FROM summary LEFT JOIN namemap ON summary.info_hash = namemap.info_hash WHERE summary.info_hash = \"$hash\"";

					/*
					 * Do the query, get the row...
					 */
					$recordset = mysql_query($query) or sqlErr(mysql_error());		
					$row=mysql_fetch_row($recordset);

					$deletelist[] = array('hash' => $hash,
												'action' => $action,
												'name' => $row[1],
												'size' => $row[2],
												'date' => $row[3],
												'seeders' => $row[4],
												'leechers' => $row[5],
												'category' => $row[6],
												'hide' => $row[7],
												'external' => $row[8],
												'grouping' => $row[9],
												'sorting' => $row[10]);
					break;
				case ACTION_RETIRE:
					/*
					 * Build query string
					 */
					$query = "SELECT summary.info_hash, 
						namemap.filename, 
						namemap.size, 
						namemap.DateAdded, 
						summary.seeds, 
						summary.leechers, 
						namemap.category,
						summary.hide_torrent,
						summary.external_torrent,
						namemap.grouping,
						namemap.sorting
						FROM summary LEFT JOIN namemap ON summary.info_hash = namemap.info_hash WHERE summary.info_hash = \"$hash\"";

					/*
					 * Do the query, get the row...
					 */
					$recordset = mysql_query($query) or sqlErr(mysql_error());		
					$row=mysql_fetch_row($recordset);

					/*
					 * If it's external delete it. You can't retire an external torrent.
					 * Otherwise retire as usual.
					 */
					if ($row[8] == "Y") {
						$deletelist[] = array('hash' => $hash,
												'action' => $action,
												'name' => $row[1],
												'size' => $row[2],
												'date' => $row[3],
												'seeders' => $row[4],
												'leechers' => $row[5],
												'category' => $row[6],
												'hide' => $row[7],
												'external' => $row[8],
												'grouping' => $row[9],
												'sorting' => $row[10]);
					} else {
						$retirelist[] = array('hash' => $hash,
												'action' => $action,
												'name' => $row[1],
												'size' => $row[2],
												'date' => $row[3],
												'seeders' => $row[4],
												'leechers' => $row[5],
												'category' => $row[6],
												'hide' => $row[7],
												'grouping' => $row[9],
												'sorting' => $row[10]);
					}
					break;
				case ACTION_UNHIDE:
					/*
					 * Build query string
					 */
					$query = "SELECT summary.info_hash, 
						namemap.filename, 
						namemap.size, 
						namemap.DateAdded, 
						summary.seeds, 
						summary.leechers, 
						namemap.category,
						summary.hide_torrent FROM summary LEFT JOIN namemap ON summary.info_hash = namemap.info_hash WHERE summary.info_hash = \"$hash\"";

					/*
					 * Do the query, get the row...
					 */
					$recordset = mysql_query($query) or sqlErr(mysql_error());		
					$row=mysql_fetch_row($recordset);

					$unhidelist[] = array('hash' => $hash,
												'action' => $action,
												'name' => $row[1],
												'size' => $row[2],
												'date' => $row[3],
												'seeders' => $row[4],
												'leechers' => $row[5],
												'category' => $row[6],
												'hide' => $row[7]);
					break;
				case ACTION_HIDE:
					/*
					 * Build query string
					 */
					$query = "SELECT summary.info_hash, 
						namemap.filename, 
						namemap.size, 
						namemap.DateAdded, 
						summary.seeds, 
						summary.leechers, 
						namemap.category,
						summary.hide_torrent FROM summary LEFT JOIN namemap ON summary.info_hash = namemap.info_hash WHERE summary.info_hash = \"$hash\"";

					/*
					 * Do the query, get the row...
					 */
					$recordset = mysql_query($query) or sqlErr(mysql_error());		
					$row=mysql_fetch_row($recordset);

					$hidelist[] = array('hash' => $hash,
												'action' => $action,
												'name' => $row[1],
												'size' => $row[2],
												'date' => $row[3],
												'seeders' => $row[4],
												'leechers' => $row[5],
												'category' => $row[6],
												'hide' => $row[7]);
					break;
				case ACTION_REVIVE:
					/*
					 * Build query string
					 */
					$query = "SELECT info_hash, 
						filename, 
						size, 
						DateAdded, 
						dateretired,
						category,
						completed,
						transferred,
						crc32 FROM retired WHERE info_hash = \"$hash\"";

					/*
					 * Do the query, get the row...
					 */
					$recordset = mysql_query($query) or sqlErr(mysql_error());		
					$row=mysql_fetch_row($recordset);

					$revivelist[] = array('hash' => $hash,
												'action' => $action,
												'name' => $row[1],
												'size' => $row[2],
												'crc' => $row[8],
												'date' => $row[3],
												'retired' => $row[4],
												'category' => $row[5],
												'completed' => $row[6],
												'transferred' => $row[7]);
					break;
				case ACTION_RDELETE:
					/*
					 * Build query string
					 */
					$query = "SELECT info_hash, 
						filename, 
						size, 
						DateAdded, 
						dateretired,
						category,
						completed,
						transferred FROM retired WHERE info_hash = \"$hash\"";

					/*
					 * Do the query, get the row...
					 */
					$recordset = mysql_query($query) or sqlErr(mysql_error());		
					$row=mysql_fetch_row($recordset);

					$rdeletelist[] = array('hash' => $hash,
												'action' => $action,
												'name' => $row[1],
												'size' => $row[2],
												'date' => $row[3],
												'retired' => $row[4],
												'category' => $row[5],
												'completed' => $row[6],
												'transferred' => $row[7]);
					break;
		}
	}

	/*
	 * Okay, they are seperated, now sort the 3 lists,
	 * and put them in a session variable for later use.
	 * First, unset the session variables, in case the screen has been
	 * used previously in the same session.
	 */
	unset($_SESSION['deletelist'], $_SESSION['retirelist'], $_SESSION['unhidelist'], $_SESSION['revivelist'], $_SESSION['rdeletelist'], $_SESSION['hidelist']);
	if (count($deletelist) > 0)
		$_SESSION['deletelist'] = array_sort($deletelist, "name");
	if (count($retirelist) > 0)
		$_SESSION['retirelist'] = array_sort($retirelist, "name");
	if (count($unhidelist) > 0)
		$_SESSION['unhidelist'] = array_sort($unhidelist, "name");
	if (count($hidelist) > 0)
		$_SESSION['hidelist'] = array_sort($hidelist, "name");
	if (count($revivelist) > 0)
		$_SESSION['revivelist'] = array_sort($revivelist, "name");
	if (count($rdeletelist) > 0)
		$_SESSION['rdeletelist'] = array_sort($rdeletelist, "name");
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
<FORM ENCTYPE="multipart/form-data" METHOD="POST" ACTION="bta_confirm.php">
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
	   <A HREF="help/help_confirm.php" TARGET="_blank">Need help?</A><BR>
	</TD>
</TR>
<TR>
<?php
	if (isset($_SESSION["retiredadmin"]))
		echo "\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_retired.php\">Return to Retired Torrents Administrative screen (no changes will be made).</A><BR>&nbsp;</TD>\r\n";
	else
		echo "\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_main.php\">Return to Main Administrative screen (no changes will be made).</A><BR></TD>\r\n";
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

	if (isset($_SESSION["unhidelist"])) {
		echo "\t\t<FONT SIZE=+2><B>UNHIDE the following:</B></FONT><BR>\r\n";
		echo "\t\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>Hash</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>File Name</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Size<BR>(MiB)</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Date<BR>Added</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>UL</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>DL<B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Category</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Torrent<BR>Status</B></TD>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Use to alternate rows.
		 */
		$rowCount = 0;

		/*
		 * Spit out the contents stored in the session variable.
		 */
		foreach ($_SESSION["unhidelist"] as $key => $value) {
			$useRowClass = $classRowBGClr[$rowCount % 2];
			echo "\t\t\t<TD $useRowClass>".$_SESSION["unhidelist"][$key]["hash"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass>".$_SESSION["unhidelist"][$key]["name"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["unhidelist"][$key]["size"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["unhidelist"][$key]["date"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["unhidelist"][$key]["seeders"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["unhidelist"][$key]["leechers"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["unhidelist"][$key]["category"]."</TD>\r\n";
			if ($_SESSION["unhidelist"][$key]["hide"] == 'Y')
				echo "\t\t\t<TD $useRowClass ALIGN=\"center\"><DIV CLASS=\"specialtag\">HIDDEN</DIV></TD>\r\n";
			else
				echo "\t\t\t<TD $useRowClass ALIGN=\"center\">Active</TD>\r\n";
			echo "\t\t</TR>\r\n";

			$rowCount++;
		}
		echo "\t\t</TABLE><BR><BR>\r\n";
	}

	if (isset($_SESSION["hidelist"])) {
		echo "\t\t<FONT SIZE=+2><B>HIDE the following:</B></FONT><BR>\r\n";
		echo "\t\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>Hash</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>File Name</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Size<BR>(MiB)</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Date<BR>Added</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>UL</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>DL<B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Category</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Torrent<BR>Status</B></TD>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Use to alternate rows.
		 */
		$rowCount = 0;

		/*
		 * Spit out the contents stored in the session variable.
		 */
		foreach ($_SESSION["hidelist"] as $key => $value) {
			$useRowClass = $classRowBGClr[$rowCount % 2];
			echo "\t\t\t<TD $useRowClass>".$_SESSION["hidelist"][$key]["hash"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass>".$_SESSION["hidelist"][$key]["name"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["hidelist"][$key]["size"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["hidelist"][$key]["date"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["hidelist"][$key]["seeders"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["hidelist"][$key]["leechers"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["hidelist"][$key]["category"]."</TD>\r\n";
			if ($_SESSION["hidelist"][$key]["hide"] == 'Y')
				echo "\t\t\t<TD $useRowClass ALIGN=\"center\"><DIV CLASS=\"specialtag\">HIDDEN</DIV></TD>\r\n";
			else
				echo "\t\t\t<TD $useRowClass ALIGN=\"center\">Active</TD>\r\n";
			echo "\t\t</TR>\r\n";

			$rowCount++;
		}
		echo "\t\t</TABLE><BR><BR>\r\n";
	}
	
	if (isset($_SESSION["retirelist"])) {
		echo "\t\t<FONT SIZE=+2><B>RETIRE the following:</B></FONT><BR>\r\n";
		echo "\t\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>Hash</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>File Name</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Size<BR>(MiB)</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Date<BR>Added</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>UL</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>DL<B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Category</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Torrent<BR>Status</B></TD>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Use to alternate rows.
		 */
		$rowCount = 0;

		/*
		 * Spit out the contents stored in the session variable.
		 */
		foreach ($_SESSION["retirelist"] as $key => $value) {
			$useRowClass = $classRowBGClr[$rowCount % 2];
			echo "\t\t\t<TD $useRowClass>".$_SESSION["retirelist"][$key]["hash"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass>".$_SESSION["retirelist"][$key]["name"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["retirelist"][$key]["size"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["retirelist"][$key]["date"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["retirelist"][$key]["seeders"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["retirelist"][$key]["leechers"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["retirelist"][$key]["category"]."</TD>\r\n";
			if ($_SESSION["retirelist"][$key]["hide"] == 'Y')
				echo "\t\t\t<TD $useRowClass ALIGN=\"center\"><DIV CLASS=\"specialtag\">HIDDEN</DIV></TD>\r\n";
			else
				echo "\t\t\t<TD $useRowClass ALIGN=\"center\">Active</TD>\r\n";
			echo "\t\t</TR>\r\n";

			$rowCount++;
		}
		echo "\t\t</TABLE><BR><BR>\r\n";
	}

	if (isset($_SESSION["deletelist"])) {
		echo "\t\t<FONT SIZE=+2><B>DELETE the following:</B></FONT><BR>\r\n";
		echo "\t\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>Hash</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>File Name</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Size<BR>(MiB)</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Date<BR>Added</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>UL</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>DL<B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Category</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Torrent<BR>Status</B></TD>\r\n";
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
			echo "\t\t\t<TD $useRowClass>".$_SESSION["deletelist"][$key]["hash"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass>".$_SESSION["deletelist"][$key]["name"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["deletelist"][$key]["size"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["deletelist"][$key]["date"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["deletelist"][$key]["seeders"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["deletelist"][$key]["leechers"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["deletelist"][$key]["category"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">";
			if ($_SESSION["deletelist"][$key]["external"] == 'Y')
				echo "<DIV CLASS=\"specialtag\">External</DIV>";

			if ($_SESSION["deletelist"][$key]["hide"] == 'Y')
				echo "<DIV CLASS=\"specialtag\">HIDDEN</DIV>";
			else
				echo "Active";
			echo "</TD>\r\n";
			echo "\t\t</TR>\r\n";

			$rowCount++;
		}
		echo "\t\t</TABLE><BR><BR>\r\n";
	}

	if (isset($_SESSION["revivelist"])) {
		echo "\t\t<FONT SIZE=+2><B>REVIVE the following:</B></FONT><BR>";
		echo "\t\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>Hash</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>File Name</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Size<BR>(MiB)</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Date<BR>Added</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Date<BR>Retired</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Category</B></TD>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Use to alternate rows.
		 */
		$rowCount = 0;

		/*
		 * Spit out the contents stored in the session variable.
		 */
		foreach ($_SESSION["revivelist"] as $key => $value) {
			$useRowClass = $classRowBGClr[$rowCount % 2];
			echo "\t\t\t<TD $useRowClass>".$_SESSION["revivelist"][$key]["hash"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass>".$_SESSION["revivelist"][$key]["name"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["revivelist"][$key]["size"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["revivelist"][$key]["date"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["revivelist"][$key]["retired"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["revivelist"][$key]["category"]."</TD>\r\n";
			echo "\t\t</TR>";

			$rowCount++;
		}
		echo "\t\t</TABLE><BR><BR>\r\n";
	}

	if (isset($_SESSION["rdeletelist"])) {
		echo "\t\t<FONT SIZE=+2><B>DELETE the following RETIRED torrents:</B></FONT><BR>";
		echo "\t\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>Hash</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\"><B>File Name</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Size<BR>(MiB)</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Date<BR>Added</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Date<BR>Retired</B></TD>\r\n";
		echo "\t\t\t<TD CLASS=\"heading\" VALIGN=\"bottom\" ALIGN=\"center\"><B>Category</B></TD>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Use to alternate rows.
		 */
		$rowCount = 0;

		/*
		 * Spit out the contents stored in the session variable.
		 */
		foreach ($_SESSION["rdeletelist"] as $key => $value) {
			$useRowClass = $classRowBGClr[$rowCount % 2];
			echo "\t\t\t<TD $useRowClass>".$_SESSION["rdeletelist"][$key]["hash"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass>".$_SESSION["rdeletelist"][$key]["name"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["rdeletelist"][$key]["size"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["rdeletelist"][$key]["date"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["rdeletelist"][$key]["retired"]."</TD>\r\n";
			echo "\t\t\t<TD $useRowClass ALIGN=\"center\">".$_SESSION["rdeletelist"][$key]["category"]."</TD>\r\n";
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
	if (isset($_SESSION["retiredadmin"]))
		echo "\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_retired.php\">Return to Retired Torrents Administrative screen (no changes will be made).</A><BR>&nbsp;</TD>\r\n";
	else
		echo "\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_main.php\">Return to Main Administrative screen (no changes will be made).</A><BR>&nbsp;</TD>\r\n";
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