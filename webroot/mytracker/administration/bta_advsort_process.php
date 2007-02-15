<?php
	/*
	 * Module:	bta_advsort_confirm.php
	 * Description: This is the confirm operation screen of the advanced sort interface.
	 * 		This module displays selected torrents from the sort interface and asks the
	 * 		user to supply additional information if needed.
	 *
	 * Author:	danomac
	 * Written:	7-Aug-2005
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
	require_once ("../version.php");
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
	if (!($_SESSION["admin_perms"]["advsort"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Redirecting to the main administration panel.",
			       $adm_page_title, true, "bta_main.php", 3);
	}

	/*
	 * For the URL operations that use _GET. 
	 */
	if (isset($_GET["action"])) {
		/*
		 * Connect to the database
		 */
		if ($GLOBALS["persist"])
			$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		else
			$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		mysql_select_db($database) or sqlErr(mysql_error());

		/*
		 * Category decision: need to figure out which session variable to use
		 */
		if ($_SESSION["admin_perms"]["root"])
			$category = $_SESSION["root_last_cat"];
		else
			$category = $_SESSION["admin_perms"]["category"];

		/*
		 * Check to see what the script is supposed to do.
		 */
		switch ($_GET["action"]) {
			case "grpdel":
				/*
				 * Requires the group_id to be specified
				 */
				if (!isset($_GET["group"]) || !is_numeric($_GET["group"])) {
					admShowMsg("Invalid group specified","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
					exit;				
				}
				
				/*
				 * In order to do this *properly*, all the torrents need to get dumped into the orphaned torrents group.
				 * Then we can go ahead and toast the category.
				 */
				$rstTorrents = @mysql_query("SELECT `info_hash`, `grouping`, `sorting` FROM `namemap` WHERE `grouping` = ".$_GET["group"]." ORDER BY `sorting`");
				
				if ($rstTorrents === false) {
					admShowMsg("There was an error trying to delete the group","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
				} else {
					/*
					 * Now we have a list of these, we need to get the max value of the sorting for the orphaned torrent group.
					 */
					$rstAdvSort = @mysql_query("SELECT MAX(`sorting`) FROM `namemap` WHERE `grouping` = 0 and `category` = \"$category\"");
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
					
					/*
					 * Go and move the torrents into the orphaned pool
					 */
					while ($rowTorrent = mysql_fetch_row($rstTorrents)) {
						/*
						 * We don't need to worry about reordering the group we are removing these from cause it's getting deleted :)
						 */
						@mysql_query("UPDATE `namemap` SET `grouping` = 0, `sorting` = $advsort WHERE `info_hash`=\"$rowTorrent[0]\"");
						$advsort++;
					}
					
					/*
					 * Finally, we can remove the category itself
					 */
					@mysql_query("DELETE FROM `subgrouping` WHERE `group_id` = ".$_GET["group"]);
				 }
				
				admShowMsg("All torrents were ungrouped and the group removed","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
				exit;
				break;
			case "grpautosort":
				/*
				 * Requires the group_id to be specified.  All this does is sort the torrents by name in the specified group.
				 */
				if (!isset($_GET["group"]) || !is_numeric($_GET["group"])) {
					admShowMsg("Invalid group specified","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
					exit;				
				}
				advSortResortGrp($category, $_GET["group"]);
				admShowMsg("Resorting completed","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
				exit;
				break;
			case "rootsort":
				/*
				 * Requires root access
				 */
				if (!isset($_SESSION["admin_perms"]["root"])) {
					admShowMsg("Not allowed","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
					exit;								
				}
				advSortResortAll();
				admShowMsg("Global resort complete","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
				exit;				
				break;
			case "rootsortall":
				/*
				 * Requires root access
				 */
				if (!isset($_SESSION["admin_perms"]["root"])) {
					admShowMsg("Not allowed","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
					exit;								
				}
				advSortResortAll();
				advSortResortAllGroupNames();
				admShowMsg("Global resort complete","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
				exit;				
				break;
			case "grpsort":
				/*
				 * Sorts torrents only in category
				 */
				advSortResortCategory($category);
				admShowMsg("Resorting finished","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
				exit;
				break;
			case "grpsortall":
				/*
				 * Sorts torrents and group names
				 */
				advSortResortCategory($category);
				advSortResortGroupNames($category);
				admShowMsg("Resorting complete","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
				exit;
				break;
			case "grponlysort":
				/*
				 * Sorts group names only
				 */
				advSortResortGroupNames($category);
				admShowMsg("Resorting of group headings complete","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
				exit;
				break;
			case "mvtorrent":
				/*
				 * Requires the info hash to be specified.
				 */
				if (!isset($_GET["info_hash"]) || strpos($_GET["info_hash"], " ") !== false) {
					admShowMsg("Invalid info_hash specified","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
					exit;				
				}

				/*
				 * Let's grab some information on the torrent.
				 */
				$rstTorrent = @mysql_query("SELECT `grouping`, `sorting`, `filename` FROM `namemap` WHERE `info_hash` = \"".$_GET["info_hash"]."\"");
				if ($rstTorrent === false) {
					admShowMsg("Internal error - torrent not found","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
				} else {
					$rowTorrent = mysql_fetch_row($rstTorrent);
					list($group, $oldpos, $fname, $info_hash) = $rowTorrent;
					
					if (is_null($fname) || strlen($fname) == 0) {
						$fname = $_GET["info_hash"];
					}
					
					/*
					 * Check to see if the torrent selected is first. If it is, we don't want to show that option; 
					 * the tables will get messed up.
					 */
					if ($oldpos == 1) {
						$firstgrp = true;
					} else {
						$firstgrp = false;
					}
					
					/*
					 * Grab the other torrents from the group
					 */
					$rstTorrentList = @mysql_query("SELECT `grouping`, `sorting`, `filename`, `info_hash` FROM `namemap` WHERE `category` = \"".$category."\" AND `grouping` = $group AND `info_hash` != \"".$_GET["info_hash"]."\"ORDER BY `sorting`");
					if ($rstTorrentList === false) {
						admShowMsg("Internal error - unable to get torrent list","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
					} else {
						if (mysql_num_rows($rstTorrentList) == 0) {
							admShowMsg("This is the only torrent in the group. You cannot move it.","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
						} else {
							/*
							 * Check to see if the group selected is first. If it is, we don't want to show that option; 
							 * the tables will get messed up. We'll also grab the group title to display here.
							 */
							echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n<HTML>\r\n<HEAD>\r\n\t<META NAME=\"Author\" CONTENT=\"danomac\">\r\n";
							echo "\t<LINK REL=\"stylesheet\" HREF=\"admin.css\" TYPE=\"text/css\" TITLE=\"Default\">\r\n";
							echo "\t<TITLE>". $adm_page_title . " - Move torrent to?</TITLE>\r\n</HEAD>\r\n\r\n<BODY>\r\n";
							echo "<FORM ENCTYPE=\"multipart/form-data\" METHOD=\"POST\" ACTION=\"bta_advsort_process.php\">\r\n";
							echo "<TABLE CLASS=\"tblAdminOuter\">\r\n<TR>\r\n";
							echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>Move torrent</TD>\r\n</TR>\r\n";
							echo "<TR>\r\n\t<TD CLASS=\"data\" COLSPAN=15 ALIGN=CENTER><BR><A HREF=\"help/help_advsort_mvtorrent.php\" TARGET=\"_blank\">Need help?</A><BR>&nbsp;</TD>\r\n</TR>\r\n";
							echo "<TR>\r\n\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_advsort.php\">Return to Torrent Sorting screen (no changes will be made).</A><BR>&nbsp;</TD>\r\n</TR>\r\n";
							echo "<TR>\r\n\t<TD COLSPAN=15 ALIGN=CENTER><B>Move <FONT COLOR=RED><I>$fname</I></FONT><BR> to:</B>\r\n";
	
							echo "\t<SELECT NAME=\"torrentlocation\">\r\n";
							
							
							if (!$firstgrp) {
								echo "\t<OPTION VALUE=\"0\">First</OPTION>\r\n";
							}
	
							while ($rowGrp = mysql_fetch_row($rstTorrentList)) {
								if (is_null($row[2]) || strlen($row[2]) == 0) {
									$row[2] = $row[3];
								}
								
								if ($rowGrp[1] != $oldpos - 1) {
									echo "\t<OPTION VALUE=\"$rowGrp[1]\">Below $rowGrp[2]</OPTION>\r\n";
								}
							}
							
							echo "\t</SELECT><BR>&nbsp;\r\n\t</TD>\r\n</TR>\r\n";
							echo "<TR>\r\n\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_advsort.php\">Return to Torrent Sorting screen (no changes will be made).</A><BR>&nbsp;</TD>\r\n</TR>\r\n";
							echo "<TR>\r\n<TD COLSPAN=15 ALIGN=CENTER><INPUT TYPE=\"submit\" NAME=\"advsort_move_torrent\" VALUE=\"Move torrent\" CLASS=\"button\"><INPUT TYPE=HIDDEN NAME=\"info_hash\" VALUE=\"".$_GET["info_hash"]."\"><INPUT TYPE=HIDDEN NAME=\"group\" VALUE=\"$group\"></TD>\r\n</TR>\r\n";
							echo "</TABLE>\r\n</FORM>\r\n</BODY>\r\n</HTML>";
							exit;
						}
					}
				}
				exit;
				break;
			case "mvgrp":
				/*
				 * Requires the group_id to be specified.
				 */
				if (!isset($_GET["group"]) || !is_numeric($_GET["group"])) {
					admShowMsg("Invalid group specified","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
					exit;				
				}
				
				/*
				 * Let's grab some information on the group.
				 */
				$rstGrp = @mysql_query("SELECT `groupsort`, `heading` FROM `subgrouping` WHERE `group_id` != ".$_GET["group"]." AND `category` = \"$category\" ORDER BY `groupsort`");
				if ($rstGrp === false) {
					admShowMsg("Internal error - group not found","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
				} else {
					if (mysql_num_rows($rstGrp) == 0) {
						admShowMsg("This is the only group. You cannot move it.","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
					} else {
						/*
						 * Check to see if the group selected is first. If it is, we don't want to show that option; 
						 * the tables will get messed up. We'll also grab the group title to display here.
						 */
						$rstFirst = mysql_query("SELECT `groupsort`, `heading` FROM `subgrouping` WHERE `group_id` = " . $_GET["group"] ." AND `category` = \"$category\"");
						if ($rstFirst === false) {
							$firstgrp = false;
						} else {
							$rowFirst = mysql_fetch_row($rstFirst); 
							list($grouporder, $grpheading) = $rowFirst;
							
							if ($grouporder == 1) {
								$firstgrp = true;
							} else {
								$firstgrp = false;
							}
						
						}
					
						echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n<HTML>\r\n<HEAD>\r\n\t<META NAME=\"Author\" CONTENT=\"danomac\">\r\n";
						echo "\t<LINK REL=\"stylesheet\" HREF=\"admin.css\" TYPE=\"text/css\" TITLE=\"Default\">\r\n";
						echo "\t<TITLE>". $adm_page_title . " - Move group to?</TITLE>\r\n</HEAD>\r\n\r\n<BODY>\r\n";
						echo "<FORM ENCTYPE=\"multipart/form-data\" METHOD=\"POST\" ACTION=\"bta_advsort_process.php\">\r\n";
						echo "<TABLE CLASS=\"tblAdminOuter\">\r\n<TR>\r\n";
						echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>Move group</TD>\r\n</TR>\r\n";
						echo "<TR>\r\n\t<TD CLASS=\"data\" COLSPAN=15 ALIGN=CENTER><BR><A HREF=\"help/help_advsort_mvgrp.php\" TARGET=\"_blank\">Need help?</A><BR>&nbsp;</TD>\r\n</TR>\r\n";
						echo "<TR>\r\n\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_advsort.php\">Return to Torrent Sorting screen (no changes will be made).</A><BR>&nbsp;</TD>\r\n</TR>\r\n";
						echo "<TR>\r\n\t<TD COLSPAN=15 ALIGN=CENTER><B>Move <FONT COLOR=RED><I>$grpheading</I></FONT><BR> to:</B>\r\n";

						echo "\t<SELECT NAME=\"grouplocation\">\r\n";
						
						
						if (!$firstgrp) {
							echo "\t<OPTION VALUE=\"0\">First<SUP>*</SUP></OPTION>\r\n";
						}

						while ($rowGrp = mysql_fetch_row($rstGrp)) {
							if ($rowGrp[0] != $grouporder - 1) {
								echo "\t<OPTION VALUE=\"$rowGrp[0]\">Below $rowGrp[1]</OPTION>\r\n";
							}
						}
						
						echo "\t</SELECT><BR>&nbsp;\r\n\t</TD>\r\n</TR>\r\n";
						echo "<TR>\r\n\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_advsort.php\">Return to Torrent Sorting screen (no changes will be made).</A><BR><BR><SUP>*</SUP>: Ungrouped torrents always show first!<BR>&nbsp;</TD>\r\n</TR>\r\n";
						echo "<TR>\r\n<TD COLSPAN=15 ALIGN=CENTER><INPUT TYPE=\"submit\" NAME=\"advsort_move_group\" VALUE=\"Move group\" CLASS=\"button\"><INPUT TYPE=HIDDEN NAME=\"groupid\" VALUE=\"".$_GET["group"]."\"</TD>\r\n</TR>\r\n";
						echo "</TABLE>\r\n</FORM>\r\n</BODY>\r\n</HTML>";
						exit;
					}
				}
				
				exit;
				break;
			default:
				admShowMsg("Invalid action specified","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
				exit;
				break;
		}
		exit;
	}

	/*
	 * Deal with information submitted via a form
	 */
	if (isset($_POST)) {
		/*
		 * Connect to the database
		 */
		if ($GLOBALS["persist"])
			$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		else
			$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		mysql_select_db($database) or sqlErr(mysql_error());

		/*
		 * Move a torrent?
		 */
		if (isset($_POST["advsort_move_torrent"])) {
			/*
			 * Category decision: need to figure out which session variable to use
			 */
			if ($_SESSION["admin_perms"]["root"])
				$category = $_SESSION["root_last_cat"];
			else
				$category = $_SESSION["admin_perms"]["category"];

			/*
			 * We need to make a new spot available, then check where the old torrent has moved to,
			 * then move it there and remove the old location in the list.
			 */
			$newindex = $_POST["torrentlocation"] + 1;
			@mysql_query("UPDATE `namemap` SET `sorting` = `sorting` + 1 
									WHERE `category` = \"$category\" AND `grouping` = ". $_POST["group"] ." AND `sorting` >= $newindex");

			/*
			 * Get the new sort index of the group ID we are moving. We'll need this to remove a "hole" later.
			 */
			$rstOldTorrent = @mysql_query("SELECT `sorting` FROM `namemap` WHERE `info_hash` = \"" . $_POST["info_hash"] . "\"");
			if ($rstOldTorrent === false) {
				admShowMsg("Internal error (-1), cannot move torrent","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
			} else {
				$oldindex = mysql_result($rstOldTorrent, 0, 0);
			}
			
			/*
			 * Update the group with the new sort index
			 */
			mysql_query("UPDATE `namemap` SET `sorting` = $newindex WHERE `info_hash` = \"" . $_POST["info_hash"] . "\"");

			/*
			 * Then we need to remove the old index.
			 */
			@mysql_query("UPDATE `namemap` SET `sorting` = `sorting` - 1 
									WHERE `category` = \"$category\" AND `grouping` = ". $_POST["group"] ." AND `sorting` >= $oldindex");
			
			admShowMsg("Move completed.","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
			exit;
		}

		/*
		 * Move a group?
		 */
		if (isset($_POST["advsort_move_group"])) {
			/*
			 * Category decision: need to figure out which session variable to use
			 */
			if ($_SESSION["admin_perms"]["root"])
				$category = $_SESSION["root_last_cat"];
			else
				$category = $_SESSION["admin_perms"]["category"];
		
			/*
			 * We need to make a new spot available, then check where the old group has moved to,
			 * then move it there and remove the old location in the list.
			 */
			$newindex = $_POST["grouplocation"] + 1;
			@mysql_query("UPDATE `subgrouping` SET `groupsort` = `groupsort` + 1 
									WHERE `category` = \"$category\" AND `groupsort` >= $newindex");
			
			
			/*
			 * Get the new sort index of the group ID we are moving. We'll need this to remove a "hole" later.
			 */
			$rstOldGroup = @mysql_query("SELECT `groupsort` FROM `subgrouping` WHERE `group_id` = " . $_POST["groupid"]);
			if ($rstOldGroup === false) {
				admShowMsg("Internal error (-1), cannot move group","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
			} else {
				$oldindex = mysql_result($rstOldGroup, 0, 0);
			}
			
			/*
			 * Update the group with the new sort index
			 */
			mysql_query("UPDATE `subgrouping` SET `groupsort` = $newindex WHERE `group_id` = " . $_POST["groupid"]);

			/*
			 * Then we need to remove the old index.
			 */
			@mysql_query("UPDATE `subgrouping` SET `groupsort` = `groupsort` - 1 
									WHERE `category` = \"$category\" AND `groupsort` >= $oldindex");
			
			admShowMsg("Move completed.","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
			exit;
		}
		
		/*
		 * See if a new group is supposed to be added. If it is, add it and ignore other requests...
		 */
		if (isset($_POST["addnewgrp"])) {
			if (!isset($_POST["addgrp"]) || strlen(trim($_POST["addgrp"])) == 0) {
				admShowMsg("Please specify a group name","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
				exit;
			}

			/*
			 * Category decision: need to figure out which session variable to use
			 */
			if ($_SESSION["admin_perms"]["root"])
				$category = $_SESSION["root_last_cat"];
			else
				$category = $_SESSION["admin_perms"]["category"];

			/*
			 * We need to add at the END of the group names
			 */
			$rstAdvSort = @mysql_query("SELECT MAX(`groupsort`) FROM `subgrouping` WHERE `category` = \"$category\"");
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
			
			/*
			 * Add the new group
			 */
			@mysql_query("INSERT INTO `subgrouping` (`heading`, `groupsort`, `category`) VALUES (\"".$_POST["addgrp"]."\", $advsort, \"$category\")");
			
			admShowMsg("New group added","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);		
			exit;
		}
		
		/*
		 * If step 2 is set, we need to go through and group/ungroup as requested.
		 */
		if (isset($_POST["advsort_confirmation_step2"])) {
			if (isset($_SESSION["ungrouplist"])) {
				/*
				 * Ungroup requested torrents
				 */
				foreach($_SESSION["ungrouplist"] as $key => $value) {
					/*
					 * Due to this script rearranging things, we must look up the new index numbers or the tables will get
					 * REALLY disorganized!
					 */
					$rstNewSorting = @mysql_query("SELECT `sorting` FROM `namemap` WHERE `info_hash`=\"".$_SESSION["ungrouplist"][$key]["hash"]."\"");
					if ($rstNewSorting === false)
						$newSort = $_SESSION["ungrouplist"][$key]["sorting"];
					else
						$newSort = mysql_result($rstNewSorting, 0, 0);

					/*
					 * We need to add at the END of the ungroup list
					 */
					$rstAdvSort = @mysql_query("SELECT MAX(`sorting`) FROM `namemap` WHERE `grouping` = 0 and `category` = \"".$_SESSION["ungrouplist"][$key]["category"]."\"");
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
	
					/*
					 * First, ungroup it, and add it to the end of the orphaned torrents
					 */
					@mysql_query("UPDATE `namemap` SET `grouping` = 0, `sorting` = $advsort WHERE `info_hash`=\"".$_SESSION["ungrouplist"][$key]["hash"]."\"");

					/*
					 * Then reorder the old group it was in
					 */
					advSortDelete($_SESSION["ungrouplist"][$key]["category"], $_SESSION["ungrouplist"][$key]["grouping"], $newSort);
				}
			}
			
			
			if (isset($_SESSION["grouplist"])) {
				/*
				 * Check to see if a group was even selected
				 */
				if (!isset($_POST["groupname"]) || $_POST["groupname"] == 0) {
					admShowMsg("No group selected, skipping","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
					exit;
				}

				/*
				 * Category decision: need to figure out which session variable to use
				 */
				if ($_SESSION["admin_perms"]["root"])
					$category = $_SESSION["root_last_cat"];
				else
					$category = $_SESSION["admin_perms"]["category"];

				/*
				 * We need to add at the END of the group, taking into account the category decision above...
				 */
				$rstAdvSort = @mysql_query("SELECT MAX(`sorting`) FROM `namemap` WHERE `grouping` = ". $_POST["groupname"] ." and `category` = \"$category\"");
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

				/*
				 * Group requested torrents
				 */
				foreach($_SESSION["grouplist"] as $key => $value) {
					/*
					 * Due to this script rearranging things, we must look up the new index numbers or the tables will get
					 * REALLY disorganized!
					 */
					$rstNewSorting = @mysql_query("SELECT `sorting` FROM `namemap` WHERE `info_hash`=\"".$_SESSION["grouplist"][$key]["hash"]."\"");
					if ($rstNewSorting === false)
						$newSort = $_SESSION["grouplist"][$key]["sorting"];
					else
						$newSort = mysql_result($rstNewSorting, 0, 0);
					
					/*
					 * First, group it in the requested group adding it to the end of the torrents
					 */
					@mysql_query("UPDATE `namemap` SET `grouping` = ". $_POST["groupname"] .", `sorting` = $advsort WHERE `info_hash`=\"".$_SESSION["grouplist"][$key]["hash"]."\"");
					
					/*
					 * Then reorder the old group it was in
					 */
					advSortDelete($_SESSION["grouplist"][$key]["category"], 0, $newSort);

					/*
					 * Increment the advanced sort counter, to keep everything in order!
					 */
					$advsort++;
				}
			}

			admShowMsg("Update complete","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
			exit;
		}
		
		/*
		 * If processbuttonstep1 is set, this means it is the first step from the main sorting page...
		 */
		if (isset($_POST["processbuttonstep1"])) {
			/*
			 * Commit the changes to the headings
			 */
			if (isset($_POST["hdtitle"])) {
				foreach ($_POST["hdtitle"] as $id => $value) {
					@mysql_query("UPDATE `subgrouping` SET `heading`=\"$value\" WHERE `group_id` = $id");
				}
			}
			
			if (!isset($_POST["group_action"])) {
				admShowMsg("Update complete","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);
			} else {
				foreach ($_POST["group_action"] as $hash => $action) {
					/*
					 * Design choice: I decided to split up the operations by action. So,
					 * tables will be presented below with the actions grouped together. (i.e.
					 * there will be a table listing items selected for grouping, a seperate table
					 * for listing items to be ungrouping.)
					 *
					 * To do this, they will be broken up into seperate arrays and populated
					 * with items grabbed from mysql. The arrays will be sorted by name using
					 * a function I threw together to sort multidimensional arrays.
					 */
					switch ($action) {
						case ACTION_SORT_GROUP:
							/*
							 * Build query string
							 */
							$query = "SELECT `info_hash`, 
								`filename`, 
								`category`,
								`grouping`,
								`sorting`
								FROM `namemap` WHERE `info_hash` = \"$hash\"";
		
							/*
							 * Do the query, get the row...
							 */
							$recordset = mysql_query($query) or sqlErr(mysql_error());		
							$row=mysql_fetch_row($recordset);
		
							$grouplist[] = array('hash' => $hash,
														'action' => $action,
														'name' => $row[1],
														'category' => $row[2],
														'grouping' => $row[3],
														'sorting' => $row[4]);
							break;
						case ACTION_SORT_UNGROUP:
							/*
							 * Build query string
							 */
							$query = "SELECT `info_hash`, 
								`filename`, 
								`category`,
								`grouping`,
								`sorting`
								FROM `namemap` WHERE `info_hash` = \"$hash\"";
		
							/*
							 * Do the query, get the row...
							 */
							$recordset = mysql_query($query) or sqlErr(mysql_error());		
							$row=mysql_fetch_row($recordset);
		
							$ungrouplist[] = array('hash' => $hash,
													'action' => $action,
													'name' => $row[1],
													'category' => $row[2],
													'grouping' => $row[3],
													'sorting' => $row[4]);
							break;
					}
				}
			
				/*
				 * Okay, they are seperated, now sort the 3 lists,
				 * and put them in a session variable for later use.
				 * First, unset the session variables, in case the screen has been
				 * used previously in the same session.
				 */
				unset($_SESSION['grouplist'], $_SESSION['ungrouplist']);
				if (count($grouplist) > 0)
					$_SESSION['grouplist'] = array_sort($grouplist, "name");
				if (count($ungrouplist) > 0)
					$_SESSION['ungrouplist'] = array_sort($ungrouplist, "name");
					
				if (count($grouplist) == 0 && count($ungrouplist) ==0) {
					admShowMsg("Update complete","Redirecting to advanced sorting page.","Redirecting", true, "bta_advsort.php", 3);				
				}
			}
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
	echo "<TITLE>". $adm_page_title . " - Confirm selections</TITLE>\r\n";
?>
</HEAD>

<BODY>
<FORM ENCTYPE="multipart/form-data" METHOD="POST" ACTION="bta_advsort_process.php">
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
	   <A HREF="help/help_advsort_confirm.php" TARGET="_blank">Need help?</A><BR>
	</TD>
</TR>
<TR>
<?php
	echo "\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_advsort.php\">Return to Torrent Sorting screen (no changes will be made).</A><BR></TD>\r\n";
?>
</TR>
<TR>
	<TD COLSPAN=15>
		<FONT SIZE=+2>You have elected to:</FONT><BR><BR>
<?php
	/*
	 * These are the alternating Cascadying Style Sheet classes used for the data.
	 */
	$classRowBGClr[0] = 'CLASS="advSortOdd"';
	$classRowBGClr[1] = 'CLASS="advSortEven"';

	if (isset($_SESSION["ungrouplist"])) {
		echo "\t\t<FONT SIZE=+2><B>Ungroup the following:</B></FONT><BR>\r\n";
		echo "\t\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TD CLASS=\"advSortHeading\" VALIGN=\"bottom\"><B>File Name</B></TD>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Use to alternate rows.
		 */
		$rowCount = 0;

		/*
		 * Spit out the contents stored in the session variable.
		 */
		foreach ($_SESSION["ungrouplist"] as $key => $value) {
			$useRowClass = $classRowBGClr[$rowCount % 2];
			echo "\t\t\t<TD $useRowClass>".$_SESSION["ungrouplist"][$key]["name"]."</TD>\r\n";
			echo "\t\t</TR>\r\n";

			$rowCount++;
		}
		echo "\t\t</TABLE><BR><BR>\r\n";
	}

	if (isset($_SESSION["grouplist"])) {
		/*
		 * We better build a list of groups first...
		 */
		if ($_SESSION["admin_perms"]["root"])
			$category = $_SESSION["root_last_cat"];
		else
			$category = $_SESSION["admin_perms"]["category"];
		
		$rstGrpList = @mysql_query("SELECT `group_id`, `heading` FROM `subgrouping` WHERE `category` = \"$category\" ORDER BY `heading`");
		
		if ($rstGrpList === false) {
			$grpCombo = "<FONT COLOR=RED>ERROR: can't get a group list!</FONT>";
		} else {
			if (mysql_num_rows($rstGrpList) == 0) {
				$grpCombo = "<FONT COLOR=RED>ERROR: you need to add a group first!</FONT>";
			} else {
				$grpCombo = "\t\t<SELECT NAME=\"groupname\"><OPTION VALUE=\"0\">.: Choose a group :.</OPTION>";
				while ($rowGrp = mysql_fetch_row($rstGrpList)) {
					$grpCombo .= "<OPTION VALUE=\"$rowGrp[0]\">$rowGrp[1]</OPTION>";
				}
				$grpCombo .= "</SELECT>\r\n";
			}
		}
		
		echo "\t\t<FONT SIZE=+2><B>GROUP the following in:</B></FONT>&nbsp;$grpCombo<BR>\r\n";
		echo "\t\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
		echo "\t\t<TR>\r\n";
		echo "\t\t\t<TD CLASS=\"advSortHeading\" VALIGN=\"bottom\"><B>File Name</B></TD>\r\n";
		echo "\t\t</TR>\r\n";

		/*
		 * Use to alternate rows.
		 */
		$rowCount = 0;

		/*
		 * Spit out the contents stored in the session variable.
		 */
		foreach ($_SESSION["grouplist"] as $key => $value) {
			$useRowClass = $classRowBGClr[$rowCount % 2];
			echo "\t\t\t<TD $useRowClass>".$_SESSION["grouplist"][$key]["name"]."</TD>\r\n";
			echo "</TD>\r\n";
			echo "\t\t</TR>\r\n";

			$rowCount++;
		}
		echo "\t\t</TABLE><BR><BR>\r\n";
	}
?>
	</TD>
</TR>
<TR>
<?php
	echo "\t<TD COLSPAN=15 ALIGN=\"center\"><A HREF=\"bta_advsort.php\">Return to Torrent Sorting screen (no changes will be made).</A><BR>&nbsp;</TD>\r\n";
?>
</TR>
<TR>
	<TD COLSPAN=15 ALIGN="center">If the information above is correct, click the <I>Confirm and process</I> button below to proceed.</TD>
</TR>
<TR>
	<TD COLSPAN=15 ALIGN="center"><INPUT TYPE="submit" NAME="advsort_confirmation_step2" VALUE="Confirm and process" CLASS="button"></TD>
</TR>
</TABLE>
</FORM>
</BODY>
</HTML>