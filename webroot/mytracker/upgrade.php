<?php
	/*
	 * Module:	upgrade.php
	 * Description: This script upgrades an existing database for use.
	 *
	 * Author:	danomac
	 * Written:	06-May-2005
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

	define (NO_SEL, 0);
	define (PHPBTP16, 1);
	define (PHPBTP17, 2);
	define (PHPBT15, 3);
	define (PHPBTP20, 4);
	define (PHPBTP21, 5);

	/*
	 * Pre-Upgrade functions. These ask questions to the user pertaining to specifics
	 * regarding upgrading to the new version, and can vary version to version.
	 */
	function pre_upgr_phpbttrkplus16() {
		echo "\tUpgrading from: PHPBTTracker+ 1.6<BR><BR>\r\n";
		echo "\tNow for a few version-specific questions/issues:<BR><BR>\r\n";

		echo "\t<B><U>Upgrading the IP Banning section</U></B><BR><BR>\r\n";
		echo "\tThis script needs to update the tables to a new format. This script will need to either make changes to the existing\r\n";
		echo "\tban records or remove them permanently to make the change.<BR><BR>\r\n";
		echo "\tWhat do you want the script to do?<BR>\r\n";
		echo "\t&nbsp;&nbsp;&nbsp;<INPUT TYPE=RADIO NAME=\"ban_q\" VALUE=1 CHECKED> Do the changes and attempt to update the table data<BR>\r\n";
		echo "\t&nbsp;&nbsp;&nbsp;<INPUT TYPE=RADIO NAME=\"ban_q\" VALUE=2> Remove the bans permanently and make the structure changes<BR><BR>\r\n";

		echo "\t<B><U>Upgrading the usernames and category names</U></B><BR><BR>\r\n";
		echo "\tThis script will change any spaces in usernames and category names to underscores (_).\r\n";
		echo "\t<B>You will have to notify your users and possibly update your scripts to reflect this change.</B><BR><BR>\r\n";

		echo "\t<B><U>You need to set a root user/password in config.php</U></B><BR><BR>\r\n";
		echo "\tYou need to set two new variables in config.php (admin_user and admin_pass) to be able to login to the admin interface.<BR><BR>\r\n";

		echo "\t<B><U>Cleaning up some internal tracker tables</U></B><BR><BR>\r\n";
		echo "\tThe new administration scripts will keep the internal tracker tables clean; however there is a need for a one-time\r\n";
		echo "\tsweep through them to ensure they are clean (if you go through a lot of torrents on the tracker there are remnants of\r\n";
		echo "\tthem lying around.) <B>WARNING:</B> If this part of the script fails, your tracker could be left in an unusable state!</B><BR><BR>\r\n";
		echo "\tWhat do you want the script to do?<BR>\r\n";
		echo "\t&nbsp;&nbsp;&nbsp;<INPUT TYPE=RADIO NAME=\"timestamps_q\" VALUE=1 CHECKED> No, don't do clean up<BR>\r\n";
		echo "\t&nbsp;&nbsp;&nbsp;<INPUT TYPE=RADIO NAME=\"timestamps_q\" VALUE=2> Yes, do the clean up<BR><BR>\r\n";

		echo "\t<B><U>Database Username/Password/Database Name</U></B><BR><BR>\r\n";
		echo "\tEnter the database details below. Use the username and password you use to logon to the Administrative interface as root.<BR><BR>\r\n";

		echo "\tDatabase name: <INPUT TYPE=\"text\" NAME=\"db_name\"><BR>\r\n";
		echo "\tDatabase location: <INPUT TYPE=\"text\" NAME=\"db_loc\" VALUE=\"localhost\"><BR>\r\n";
		echo "\tUsername: <INPUT TYPE=\"text\" NAME=\"dbusername\"><BR>\r\n";
		echo "\tPassword: <INPUT TYPE=\"text\" NAME=\"dbpassword\"><BR>\r\n";

		echo "\t<INPUT TYPE=\"HIDDEN\" NAME=\"upgrade_from_query\" VALUE=". $_POST["old_version"] . ">\r\n";
		echo "\t<BR><BR><INPUT TYPE=\"SUBMIT\" NAME=\"step4\" VALUE=\"Next -&gt;\">\r\n";
	}

	function pre_upgr_phpbttrkplus17() {
		echo "\tUpgrading from: PHPBTTracker+ 1.7<BR><BR>\r\n";
		echo "\tNow for a few version-specific questions/issues:<BR><BR>\r\n";

		echo "\t<B>WARNING:</B> The tracker may become somewhat unresponsive during this process. <B>Keep a backup of the database ready</B> in case it needs to be restored.<BR><BR>\r\n";

		echo "\t<B><U>Cleaning up some internal tracker tables</U></B><BR><BR>\r\n";
		echo "\tThe new administration scripts will keep the internal tracker tables clean; however there is a need for a one-time\r\n";
		echo "\tsweep through them to ensure they are clean (if you go through a lot of torrents on the tracker there are remnants of\r\n";
		echo "\tthem lying around.) <B>WARNING:</B> If this part of the script fails, your tracker could be left in an unusable state!</B><BR><BR>\r\n";
		echo "\tWhat do you want the script to do?<BR>\r\n";
		echo "\t&nbsp;&nbsp;&nbsp;<INPUT TYPE=RADIO NAME=\"timestamps_q\" VALUE=1 CHECKED> No, don't do clean up<BR>\r\n";
		echo "\t&nbsp;&nbsp;&nbsp;<INPUT TYPE=RADIO NAME=\"timestamps_q\" VALUE=2> Yes, do the clean up<BR><BR>\r\n";

		echo "\t<B><U>You need to set a root user/password in config.php</U></B><BR><BR>\r\n";
		echo "\tYou need to set two new variables in config.php (admin_user and admin_pass) to be able to login to the admin interface.<BR><BR>\r\n";

		echo "\t<B><U>Database Username/Password/Database Name</U></B><BR><BR>\r\n";
		echo "\tEnter the database details below. Use the username and password you use to logon to the Administrative interface as root.<BR><BR>\r\n";

		echo "\tDatabase name: <INPUT TYPE=\"text\" NAME=\"db_name\"><BR>\r\n";
		echo "\tDatabase location: <INPUT TYPE=\"text\" NAME=\"db_loc\" VALUE=\"localhost\"><BR>\r\n";
		echo "\tUsername: <INPUT TYPE=\"text\" NAME=\"dbusername\"><BR>\r\n";
		echo "\tPassword: <INPUT TYPE=\"text\" NAME=\"dbpassword\"><BR>\r\n";

		echo "\t<INPUT TYPE=\"HIDDEN\" NAME=\"upgrade_from_query\" VALUE=". $_POST["old_version"] . ">\r\n";
		echo "\t<BR><BR><INPUT TYPE=\"SUBMIT\" NAME=\"step4\" VALUE=\"Next -&gt;\">\r\n";
	}

	function pre_upgr_phpbttrkplus20() {
		echo "\tUpgrading from: PHPBTTracker+ 2.0<BR><BR>\r\n";
		echo "\tNow for a few version-specific questions/issues:<BR><BR>\r\n";

		echo "\t<B>WARNING:</B> The tracker may become somewhat unresponsive during this process. <B>Keep a backup of the database ready</B> in case it needs to be restored.<BR><BR>\r\n";

		echo "\t<B><U>Database Username/Password/Database Name</U></B><BR><BR>\r\n";
		echo "\tEnter the database details below. Use the username and password you use to logon to the Administrative interface as root.<BR><BR>\r\n";

		echo "\tDatabase name: <INPUT TYPE=\"text\" NAME=\"db_name\"><BR>\r\n";
		echo "\tDatabase location: <INPUT TYPE=\"text\" NAME=\"db_loc\" VALUE=\"localhost\"><BR>\r\n";
		echo "\tUsername: <INPUT TYPE=\"text\" NAME=\"dbusername\"><BR>\r\n";
		echo "\tPassword: <INPUT TYPE=\"text\" NAME=\"dbpassword\"><BR>\r\n";

		echo "\t<INPUT TYPE=\"HIDDEN\" NAME=\"upgrade_from_query\" VALUE=". $_POST["old_version"] . ">\r\n";
		echo "\t<BR><BR><INPUT TYPE=\"SUBMIT\" NAME=\"step4\" VALUE=\"Next -&gt;\">\r\n";
	}

	function pre_upgr_phpbttrkplus21() {
		echo "\tUpgrading from: PHPBTTracker+ 2.1<BR><BR>\r\n";
		echo "\tNow for a few version-specific questions/issues:<BR><BR>\r\n";

		echo "\t<B>WARNING:</B> The tracker may become somewhat unresponsive during this process. <B>Keep a backup of the database ready</B> in case it needs to be restored.<BR><BR>\r\n";

		echo "\tThe peer tables will be cleaned and reconfigured during this process. As peers reconnect, the statistics will return to normal.<BR><BR>\r\n";
		echo "\t<B><U>Database Username/Password/Database Name</U></B><BR><BR>\r\n";
		echo "\tEnter the database details below. Use the username and password you use to logon to the Administrative interface as root.<BR><BR>\r\n";

		echo "\tDatabase name: <INPUT TYPE=\"text\" NAME=\"db_name\"><BR>\r\n";
		echo "\tDatabase location: <INPUT TYPE=\"text\" NAME=\"db_loc\" VALUE=\"localhost\"><BR>\r\n";
		echo "\tUsername: <INPUT TYPE=\"text\" NAME=\"dbusername\"><BR>\r\n";
		echo "\tPassword: <INPUT TYPE=\"text\" NAME=\"dbpassword\"><BR>\r\n";

		echo "\t<INPUT TYPE=\"HIDDEN\" NAME=\"upgrade_from_query\" VALUE=". $_POST["old_version"] . ">\r\n";
		echo "\t<BR><BR><INPUT TYPE=\"SUBMIT\" NAME=\"step4\" VALUE=\"Next -&gt;\">\r\n";
	}


	function pre_upgr_phpbttrk15() {
		echo "\tUpgrading from: Original PHPBTTracker 1.5e<BR><BR>\r\n";
		echo "\tNow for a few version-specific questions/issues:<BR><BR>\r\n";

		echo "\t<B><U>Moving torrent size from info column to size column</U></B><BR><BR>\r\n";
		echo "\tIn phpbttracker, the size of the torrent is stored in the 'info' field in the database. This script will attempt to move this information\r\n";
		echo "\tto the 'size' field. <B>Note:</B> The torrent size must be at the <U>beginning</U> of the info field, followed by a space and 'MB' for this\r\n";
		echo "\tto work (ie 203 MB.) If not, the size will remain zero for the torrent, and the average progress per torrent will not be accurate.<BR><BR>\r\n";
		echo "\tWhat do you want the script to do?<BR>\r\n";
		echo "\t&nbsp;&nbsp;&nbsp;<INPUT TYPE=RADIO NAME=\"info_q\" VALUE=1 CHECKED> Yes, move the torrent size information over.<BR>\r\n";
		echo "\t&nbsp;&nbsp;&nbsp;<INPUT TYPE=RADIO NAME=\"info_q\" VALUE=2> No, don't move the torrent size information over.<BR><BR>\r\n";

		echo "\t<B><U>You need to set a root user/password in config.php</U></B><BR><BR>\r\n";
		echo "\tYou need to set two new variables in config.php (admin_user and admin_pass) to be able to login to the admin interface.<BR><BR>\r\n";

		echo "\t<B><U>Database Username/Password/Database Name</U></B><BR><BR>\r\n";
		echo "\tEnter the database details below. Use the username and password you use to logon to the Administrative interface as root.<BR><BR>\r\n";

		echo "\tDatabase name: <INPUT TYPE=\"text\" NAME=\"db_name\"><BR>\r\n";
		echo "\tDatabase location: <INPUT TYPE=\"text\" NAME=\"db_loc\" VALUE=\"localhost\"><BR>\r\n";
		echo "\tUsername: <INPUT TYPE=\"text\" NAME=\"dbusername\"><BR>\r\n";
		echo "\tPassword: <INPUT TYPE=\"text\" NAME=\"dbpassword\"><BR>\r\n";

		echo "\t<INPUT TYPE=\"HIDDEN\" NAME=\"upgrade_from_query\" VALUE=". $_POST["old_version"] . ">\r\n";
		echo "\t<BR><BR><INPUT TYPE=\"SUBMIT\" NAME=\"step4\" VALUE=\"Next -&gt;\">\r\n";
	}

	/*
	 * Upgrade functions.
	 */
	function upgr_phpbttrkplus16() {
		if (!isset($_POST["db_name"]) || !isset($_POST["db_loc"]) || !isset($_POST["dbusername"])) {
			echo "\tUpgrade aborted; you didn't specify database details required.\r\n";
		} else {
			if (strlen($_POST["db_name"]) == 0 || strlen($_POST["db_loc"]) == 0 || strlen($_POST["dbusername"]) == 0 ) {
				echo "\tUpgrade aborted; you didn't specify database details required.\r\n";
			} else {
				$db_password = ( isset($_POST["dbpassword"]) ) ? $_POST["dbpassword"] : "" ;

				if (isset($_POST["ban_q"])) {
					if ($_POST["ban_q"] == 2) 
						$clearban = true; 
					else 
						$clearban = false;
				} else {
					$clearban = false;
				}

				if (isset($_POST["timestamps_q"])) {
					if ($_POST["timestamps_q"] == 2) 
						$timestamps = true; 
					else 
						$timestamps = false;
				} else {
					$timestamps = false;
				}
				
				/*
				 * Connect to the database
				 */
				@mysql_connect($_POST["db_loc"], $_POST["dbusername"], $db_password) or die("\tCan't connect to database: ".mysql_error()."<BR>\r\n"); 

				/*
				 * Connect to the desired database
				 */
				@mysql_select_db($_POST["db_name"]) or die("\tCan't open database ".$_POST["db_name"].": " . mysql_error() . "<BR>\r\n");

				echo "\tStarting part 1 of upgrade process...<BR><BR>\r\n";

				/*
				 * Check to see if this is the right database...
				 */
				if (!@mysql_query("SELECT `username` FROM `adminusers`")) {
					echo "<FONT COLOR=RED><B>This does not appear to be PHPBTTracker+ 1.6 database; script aborted.</B></FONT><BR>";
				} else {
					/*
					 * Create the new torrents table
					 */
					echo "Creating new torrents table... ";
					if (!@mysql_query("CREATE TABLE `torrents` (`info_hash` VARCHAR (40) DEFAULT '0' NOT NULL, `name` VARCHAR (255) NOT NULL, `metadata` LONGBLOB NOT NULL, PRIMARY KEY(`info_hash`))")) {
						echo "<FONT COLOR=RED>FAILED</FONT><BR>";
					} else {
						echo "done!<BR>";
					}

					/*
					 * Do the 3 queries to update the adminusers table
					 */
					echo "Updating administration users table (1/3)... ";
					if (!@mysql_query("ALTER TABLE `adminusers` ADD `perm_mirror` ENUM('Y','N')  DEFAULT 'N' NOT NULL")) {
						echo "<FONT COLOR=RED>FAILED</FONT><BR>";
					} else {
						echo "done!<BR>";
					}

					echo "Updating administration users table (2/3)... ";
					if (!@mysql_query("ALTER TABLE `adminusers` ADD `enabled` ENUM('Y','N')  DEFAULT 'Y' NOT NULL AFTER `comment`")) {
						echo "<FONT COLOR=RED>FAILED</FONT><BR>";
					} else {
						echo "done!<BR>";
					}

					echo "Updating administration users table (3/3)... ";
					if (!@mysql_query("ALTER TABLE `adminusers` ADD `disable_reason` VARCHAR(255)  DEFAULT \"\" NOT NULL AFTER `enabled`")) {
						echo "<FONT COLOR=RED>FAILED</FONT><BR>";
					} else {
						echo "done!<BR>";
					}	

					if ($clearban) {
						echo "\tStarting IP Banning transition...<BR>\r\n";
						echo "\tClearing all bans... ";
						if (!@mysql_query("TRUNCATE TABLE `ipbans`")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}	

						echo "Updating IP banning table (1/7)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` ADD `iplong` INT(10)  DEFAULT 0 NOT NULL AFTER `ip`")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}

						echo "Updating IP banning table (2/7)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` DROP PRIMARY KEY")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}

						echo "Updating IP banning table (3/7)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` ADD `ban_id` BIGINT UNSIGNED NOT NULL FIRST")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}

						echo "Updating IP banning table (4/7)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` ADD PRIMARY KEY (ban_id)")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}

						echo "Updating IP banning table (5/7)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` CHANGE `ban_id` `ban_id` BIGINT(20)  UNSIGNED NOT NULL AUTO_INCREMENT")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}

						echo "Updating IP banning table (6/7)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` AUTO_INCREMENT = 1")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}

						echo "Updating IP banning table (7/7)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` ADD INDEX (`iplong`)")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}
					} else {
						echo "Updating IP banning table (1/8)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` ADD `iplong` INT(10)  DEFAULT 0 NOT NULL AFTER `ip`")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}

						echo "Updating IP banning table (2/8)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` DROP PRIMARY KEY")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}

						echo "Updating IP banning table (3/8)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` ADD `ban_id` BIGINT UNSIGNED NOT NULL FIRST")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}

						/*
						 * Have to reindex everything...
						 */
						echo "Updating IP banning table [reindexing] (4/8)... ";
						$recordset = @mysql_query("SELECT `ip` FROM `ipbans`");

						$counter = 1;

						if (!$recordset) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							if (mysql_num_rows($recordset) > 0) {
								while ($row = mysql_fetch_row($recordset)) {
									@mysql_query("UPDATE `ipbans` SET `ban_id` = $counter WHERE `ip`=\"$row[0]\"");
									$counter++;
								}
								$updated = $counter-1;
								echo "done - $updated records reindexed!<BR>";
							} else {
								echo "done - no bans found!<BR>";
							}
						}

						echo "Updating IP banning table (5/8)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` ADD PRIMARY KEY (ban_id)")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}

						echo "Updating IP banning table (6/8)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` CHANGE `ban_id` `ban_id` BIGINT(20)  UNSIGNED NOT NULL AUTO_INCREMENT")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}

						echo "Updating IP banning table (7/8)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` AUTO_INCREMENT = $counter")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}

						echo "Updating IP banning table (8/8)... ";
						if (!@mysql_query("ALTER TABLE `ipbans` ADD INDEX (`iplong`)")) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>";
						}
					}
	
					if ($timestamps) {
						echo "\tTimestamp cleanup starting...<BR>\r\n";

						echo "\tBuilding a list of active torrents... ";
						$rstActiveTorrents = @mysql_query("SELECT `info_hash` FROM namemap");
						if (!$rstActiveTorrents) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>\r\n";

							if (mysql_num_rows($rstActiveTorrents) > 0) {
								while ($rowActiveTorrent = mysql_fetch_row($rstActiveTorrents)) {
									$arActiveTorrents[] = $rowActiveTorrent[0];
								}
	
								echo "\tStarting cleanup... ";
								$rstTimestamps = @mysql_query("SELECT DISTINCT `info_hash` FROM timestamps");
								if (!$rstTimestamps) {
									echo "Table shows no stale records; no changes to database were made.<BR>\r\n";
								} else {
									if (mysql_num_rows($rstTimestamps) > 0) {
										$purged = 0;

										while ($rowTimestamps = mysql_fetch_row($rstTimestamps)) {
											if (!in_array($rowTimestamps[0], $arActiveTorrents)) {
												$purged++;
												@mysql_query("DELETE FROM `timestamps` WHERE `info_hash`=\"$rowTimestamps[0]\"");
											}
										}

										echo "Cleanup finished; $purged stale torrent records were removed. Keep an eye on the statistics for the existing torrents. You may need to readd torrents that show oddball results, or run the database consistency check.<BR>\r\n";
									} else {
										echo "Table shows no stale records; no changes to database were made.<BR>\r\n";
									}
								}
							} else {
								echo "\tNo active torrents on tracker. Purging old information if it exists.<BR>\r\n";
								@mysql_query("TRUNCATE TABLE `timestamps`");
							}
						}
					} else {
						echo "\tSkipping internal tables cleanup, as requested...<BR>\r\n";
					}

					/*
					 * Ok, now see what category names are available, and make them valid
					 * 3 tables have this information, we'll have to do 1 at a time...
					 */
					echo "\t<BR>OK, examining adminusers table for category names that have spaces in them... \r\n";
					$rstAdminCategory = mysql_query("SELECT DISTINCT `category` from `adminusers`");
					if (!$rstAdminCategory) {
						/*
						 * No categories, skip.
						 */
						echo "skipping, there do not appear to be category names in the adminusers table...<BR>\r\n";
					} else {
						/*
						 * Some categories were found, let's take a look at them...
						 */
						$counter=0;
						while ($row = mysql_fetch_row($rstAdminCategory)) {
							list($category) = $row;
							if (strpos($category, " ") !== false) {
								/*
								 * Gah, a space was found... let's fix it!
								 */
								$newcategory = ereg_replace(" ","_", $category);
								@mysql_query("UPDATE `adminusers` SET `category`=\"$newcategory\" WHERE `category`=\"$category\"");
								echo "\r\n\t<BR><B>!</B> Renaming $category => $newcategory!";
								$counter++;
							}
						}
						echo "\r\n\t<BR>Finished adminusers table. $counter categories changed! <B><FONT COLOR=RED>You may need to edit your stats scripts to reflect this!!!</FONT></B><BR>\r\n";
					}

					echo "\t<BR>OK, examining retired torrents table for category names that have spaces in them... \r\n";
					$rstRetiredCategory = @mysql_query("SELECT DISTINCT `category` from `retired`");
					if (!$rstRetiredCategory) {
						/*
						 * No categories, skip.
						 */
						echo "skipping, there do not appear to be category names in the retired torrents table...<BR>\r\n";
					} else {
						/*
						 * Some categories were found, let's take a look at them...
						 */
						$counter=0;
						while ($row = mysql_fetch_row($rstRetiredCategory)) {
							list($category) = $row;
							if (strpos($category, " ") !== false) {
								/*
								 * Gah, a space was found... let's fix it!
								 */
								$newcategory = ereg_replace(" ","_", $category);
								@mysql_query("UPDATE `retired` SET `category`=\"$newcategory\" WHERE `category`=\"$category\"");
								echo "\r\n\t<BR><B>!</B> Renaming $category => $newcategory!";
								$counter++;
							}
						}
						echo "\r\n\t<BR>Finished retired torrents table. $counter categories changed! <B><FONT COLOR=RED>You may need to edit your stats scripts to reflect this!!!</FONT></B><BR>\r\n";
					}

					echo "\t<BR>OK, examining active torrents table for category names that have spaces in them... \r\n";
					$rstActiveCategory = @mysql_query("SELECT DISTINCT `category` from `namemap`");
					if (!$rstActiveCategory) {
						/*
						 * No categories, skip.
						 */
						echo "skipping, there do not appear to be category names in the retired torrents table...<BR>\r\n";
					} else {
						/*
						 * Some categories were found, let's take a look at them...
						 */
						$counter=0;
						while ($row = mysql_fetch_row($rstActiveCategory)) {
							list($category) = $row;
							if (strpos($category, " ") !== false) {
								/*
								 * Gah, a space was found... let's fix it!
								 */
								$newcategory = ereg_replace(" ","_", $category);
								@mysql_query("UPDATE `namemap` SET `category`=\"$newcategory\" WHERE `category`=\"$category\"");
								echo "\r\n\t<BR><B>!</B> Renaming $category => $newcategory!";
								$counter++;
							}
						}
						echo "\r\n\t<BR>Finished active torrents table. $counter categories changed! <B><FONT COLOR=RED>You may need to edit your stats scripts to reflect this!!!</FONT></B><BR>\r\n";
					}

					echo "\t<BR><B>OK</B>, doing second and final part of upgrade...<BR><BR>\r\n";

					upgr_phpbttrkplus17(true);

					/*
					 * Upgrade should be done
					 */
					echo "\t<BR><BR><BR>Upgrade completed! If any part of the script failed, please try re-running this upgrade script.<BR><BR>\r\n";
					echo "\t<B>Delete install.php and/or upgrade.php from your web server if it was successful!</B> Refer to the INSTALL for the rest of the installation procedure.";
				}
			}
		}
	}

	function upgr_phpbttrkplus17($cascading=false) {
		if (!isset($_POST["db_name"]) || !isset($_POST["db_loc"]) || !isset($_POST["dbusername"])) {
			echo "\tUpgrade aborted; you didn't specify database details required.\r\n";
		} else {
			if (strlen($_POST["db_name"]) == 0 || strlen($_POST["db_loc"]) == 0 || strlen($_POST["dbusername"]) == 0 ) {
				echo "\tUpgrade aborted; you didn't specify database details required.\r\n";
			} else {
				$db_password = ( isset($_POST["dbpassword"]) ) ? $_POST["dbpassword"] : "" ;

				/*
				 * Connect to the database
				 */
				@mysql_connect($_POST["db_loc"], $_POST["dbusername"], $db_password) or die("\tCan't connect to database: ".mysql_error()."<BR>\r\n"); 

				/*
				 * Connect to the desired database
				 */
				@mysql_select_db($_POST["db_name"]) or die("\tCan't open database ".$_POST["db_name"].": " . mysql_error() . "<BR>\r\n");

				/*
				 * Array of operations to perform
				 */
				$query_list = array();
				$query_list[0]['name'] = "Fixing timestamps table indexing (1/2)...";
				$query_list[0]['query'] = "ALTER TABLE `timestamps` DROP INDEX SORTING";
				$query_list[1]['name'] = "Fixing timestamps table indexing (2/2)...";
				$query_list[1]['query'] = "ALTER TABLE `timestamps` ADD INDEX SORTING (`info_hash`)";
				$query_list[2]['name'] = "Fixing summary table (1/9)...";
				$query_list[2]['query'] = "ALTER TABLE `summary` CHANGE `dlbytes` `dlbytes` BIGINT(20) UNSIGNED DEFAULT 0 NOT NULL";
				$query_list[3]['name'] = "Fixing summary table (2/9)...";
				$query_list[3]['query'] = "ALTER TABLE `summary` CHANGE `seeds` `seeds` INT(10) UNSIGNED DEFAULT 0 NOT NULL";
				$query_list[4]['name'] = "Fixing summary table (3/9)...";
				$query_list[4]['query'] = "ALTER TABLE `summary` CHANGE `leechers` `leechers` INT(10) UNSIGNED DEFAULT 0 NOT NULL";
				$query_list[5]['name'] = "Fixing summary table (4/9)...";
				$query_list[5]['query'] = "ALTER TABLE `summary` CHANGE `finished` `finished` INT(10) UNSIGNED DEFAULT 0 NOT NULL";
				$query_list[6]['name'] = "Fixing summary table (5/9)...";
				$query_list[6]['query'] = "ALTER TABLE `summary` CHANGE `finished` `finished` INT(10) UNSIGNED DEFAULT 0 NOT NULL";
				$query_list[7]['name'] = "Fixing summary table (6/9)...";
				$query_list[7]['query'] = "ALTER TABLE `summary` CHANGE `lastSpeedCycle` `lastSpeedCycle` INT(10) UNSIGNED DEFAULT 0 NOT NULL";
				$query_list[8]['name'] = "Fixing summary table (7/9)...";
				$query_list[8]['query'] = "ALTER TABLE `summary` CHANGE `lastAvgCycle` `lastAvgCycle` INT(10) UNSIGNED DEFAULT 0 NOT NULL";
				$query_list[9]['name'] = "Fixing summary table (8/9)...";
				$query_list[9]['query'] = "ALTER TABLE `summary` CHANGE `lastcycle` `lastcycle` INT(10) UNSIGNED DEFAULT 0 NOT NULL";
				$query_list[10]['name'] = "Fixing summary table (9/9)...";
				$query_list[10]['query'] = "ALTER TABLE `summary` CHANGE `speed` `speed` BIGINT(20) UNSIGNED DEFAULT 0 NOT NULL";

				/*
				 * Do timestamps cleanup?
				 */
				if (isset($_POST["timestamps_q"])) {
					if ($_POST["timestamps_q"] == 2) 
						$timestamps = true; 
					else 
						$timestamps = false;
				} else {
					$timestamps = false;
				}

				/*
				 * Check to see if this is the right database...
				 */
				if (!@mysql_query("SELECT `perm_mirror` FROM `adminusers`")) {
					echo "<FONT COLOR=RED><B>This does not appear to be PHPBTTracker+ 1.7 database; script aborted.</B></FONT><BR>";
				} else {
					/*
					 * Run through all the fixes in the query array
					 */
					for ($i=0; $i < count($query_list); $i++) {
						if (strlen($query_list[$i]["query"]) > 0) {
							echo $query_list[$i]['name'];
							if (!@mysql_query($query_list[$i]['query'])) {
								echo "<FONT COLOR=RED>FAILED</FONT><BR>";
							} else {
								echo "done!<BR>";
							}
						}
					}

					/*
					 * Need to check x<hash> tables now; grab a list of hashes from the summary table...
					 */
					echo "<BR>Checking for active torrents that need to be updated...<BR>\r\n";
					$recordset = @mysql_query("SELECT `info_hash` FROM `summary`");
					if (!$recordset) {
						echo "Can't get a list of active torrents, upgrade script halted.<BR><BR>\r\n";
					} else {
						$fails = 0;
						if (mysql_num_rows($recordset) > 0) {
							$updated = 0;
							while ($row = mysql_fetch_row($recordset)) {
								$tmpFail = false;
								if (!@mysql_query("ALTER TABLE `x$row[0]` CHANGE `peer_id` `peer_id` CHAR(40) NOT NULL, CHANGE `ip` `ip` CHAR(50)  DEFAULT 'error.x' NOT NULL")) {
									$tmpFail = true;
								}

								if (!@mysql_query("ALTER TABLE `x$row[0]` CHANGE `port` `port` SMALLINT(5)  UNSIGNED DEFAULT 0 NOT NULL")) {
									$tmpFail = true;
								}

								if (!@mysql_query("ALTER TABLE `x$row[0]` CHANGE `lastupdate` `lastupdate` INT(10)  UNSIGNED DEFAULT 0 NOT NULL")) {
									$tmpFail = true;
								}

								if (!@mysql_query("ALTER TABLE `x$row[0]` CHANGE `sequence` `sequence` INT(10)  UNSIGNED NOT NULL AUTO_INCREMENT")) {
									$tmpFail = true;
								}

								if (!@mysql_query("OPTIMIZE TABLE `x$row[0]`")) {
									$tmpFail = true;
								}

								if ($tmpFail) {
									$fails++;
								} else {
									$updated++;
								}
							}

							echo "$updated active torrent tables have been updated successfully...<BR>\r\n";

							if ($fails > 0) {
								echo "$fails active torrents tables could not be updated!<BR>\r\n";
							}
						} else {
							echo "No active torrents.<BR>\r\n";
						}
					}

					if ($timestamps) {
						echo "\tTimestamp cleanup starting...<BR>\r\n";

						echo "\tBuilding a list of active torrents... ";
						$rstActiveTorrents = @mysql_query("SELECT `info_hash` FROM namemap");
						if (!$rstActiveTorrents) {
							echo "<FONT COLOR=RED>FAILED</FONT><BR>";
						} else {
							echo "done!<BR>\r\n";

							if (mysql_num_rows($rstActiveTorrents) > 0) {
								while ($rowActiveTorrent = mysql_fetch_row($rstActiveTorrents)) {
									$arActiveTorrents[] = $rowActiveTorrent[0];
								}
	
								echo "\tStarting cleanup... ";
								$rstTimestamps = @mysql_query("SELECT DISTINCT `info_hash` FROM timestamps");
								if (!$rstTimestamps) {
									echo "Table shows no stale records; no changes to database were made.<BR>\r\n";
								} else {
									if (mysql_num_rows($rstTimestamps) > 0) {
										$purged = 0;

										while ($rowTimestamps = mysql_fetch_row($rstTimestamps)) {
											if (!in_array($rowTimestamps[0], $arActiveTorrents)) {
												$purged++;
												@mysql_query("DELETE FROM `timestamps` WHERE `info_hash`=\"$rowTimestamps[0]\"");
											}
										}

										echo "Cleanup finished; $purged stale torrent records were removed. Keep an eye on the statistics for the existing torrents. You may need to readd torrents that show oddball results, or run the database consistency check.<BR>\r\n";
									} else {
										echo "Table shows no stale records; no changes to database were made.<BR>\r\n";
									}
								}
							} else {
								echo "\tNo active torrents on tracker. Purging old information if it exists.<BR>\r\n";
								@mysql_query("TRUNCATE TABLE `timestamps`");
							}
						}
					} else {
						echo "\tSkipping internal tables cleanup, as requested...<BR>\r\n";
					}
					upgr_phpbttrkplus20();
				}
			}
		}
	}


	function upgr_phpbttrkplus20($cascading=false) {
		if (!isset($_POST["db_name"]) || !isset($_POST["db_loc"]) || !isset($_POST["dbusername"])) {
			echo "\tUpgrade aborted; you didn't specify database details required.\r\n";
		} else {
			if (strlen($_POST["db_name"]) == 0 || strlen($_POST["db_loc"]) == 0 || strlen($_POST["dbusername"]) == 0 ) {
				echo "\tUpgrade aborted; you didn't specify database details required.\r\n";
			} else {
				$db_password = ( isset($_POST["dbpassword"]) ) ? $_POST["dbpassword"] : "" ;

				/*
				 * Connect to the database
				 */
				@mysql_connect($_POST["db_loc"], $_POST["dbusername"], $db_password) or die("\tCan't connect to database: ".mysql_error()."<BR>\r\n"); 

				/*
				 * Connect to the desired database
				 */
				@mysql_select_db($_POST["db_name"]) or die("\tCan't open database ".$_POST["db_name"].": " . mysql_error() . "<BR>\r\n");

				/*
				 * Array of operations to perform
				 */
				$query_list = array();
				$query_list[0]['name'] = "Adding subgrouping table (1/9)...";
				$query_list[0]['query'] = "CREATE TABLE `subgrouping` (`group_id` BIGINT (10) UNSIGNED AUTO_INCREMENT NOT NULL, `heading` TEXT NOT NULL DEFAULT '', `groupsort` INT (5) UNSIGNED NOT NULL DEFAULT 0, `category` VARCHAR (10) NOT NULL DEFAULT 'main', PRIMARY KEY(`group_id`))";
				$query_list[1]['name'] = "Adding new permission to users table (2/9)...";
				$query_list[1]['query'] = "ALTER TABLE `adminusers` ADD `perm_advsort` ENUM('Y','N') DEFAULT 'N' NOT NULL";
				$query_list[2]['name'] = "Fixing namemap table (3/9)...";
				$query_list[2]['query'] = "ALTER TABLE `namemap` ADD `grouping` INT(5) UNSIGNED NOT NULL DEFAULT 0";
				$query_list[3]['name'] = "Fixing namemap table (4/9)...";
				$query_list[3]['query'] = "ALTER TABLE `namemap` ADD `sorting` INT(5) UNSIGNED DEFAULT 0 NOT NULL";
				$query_list[4]['name'] = "Fixing namemap table (5/9)...";
				$query_list[4]['query'] = "ALTER TABLE `namemap` ADD `comment` VARCHAR(255) DEFAULT '' NOT NULL";
				$query_list[5]['name'] = "Fixing namemap table (6/9)...";
				$query_list[5]['query'] = "ALTER TABLE `namemap` ADD `show_comment` ENUM('Y','N') DEFAULT 'N' NOT NULL";
				$query_list[6]['name'] = "Fixing namemap table (7/9)...";
				$query_list[6]['query'] = "ALTER TABLE `namemap` ADD `tsAdded` BIGINT(20) DEFAULT 0 NOT NULL";
				$query_list[7]['name'] = "Fixing namemap table (8/9)...";
				$query_list[7]['query'] = "ALTER TABLE `namemap` ADD `torrent_size` BIGINT(10) DEFAULT 0 NOT NULL";
				$query_list[8]['name'] = "Fixing namemap table (9/9)...";
				$query_list[8]['query'] = "ALTER TABLE `namemap` ADD `finished` INT(10) UNSIGNED DEFAULT 0 NOT NULL";
	
				/*
				 * Check to see if this is the right database...
				 * (There wasn't any significant changes between 1.7 and 2.0, other than the peer cache,
				 * which can be disabled, so we can't use that to check.
				 */
				if (!@mysql_query("SELECT `perm_mirror` FROM `adminusers`")) {
					echo "<FONT COLOR=RED><B>This does not appear to be PHPBTTracker+ 2.0 database; script aborted.</B></FONT><BR>";
				} else {
					/*
					 * Run through all the fixes in the query array
					 */
					for ($i=0; $i < count($query_list); $i++) {
						if (strlen($query_list[$i]["query"]) > 0) {
							echo $query_list[$i]['name'];
							if (!@mysql_query($query_list[$i]['query'])) {
								echo "<FONT COLOR=RED>FAILED</FONT><BR>";
							} else {
								echo "done!<BR>";
							}
						}
					}

					/*
					 * We have to go through and set the timestamps and torrent size for the RSS feed...
					 */
					echo "<BR>Checking for torrents that need to be updated to be compatible with the RSS feed...<BR>\r\n";
					$recordset = @mysql_query("SELECT `info_hash`, `DateAdded` FROM `namemap`");
					if (!$recordset) {
						echo "Can't get a list of torrents, upgrade script halted.<BR><BR>\r\n";
					} else {
						$fails = 0;
						if (mysql_num_rows($recordset) > 0) {
							$updated = 0;
							while ($row = mysql_fetch_row($recordset)) {
								$tmpFail = false;
								$datearray = explode("-", $row[1]);
								$newtime = mktime(0,0,0,$datearray[1],$datearray[2], $datearray[0]);
								if (!@mysql_query("UPDATE `namemap` SET `torrent_size` = 10000, `tsAdded` = $newtime WHERE `info_hash` = '$row[0]'")) {
									$tmpFail = true;
									$fails++;
								} else {
									$updated++;
								}
							}

							echo "$updated torrent(s) have been updated successfully...<BR>\r\n";

							if ($fails > 0) {
								echo "$fails torrent(s) could not be updated!<BR>\r\n";
							}
						} else {
							echo "No torrents found.<BR>\r\n";
						}
					}

					/*
					 * We need to set up the initial order for the advanced sort...
					 */
					echo "<BR>Initializing the advanced sorting tables/fields...<BR>\r\n";
					$recordset = @mysql_query("SELECT `info_hash`,`category` FROM `namemap` ORDER BY `category`, `filename`");
					if (!$recordset) {
						echo "Can't get a list of torrents, upgrade script halted.<BR><BR>\r\n";
					} else {
						$oldcategory = '';
						if (mysql_num_rows($recordset) > 0) {
							$updated = 0;
							while ($row = mysql_fetch_row($recordset)) {
								if ($oldcategory != $row[1]) {
									$sortcounter = 1;
									$oldcategory = $row[1];
								}
								
								if (!@mysql_query("UPDATE `namemap` SET `grouping` = 0, `sorting` = $sortcounter WHERE `info_hash` = '$row[0]'")) {
									; // no need to do anything
								} else {
									$updated++;
								}
								$sortcounter++;
							}

							echo "$updated torrent(s) sorting have been updated successfully...<BR>\r\n";
						} else {
							echo "No torrents found to update.<BR>\r\n";
						}
					}
					upgr_phpbttrkplus21(true);
				}
			}
		}
	}

	function upgr_phpbttrkplus21($cascading=false) {
		if (!isset($_POST["db_name"]) || !isset($_POST["db_loc"]) || !isset($_POST["dbusername"])) {
			echo "\tUpgrade aborted; you didn't specify database details required.\r\n";
		} else {
			if (strlen($_POST["db_name"]) == 0 || strlen($_POST["db_loc"]) == 0 || strlen($_POST["dbusername"]) == 0 ) {
				echo "\tUpgrade aborted; you didn't specify database details required.\r\n";
			} else {
				$db_password = ( isset($_POST["dbpassword"]) ) ? $_POST["dbpassword"] : "" ;

				/*
				 * Connect to the database
				 */
				@mysql_connect($_POST["db_loc"], $_POST["dbusername"], $db_password) or die("\tCan't connect to database: ".mysql_error()."<BR>\r\n"); 

				/*
				 * Connect to the desired database
				 */
				@mysql_select_db($_POST["db_name"]) or die("\tCan't open database ".$_POST["db_name"].": " . mysql_error() . "<BR>\r\n");

				/*
				 * Check mysql compatability and issue warnings, if needed
				 */
				echo "Attempting to check mysql compatability [4.1.2 or greater required]... ";
				$rstVer = @mysql_query("SHOW VARIABLES LIKE 'version'");
				if (!$rstVer) {
					echo "<FONT COLOR=RED>FAILED.</FONT><BR><BR>";
				} else {
					$rowVer = mysql_fetch_row($rstVer);
					echo $rowVer[1] . " detected... ";

					/*
					 * Break down the version number and check it
					 */
					$splitVer = explode(".", $rowVer[1]);
				
					if ($splitVer[0] < 4) {
						echo " <B><FONT COLOR=RED>The mysql version running is too old.</FONT></B><BR><BR>";
					} else {
						if ($splitVer[0] == 4) {
							if ($splitVer[1] < 1 ) {
								echo " <B><FONT COLOR=RED>The mysql version running is too old.</FONT></B><BR><BR>";
							} else {
								if ($splitVer[1]==1) {
									if ($splitVer[2] < 2) {
										echo "<B><FONT COLOR=RED>The mysql version running is too old.</FONT></B><BR><BR>";
									} else {
										echo "Pass.<BR><BR>";
									}
								}
							}
	
						} else {
							echo "Pass.<BR><BR>";
						}
					}
					echo "<BR><BR>";
				}

				echo "Updating the peer tables, updating the schema and purging existing records: ";
				
				$rstQuery = mysql_query("SELECT `info_hash` FROM `summary`") or die("Oops: " . mysql_error());

				$counter=0;
	
		   	while ($record = mysql_fetch_row($rstQuery)) {
					$counter++;
					mysql_query("UPDATE `summary` SET `seeds`=0, `leechers`=0 WHERE `info_hash`=\"".$record[0]."\"");
					mysql_query("TRUNCATE TABLE `x".$record[0]."`") or die(mysql_error());
					mysql_query("TRUNCATE TABLE `y".$record[0]."`") or die(mysql_error());
					mysql_query("ALTER TABLE `y".$record[0]."` CHANGE `compact` `compact` BINARY( 6 ) NOT NULL") or die(mysql_error());
				}
	
				echo "Processed $counter records.<BR><BR>";


				echo "<BR><BR><BR><B>DONE! It is highly recommended that you modify the configuration in config_sample.php and rename it to config.php to take full advantage of this upgrade!</B><BR>\r\n";
				echo "<BR><BR><B>If you enable peer caching make sure you run makecache.php!!!!</B> The database will go in a stale state if you don't!</B><BR>\r\n";
	
				if (!$cascading) echo "\t<B>Delete install.php and/or upgrade.php from your web server if it was successful!</B> Refer to the INSTALL for the rest of the installation procedure.";
			}
		}
	}

	function upgr_phpbttrk15() {
		if (!isset($_POST["db_name"]) || !isset($_POST["db_loc"]) || !isset($_POST["dbusername"])) {
			echo "\tUpgrade aborted; you didn't specify database details required.\r\n";
		} else {
			if (strlen($_POST["db_name"]) == 0 || strlen($_POST["db_loc"]) == 0 || strlen($_POST["dbusername"]) == 0 ) {
				echo "\tUpgrade aborted; you didn't specify database details required.\r\n";
			} else {
				$db_password = ( isset($_POST["dbpassword"]) ) ? $_POST["dbpassword"] : "" ;

				/*
				 * Connect to the database
				 */
				@mysql_connect($_POST["db_loc"], $_POST["dbusername"], $db_password) or die("\tCan't connect to database: ".mysql_error()."<BR>\r\n"); 

				/*
				 * Connect to the desired database
				 */
				@mysql_select_db($_POST["db_name"]) or die("\tCan't open database ".$_POST["db_name"].": " . mysql_error() . "<BR>\r\n");

				if (isset($_POST["info_q"])) {
					if ($_POST["info_q"] == 1) 
						$movesize = true; 
					else 
						$movesize = false;
				} else {
					$movesize = false;
				}

				/*
				 * Array of operations to perform
				 */
				$query_list = array();
				$query_list[0]['name'] = "Creating logins table...";
				$query_list[0]['query'] = "CREATE TABLE `logins` (`id` INT (11) NOT NULL AUTO_INCREMENT, `used` TINYINT (1) DEFAULT '0' NOT NULL, `ipaddr` VARCHAR (16), PRIMARY KEY(`id`))";
				$query_list[1]['name'] = "Creating IP ban table...";
				$query_list[1]['query'] = "CREATE TABLE `ipbans` (`ban_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, `ip` VARCHAR (16) NOT NULL, `iplong` INT(10)  DEFAULT '0' NOT NULL, `bandate` DATE DEFAULT '0000-00-00' NOT NULL, `reason` VARCHAR (50) NOT NULL, `autoban` ENUM ('Y','N') DEFAULT 'N' NOT NULL, `banlength` TINYINT (3) UNSIGNED DEFAULT '0' NOT NULL, `banexpiry` DOUBLE DEFAULT '0000-00-00' NOT NULL, `banautoexpires` ENUM ('Y','N') DEFAULT 'N' NOT NULL, PRIMARY KEY(`ban_id`), INDEX(`bandate`,`autoban`,`banexpiry`,`banautoexpires`, `iplong`))";
				$query_list[2]['name'] = "Creating external torrents table...";
				$query_list[2]['query'] = "CREATE TABLE `trk_ext` (`info_hash` CHAR (40) NOT NULL, `scrape_url` VARCHAR (255) NOT NULL, `last_update` BIGINT (20) DEFAULT '0' NOT NULL, PRIMARY KEY(`info_hash`), INDEX(`scrape_url`))";
				$query_list[3]['name'] = "Creating retired torrents table...";
				$query_list[3]['query'] = "CREATE TABLE `retired` (`info_hash` VARCHAR (40) NOT NULL, `filename` VARCHAR (250) NOT NULL, `size` FLOAT DEFAULT '0' NOT NULL, `crc32` VARCHAR (254) NOT NULL, `category` VARCHAR (10) NOT NULL, `completed` INT (11) DEFAULT '0' NOT NULL, `transferred` BIGINT (20) DEFAULT '0' NOT NULL, `dateadded` DATE DEFAULT '0000-00-00' NOT NULL, `dateretired` DATE DEFAULT '0000-00-00' NOT NULL, PRIMARY KEY(`info_hash`))";
				$query_list[4]['name'] = "Creating user permissions table...";
				$query_list[4]['query'] = "CREATE TABLE `adminusers` (`username` VARCHAR (32) NOT NULL, `password` VARCHAR (32), `category` VARCHAR (10), `comment` VARCHAR (200), `enabled` ENUM('Y','N')  DEFAULT 'Y' NOT NULL, `disable_reason` VARCHAR(255) DEFAULT \"\" NOT NULL, `perm_add` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_addext` ENUM ('N','Y') DEFAULT 'N' NOT NULL, `perm_mirror` ENUM('Y','N')  DEFAULT 'N' NOT NULL, `perm_edit` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_delete` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_retire` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_unhide` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_peers` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_viewconf` ENUM ('N','Y') DEFAULT 'N' NOT NULL, `perm_retiredmgmt` ENUM ('N','Y') DEFAULT 'Y' NOT NULL, `perm_ipban` ENUM ('N','Y') DEFAULT 'N' NOT NULL, `perm_usermgmt` ENUM ('N','Y') DEFAULT 'N' NOT NULL, `perm_advsort` ENUM('Y','N') NOT NULL DEFAULT 'N', PRIMARY KEY(`username`))";
				$query_list[5]['name'] = "Creating torrents table...";
				$query_list[5]['query'] = "CREATE TABLE `torrents` (`info_hash` VARCHAR (40) DEFAULT '0' NOT NULL, `name` VARCHAR (255) NOT NULL, `metadata` LONGBLOB NOT NULL, PRIMARY KEY(`info_hash`))";
				$query_list[6]['name'] = "Adding new fields to namemap table...";
				$query_list[6]['query'] = "ALTER TABLE `namemap` ADD `mirrorurl` VARCHAR (250) NOT NULL, ADD `size` FLOAT DEFAULT '0' NOT NULL, ADD `crc32` VARCHAR (254) NOT NULL, ADD `DateAdded` DATE DEFAULT '0000-00-00' NOT NULL, ADD `category` VARCHAR (10) DEFAULT 'main' NOT NULL, ADD `sfvlink` VARCHAR (250), ADD `md5link` VARCHAR (250), ADD `infolink` VARCHAR (250), ADD `DateToRemoveURL` DATE DEFAULT '0000-00-00' NOT NULL, ADD `DateToHideTorrent` DATE DEFAULT '0000-00-00' NOT NULL, ADD `addedby` VARCHAR (32) DEFAULT 'root' NOT NULL, `grouping` INT (5) UNSIGNED NOT NULL DEFAULT 0, `sorting` INT (5) UNSIGNED NOT NULL DEFAULT 0, `comment` VARCHAR (255) NOT NULL DEFAULT '', `tsAdded` BIGINT (20) NOT NULL DEFAULT 0, `torrent_size` BIGINT(10) NOT NULL DEFAULT 0, ADD INDEX(`category`, `DateToHideTorrent`)";
				$query_list[7]['name'] = "Adding new fields to summary table...";
				$query_list[7]['query'] = "ALTER TABLE `summary` ADD `lastAvgCycle` INT (10) UNSIGNED DEFAULT '0' NOT NULL, ADD `hide_torrent` ENUM ('N','Y') DEFAULT 'N', ADD `avgdone` FLOAT DEFAULT '0' NOT NULL, ADD `external_torrent` ENUM ('N','Y') DEFAULT 'N', ADD `ext_no_scrape_update` ENUM ('N','Y') DEFAULT 'N', ADD INDEX(`hide_torrent`,`external_torrent`,`ext_no_scrape_update`)";
				$query_list[8]['name'] = "Fixing date added field...";
				$query_list[8]['query'] = "UPDATE `namemap` SET `DateAdded`= CURDATE()";
				$query_list[9]['name'] = "Creating subgrouping table...";
				$query_list[9]['query'] = "CREATE TABLE `subgrouping` (`group_id` BIGINT (10) UNSIGNED AUTO_INCREMENT NOT NULL, `heading` TEXT NOT NULL DEFAULT '', `groupsort` INT (5) UNSIGNED NOT NULL DEFAULT 0, `category` VARCHAR (10) NOT NULL DEFAULT 'main', PRIMARY KEY(`group_id`))";
	

				/*
				 * Check to see if this is the right database...
				 */
				if (!@mysql_query("SELECT `info` FROM `namemap`")) {
					echo "<FONT COLOR=RED><B>This does not appear to be PHPBTTracker database; script aborted.</B></FONT><BR>";
				} else {
					/*
					 * Run through all the fixes in the query array
					 */
					for ($i=0; $i < count($query_list); $i++) {
						if (strlen($query_list[$i]["query"]) > 0) {
							echo $query_list[$i]['name'];
							if (!@mysql_query($query_list[$i]['query'])) {
								echo "<FONT COLOR=RED>FAILED</FONT><BR>";
							} else {
								echo "done!<BR>";
							}
						}
					}

					/*
					 * Move the size info over if requested
					 */
					if ($movesize) {
						echo "<BR>Attempting to move torrent size information over...<BR>\r\n";
						$recordset = @mysql_query("SELECT `info_hash`, `info` FROM `namemap`");
						if (!$recordset) {
							echo "Can't get a list of hashes from namemap table, skipping.<BR><BR>\r\n";
						} else {
							$fails = 0;
							if (mysql_num_rows($recordset) > 0) {
								$updated = 0;
								while ($row = mysql_fetch_row($recordset)) {
									$infosplit = explode(" ", $row[1]);
									if (count($infosplit) >= 2) {
										if (is_numeric($infosplit[0]) && $infosplit[1] == "MB") {
											$newinfo = substr($row[1], strpos($row[1], "MB")+3);
											if (!@mysql_query("UPDATE `namemap` SET `size`= $infosplit[0], `info`=\"$newinfo\" WHERE `info_hash`=\"$row[0]\"")) {
												$fails++;
											} else {
												$updated++;
											}
										}
									}
								}

								echo "$updated active torrent tables have been updated successfully...<BR>\r\n";

								if ($fails > 0) {
									echo "$fails active torrents tables could not be updated!<BR>\r\n";
								}
							} else {
								echo "No active torrents.<BR>\r\n";
							}
						}
					}

					/*
					 * Now we need to add two fields to all the x<hash> tables
					 */
					echo "<BR>Checking for active torrents that need to be updated...<BR>\r\n";
					$recordset = @mysql_query("SELECT `info_hash` FROM `summary`");
					if (!$recordset) {
						echo "Can't get a list of active torrents, upgrade script halted.<BR><BR>\r\n";
					} else {
						$fails = 0;
						if (mysql_num_rows($recordset) > 0) {
							$updated = 0;
							while ($row = mysql_fetch_row($recordset)) {
								$tmpFail = false;
								if (!@mysql_query("ALTER TABLE `x$row[0]` ADD `uploaded` BIGINT(20) DEFAULT 0 NOT NULL")) {
									$tmpFail = true;
								}

								if (!@mysql_query("ALTER TABLE `x$row[0]` ADD `clientversion` VARCHAR(250) DEFAULT 'Not reported'")) {
									$tmpFail = true;
								}

								if ($tmpFail) {
									$fails++;
								} else {
									$updated++;
								}
							}

							echo "$updated active torrent tables have been updated successfully...<BR>\r\n";

							if ($fails > 0) {
								echo "$fails active torrents tables could not be updated!<BR>\r\n";
							}
						} else {
							echo "No active torrents.<BR>\r\n";
						}
					}

					/*
					 * We have to go through and set the timestamps and torrent size for the RSS feed...
					 */
					echo "<BR>Checking for torrents that need to be updated to be compatible with the RSS feed...<BR>\r\n";
					$recordset = @mysql_query("SELECT `info_hash`, `DateAdded` FROM `namemap`");
					if (!$recordset) {
						echo "Can't get a list of torrents, upgrade script halted.<BR><BR>\r\n";
					} else {
						$fails = 0;
						if (mysql_num_rows($recordset) > 0) {
							$updated = 0;
							while ($row = mysql_fetch_row($recordset)) {
								$tmpFail = false;
								$datearray = explode("-", $row[1]);
								$newtime = mktime(0,0,0,$datearray[1],$datearray[2], $datearray[0]);
								if (!@mysql_query("UPDATE `namemap` SET `torrent_size` = 10000, `tsAdded` = $newtime WHERE `info_hash` = '$row[0]'")) {
									$tmpFail = true;
									$fails++;
								} else {
									$updated++;
								}
							}

							echo "$updated torrent(s) have been updated successfully...<BR>\r\n";

							if ($fails > 0) {
								echo "$fails torrent(s) could not be updated!<BR>\r\n";
							}
						} else {
							echo "No torrents found.<BR>\r\n";
						}
					}

					/*
					 * We need to set up the initial order for the advanced sort...
					 */
					echo "<BR>Initializing the advanced sorting tables/fields...<BR>\r\n";
					$recordset = @mysql_query("SELECT `info_hash`,`category` FROM `namemap` ORDER BY `category`, `filename`");
					if (!$recordset) {
						echo "Can't get a list of torrents, upgrade script halted.<BR><BR>\r\n";
					} else {
						$oldcategory = '';
						if (mysql_num_rows($recordset) > 0) {
							$updated = 0;
							while ($row = mysql_fetch_row($recordset)) {
								if ($oldcategory != $row[1]) {
									$sortcounter = 1;
									$oldcategory = $row[1];
								}
								
								if (!@mysql_query("UPDATE `namemap` SET `grouping` = 0, `sorting` = $sortcounter WHERE `info_hash` = '$row[0]'")) {
									; // no need to do anything
								} else {
									$updated++;
								}
								$sortcounter++;
							}

							echo "$updated torrent(s) sorting have been updated successfully...<BR>\r\n";
						} else {
							echo "No torrents found to update.<BR>\r\n";
						}
					}


				}
			}
		}
	}

	if (isset($_POST["step4"])) {
		echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n<HTML>\r\n<HEAD>\r\n\t<META NAME=\"Author\" CONTENT=\"danomac\">\r\n";
		echo "\t<TITLE>PHPBTTracker+ Upgrade script</TITLE>\r\n</HEAD>\r\n<BODY>\r\n\t<FORM ACTION=\"upgrade.php\" METHOD=\"POST\">\r\n\t<H1>PHPBTTracker+ Upgrade</H1>\r\n";

		if (isset($_POST["upgrade_from_query"])) {
			switch ($_POST["upgrade_from_query"]) {
				case PHPBTP16:
					upgr_phpbttrkplus16();
					break;
				case PHPBTP17:
					upgr_phpbttrkplus17();
					break;
				case PHPBTP20:
					upgr_phpbttrkplus20();
					break;
				case PHPBTP21:
					upgr_phpbttrkplus21();
					break;
				case PHPBT15:
					upgr_phpbttrk15();
					break;
				default:
					echo "\t<FONT COLOR=RED><B>Upgrade halted; internal script error.</B></FONT>\r\n";
					break;
			}
		} else {
			echo "\t<FONT COLOR=RED><B>Upgrade halted; internal script error.</B></FONT>\r\n";
		}

		echo "\t</FORM>\r\n</BODY>\r\n</HTML>";
		exit;
	}

	if (isset($_POST["step3"])) {
		echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n<HTML>\r\n<HEAD>\r\n\t<META NAME=\"Author\" CONTENT=\"danomac\">\r\n";
		echo "\t<TITLE>PHPBTTracker+ Upgrade script</TITLE>\r\n</HEAD>\r\n<BODY>\r\n\t<FORM ACTION=\"upgrade.php\" METHOD=\"POST\">\r\n\t<H1>PHPBTTracker+ Upgrade</H1>\r\n";

		if (isset($_POST["old_version"])) {
			switch ($_POST["old_version"]) {
				case NO_SEL:
					echo "\t<FONT COLOR=RED><B>Upgrade halted; no prior version selected.</B></FONT>\r\n";
					break;
				case PHPBTP16:
					pre_upgr_phpbttrkplus16();
					break;
				case PHPBTP17:
					pre_upgr_phpbttrkplus17();
					break;
				case PHPBTP20:
					pre_upgr_phpbttrkplus20();
					break;
				case PHPBTP21:
					pre_upgr_phpbttrkplus21();
					break;
				case PHPBT15:
					pre_upgr_phpbttrk15();
					break;
				default:
					echo "\t<FONT COLOR=RED><B>Upgrade halted; no prior version selected.</B></FONT>\r\n";
					break;
			}
		} else {
			echo "\t<FONT COLOR=RED><B>Upgrade halted; no prior version selected.</B></FONT>\r\n";
		}

		echo "\t</FORM>\r\n</BODY>\r\n</HTML>";
		exit;
	}

	if (isset($_POST["step2"])) {
		echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n<HTML>\r\n<HEAD>\r\n\t<META NAME=\"Author\" CONTENT=\"danomac\">\r\n";
		echo "\t<TITLE>PHPBTTracker+ Upgrade script</TITLE>\r\n</HEAD>\r\n<BODY>\r\n\t<FORM ACTION=\"upgrade.php\" METHOD=\"POST\">\r\n\t<H1>PHPBTTracker+ Upgrade</H1>\r\n";

		if (isset($_POST["understood"]) && strcmp($_POST["understood"], "enabled") == 0) {
			echo "\tThis script <B>does not</B> provide a means to upgrade from the original version of phpbttracker (authored be DeHackEd.)<BR><BR>\r\n";
			echo "\tChoose the version you are upgrading from:&nbsp;&nbsp;\r\n";

			echo "\t<SELECT NAME=\"old_version\">\r\n";

			echo "\t\t<OPTION VALUE=" . NO_SEL . ">Make a selection</OPTION>\r\n";
			echo "\t\t<OPTION VALUE=" . PHPBTP16 . ">PHPBTTracker+ 1.6</OPTION>\r\n";
			echo "\t\t<OPTION VALUE=" . PHPBTP17 . ">PHPBTTracker+ 1.7</OPTION>\r\n";
			echo "\t\t<OPTION VALUE=" . PHPBTP20 . ">PHPBTTracker+ 2.0</OPTION>\r\n";
			echo "\t\t<OPTION VALUE=" . PHPBTP21 . ">PHPBTTracker+ 2.1</OPTION>\r\n";
			echo "\t\t<OPTION VALUE=" . PHPBT15 . ">Original PHPBTTracker 1.5e</OPTION>\r\n";

			echo "\t</SELECT>\r\n";

			echo "\t<BR><BR><INPUT TYPE=\"SUBMIT\" NAME=\"step3\" VALUE=\"Next -&gt;\">\r\n";
		} else {
			echo "\t<FONT COLOR=RED><B>Upgrade halted; please make sure you read the items on the first page and indicate so with the checkbox provided.</B></FONT>\r\n";
		}

		echo "\t</FORM>\r\n</BODY>\r\n</HTML>";
		exit;
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<TITLE>PHPBTTracker+ Upgrade script</TITLE>
</HEAD>
<BODY>
	<FORM ACTION="upgrade.php" METHOD="POST">
	<H1>PHPBTTracker+ Upgrade</H1>
	There are some <FONT COLOR=RED><U>very important</U></FONT> issues that you need to understand before proceeding.<BR><BR>
	<FONT COLOR=RED>PRE UPGRADE NOTES:</FONT><BR><BR>

	1. There is a PHP bug that affects file uploads to the server. It is not a serious bug, but if you upload something
	that has an apostrophe in the filename (') everything to the left of the apostrophe will be truncated from the filename.<BR><BR>

	2. <B>This script modifies your database!! <FONT COLOR=RED>BACK UP your database and original scripts first!</FONT></B> There is a possibility
	that this script can fail and leave your tracker in an <B>unusable state.</B> If this script fails it will tell you if you might have
	to restore from a backup.<BR><BR>

	3. <FONT SIZE="+2" COLOR=RED><B>This version requires MySQL Version 4.1.2 or greater!</B></FONT><BR><BR>
	<INPUT TYPE=CHECKBOX NAME="understood" VALUE="enabled"> I have read the above and want to continue.
	<BR><BR><INPUT TYPE="SUBMIT" NAME="step2" VALUE="Next -&gt;">
	</FORM>
</BODY>
</HTML>
