<?php
	/*
	 * Module:	external.php
	 * Description: This file connects to external trackers via the
	 * 		/scrape interface and updates external torrents stats on
	 * 		this tracker.
	 *
	 *       NOTE: This needs to be called from crontab or something at
	 * 				regular intervals. Every 10 minutes would be fine (IN CRONTAB!), as
	 *					there is a setting in config.php that controls how often
	 *					each external tracker is contacted.
	 *
	 *			PHP CONFIGURATION NOTE: This needs 'allow_url_fopen' to be TRUE in
	 *					php.ini for this module to work!
	 *
	 * Author:	danomac
	 * Written:	29-May-2004
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
	require_once ("BDecode.php");
	require_once ("funcsv2.php");
	require_once ("config.php");

	/*
	 * Logging constants: NONE is self explanatory, MINIMAL is basically start/stop/execution time of script,
	 *   VERBOSE is a detailed (debugging) log.
	 */
	define("EXTLOG_NONE", 0);
	define("EXTLOG_MINIMAL", 1);
	define("EXTLOG_VERBOSE", 2);

	$extlogfile = '/dev/null';
	$extlogtype = EXTLOG_NONE;

	/*
	 * Disable error reporting... damn foreach statement
	 */
	error_reporting(0);

	/*
	 * Don't edit below this comment
	 */

	/*
	 * Upgrade check: if the variable deciding to use the info_hash parameter or not
	 * isn't set, set a value
	 */
	if (!isset($GLOBALS["ext_batch_scrape"])) {
		$GLOBALS["ext_batch_scrape"] = false;
	}

	$script_starttime = time();
	if ($extlogtype >= EXTLOG_MINIMAL) error_log("-----------------------------------------------------\r\n" . date("Y-M-d/H:i:s (l)", $script_starttime) . " -- Script started\r\n", 3, $extlogfile);

	/*
	 * Check to see if allow_url_fopen is enabled, if not there is no point in
	 * continuing this script.
	 */
	if (ini_get("allow_url_fopen") == false) {
		if ($extlogtype >= EXTLOG_MINIMAL) error_log("CRITICAL ERROR: allow_url_fopen directive not set in php.ini. Script cannot continue.\r\n", 3, $extlogfile);
		exit;
	}

	/*
	 * Make a connection to the database
	 */
	if ($GLOBALS["persist"]) {
		if ($extlogtype >= EXTLOG_VERBOSE) error_log("\tAttempting to make a persistent connection to database server... ", 3, $extlogfile);
		$db = @mysql_pconnect($dbhost, $dbuser, $dbpass);
		if (!$db) {
			if ($extlogtype >= EXTLOG_VERBOSE) error_log("FAILED.\r\n", 3, $extlogfile);
			exit;
		} else {
			if ($extlogtype >= EXTLOG_VERBOSE) error_log("done\r\n", 3, $extlogfile);
		}
	} else {
		if ($extlogtype >= EXTLOG_VERBOSE) error_log("\tAttempting to make a connection to database server... ", 3, $extlogfile);
		$db = @mysql_connect($dbhost, $dbuser, $dbpass);
		if (!$db) {
			if ($extlogtype >= EXTLOG_VERBOSE) error_log("FAILED.\r\n", 3, $extlogfile);
			exit;
		} else {
			if ($extlogtype >= EXTLOG_VERBOSE) error_log("done\r\n", 3, $extlogfile);
		}
	}

	/*
	 * Open the database needed
	 */
	if ($extlogtype >= EXTLOG_VERBOSE) error_log("\tAttempting to open required database... ", 3, $extlogfile);
	$dbselresult = @mysql_select_db($database);
	if (!$dbselresult) {
		if ($extlogtype >= EXTLOG_VERBOSE) error_log("FAILED.\r\n", 3, $extlogfile);
		exit;
	} else {
		if ($extlogtype >= EXTLOG_VERBOSE) error_log("done\r\n", 3, $extlogfile);
	}

	if ($GLOBALS["ext_batch_scrape"]) {
		/*
		 * Get a list of external torrents from the database that need to be updated.
		 */
		if ($extlogtype >= EXTLOG_VERBOSE) error_log("\tQuerying database for external torrents... ", 3, $extlogfile);
		$recordset = @mysql_query("SELECT info_hash, scrape_url, last_update FROM trk_ext ORDER BY scrape_url");
		if (!$recordset) {
			if ($extlogtype >= EXTLOG_VERBOSE) error_log("FAILED.\r\n", 3, $extlogfile);
			exit;
		} else {
			$number_to_update = mysql_num_rows($recordset);
			if ($extlogtype >= EXTLOG_VERBOSE) error_log("done, $number_to_update external torrent(s) found.\r\n", 3, $extlogfile);
		}

		/*
		 * Only process if there's actually records to look at...
		 */
		if ($number_to_update == 0) {
			if ($extlogtype >= EXTLOG_MINIMAL) error_log("\tNo torrents to update; script has nothing to do.\r\n", 3, $extlogfile);
		} else {
			if ($extlogtype >= EXTLOG_VERBOSE) error_log("\tStarting to query other trackers for scrape data...\r\n", 3, $extlogfile);

			/*
			 * Go through the recordset and place things into an array for easier traversal...
			 */
			$lastscrapeurl = "";
			while ($row = mysql_fetch_row($recordset)) {
				if ($lastscrapeurl != $row[1]) {
					$scrapearray[$row[1]]["last_update"] = $row[2];
					$lastscrapeurl = $row[1];
				} else {
					if ($scrapearray[$row[1]]["last_update"] < $row[2] ) 
						$scrapearray[$row[1]]["last_update"] = $row[2];
				}
				$scrapearray[$row[1]]["info_hash"][] = $row[0];
			}

			/*
			 * Now that everything is in the array, let's walk through
			 * the array gathering whatever data we need...
			 */
			$updatedtorrents = 0;
			foreach ($scrapearray as $scrape_url => $value) {
				/*
				 * The variable $GLOBALS["external_refresh"] is set in config.php, and prevents this
				 * script from hammering external trackers /scrape output.
				 *
				 * So, check to make sure enough time has passed before asking for more results.
				 */
				if (($script_starttime + $GLOBALS["external_refresh_tolerance"]) - $scrapearray[$scrape_url]["last_update"] >= $GLOBALS["external_refresh"] * 60) {
					if ($extlogtype >= EXTLOG_VERBOSE) error_log("\t\tContacting $scrape_url... ", 3, $extlogfile);

					/*
					 * OK, enough time passed, get a refreshed scrape response
					 */
					$scrape_metadata = @file_get_contents($scrape_url);
					if ($scrape_metadata == false) {
						if ($extlogtype >= EXTLOG_VERBOSE) error_log("FAILED.\r\n", 3, $extlogfile);

						/*
						 * Seeing as we couldn't get current stats, we should just set them to zero (0)
						 * as we don't know how the torrent is doing...
						 */
						for ($i=0; $i < count($scrapearray[$scrape_url]["info_hash"]); $i++) {
							$query = "UPDATE summary SET seeds=0, leechers=0, finished=0, dlbytes=0, speed=0, avgdone=0 WHERE info_hash=\"".$scrapearray[$scrape_url]["info_hash"][$i]."\"";
							if ($extlogtype >= EXTLOG_VERBOSE) { error_log("\t\t\t\t\t--> Query: $query\r\n", 3, $extlogfile); }
							if ($extlogtype >= EXTLOG_VERBOSE) { error_log("\t\t\t\t\t--> Updating database... ", 3, $extlogfile); }
							$queryresult = @mysql_query($query);
							if (!$queryresult) {
								if ($extlogtype >= EXTLOG_VERBOSE) { error_log("FAILED!\r\n", 3, $extlogfile); }									
							} else {
								if ($extlogtype >= EXTLOG_VERBOSE) { error_log("done\r\n", 3, $extlogfile); }
								$updatedtorrents++;
							}
						}
					} else {
						if ($extlogtype >= EXTLOG_VERBOSE) error_log("done\r\n", 3, $extlogfile);				
						if ($extlogtype >= EXTLOG_VERBOSE) error_log("\t\t\tValidating /scrape data... ", 3, $extlogfile);				

						/*
						 * Decode the scrape response
						 */
						$decoded_scrape = BDecode($scrape_metadata);
						if (!$decoded_scrape) {
							if ($extlogtype >= EXTLOG_VERBOSE) error_log("FAILED.\r\n", 3, $extlogfile);				
						} else {
							if ($extlogtype >= EXTLOG_VERBOSE) error_log("done\r\n", 3, $extlogfile);				

							/*
							 * Now the fun begins... walk through the decoded output and see if the info_hash is in
							 * the output, for all of the torrents that belong to this tracker.
							 */
							for ($i=0; $i < count($scrapearray[$scrape_url]["info_hash"]); $i++) {
								if ($extlogtype >= EXTLOG_VERBOSE) {error_log("\t\t\t\tLooking for ".$scrapearray[$scrape_url]["info_hash"][$i]."... ", 3, $extlogfile); }				
								$foundhash = false;
								foreach ($decoded_scrape["files"] as $binaryhash => $scrapevalue) {
									/*
									 * OK, do they match? (stripslashes has to be applied to compare this value,
									 * took me a while to figure that out...
									 */
									if (strcmp($scrapearray[$scrape_url]["info_hash"][$i], bin2hex(stripslashes($binaryhash))) == 0) {
										if ($extlogtype >= EXTLOG_VERBOSE) { error_log("found!\r\n", 3, $extlogfile); }
										$foundhash = true;
	
										/*
										 * Need to dynamically build a query string.
										 */
										$query = "UPDATE summary SET ";
	
										/*
										 * Seeders
										 */
										if (isset($decoded_scrape["files"][$binaryhash]["complete"])) {
											if (is_numeric($decoded_scrape["files"][$binaryhash]["complete"])) {
												$query .= "seeds=".$decoded_scrape["files"][$binaryhash]["complete"].", ";
											} else {
												$query .= "seeds=0, ";				
											}
										} else {
											$query .= "seeds=0, ";				
										}

										/*
										 * Leechers
										 */
										if (isset($decoded_scrape["files"][$binaryhash]["incomplete"])) {
											if (is_numeric($decoded_scrape["files"][$binaryhash]["incomplete"])) {
												$query .= "leechers=".$decoded_scrape["files"][$binaryhash]["incomplete"].", ";
											} else {
												$query .= "leechers=0, ";				
											}
										} else {
											$query .= "leechers=0, ";				
										}
	
										/*
										 * Total downloads
										 */
										if (isset($decoded_scrape["files"][$binaryhash]["downloaded"])) {
											if (is_numeric($decoded_scrape["files"][$binaryhash]["downloaded"])) {
												$query .= "finished=".$decoded_scrape["files"][$binaryhash]["downloaded"].", ";
											} else {
												$query .= "finished=0, ";				
											}
										} else {
											$query .= "finished=0, ";				
										}

										/*
										 * Average done
										 */
										if (isset($decoded_scrape["files"][$binaryhash]["averagedone"])) {
											if (is_numeric($decoded_scrape["files"][$binaryhash]["averagedone"])) {
												$query .= "avgdone=".$decoded_scrape["files"][$binaryhash]["averagedone"].", ";
											} else {
												$query .= "avgdone=0, ";				
											}
										} else {
											$query .= "avgdone=0, ";				
										}
	
										/*
										 * Speed
										 */
										if (isset($decoded_scrape["files"][$binaryhash]["speed"])) {
											if (is_numeric($decoded_scrape["files"][$binaryhash]["speed"])) {
												$speed = $decoded_scrape["files"][$binaryhash]["speed"];
	
												/*
												 * This actually can have a "unit" attached to it (2 are valid: KiB, and MiB)
												 * so we need to check to see if the unit is there and modify the value accordingly.
												 * No unit defined here means bytes/second.
												 */
												if (isset($decoded_scrape["files"][$binaryhash]["speedunit"])) {
													switch($decoded_scrape["files"][$binaryhash]["speedunit"]) {
														case "KiB":
															$query .= "speed=$speed * 1024, ";
															break;
														case "MiB":
															$query .= "speed=$speed * 1048576, ";
															break;
														default:
															$query .= "speed=$speed, ";
															break;
													}
												} else {
													$query .= "speed=$speed, ";
												}
											} else {
												$query .= "speed=0, ";				
											}
										} else {
											$query .= "speed=0, ";				
										}
	
										/*
										 * Amount downloaded
										 */
										if (isset($decoded_scrape["files"][$binaryhash]["transferred"])) {
											if (is_numeric($decoded_scrape["files"][$binaryhash]["transferred"])) {
												$transferred = $decoded_scrape["files"][$binaryhash]["transferred"];
	
												/*
												 * This actually can have a "unit" attached to it (3 are valid: KiB, MiB, GiB, and TiB)
												 * so we need to check to see if the unit is there and modify the value accordingly.
												 * No unit defined here means bytes.
												 */
												if (isset($decoded_scrape["files"][$binaryhash]["transferredunit"])) {
													switch($decoded_scrape["files"][$binaryhash]["transferredunit"]) {
														case "KiB":
															$query .= "dlbytes=$transferred * 1024 ";
															break;
														case "MiB":
															$query .= "dlbytes=$transferred * 1048576 ";
															break;
														case "GiB":
															$query .= "dlbytes=$transferred * 1073741824 ";
															break;
														case "TiB":
															$query .= "dlbytes=$transferred * 1099511627776 ";
															break;
														default:
															$query .= "dlbytes=$transferred ";
															break;
													}
												} else {
													$query .= "dlbytes=$transferred ";
												}
											} else {
												$query .= "dlbytes=0 ";				
											}
										} else {
											$query .= "dlbytes=0 ";				
										}
	
										$query .= "WHERE info_hash=\"".$scrapearray[$scrape_url]["info_hash"][$i]."\"";
										if ($extlogtype >= EXTLOG_VERBOSE) { error_log("\t\t\t\t\t--> Query: $query\r\n", 3, $extlogfile); }
	
										/*
										 * Commit the changes
										 */
										if ($extlogtype >= EXTLOG_VERBOSE) { error_log("\t\t\t\t\t--> Updating database... ", 3, $extlogfile); }
										$queryresult = @mysql_query($query);
										if (!$queryresult) {
											if ($extlogtype >= EXTLOG_VERBOSE) { error_log("FAILED!\r\n", 3, $extlogfile); }									
										} else {
											if ($extlogtype >= EXTLOG_VERBOSE) { error_log("done\r\n", 3, $extlogfile); }
											$updatedtorrents++;
										}
									}
								}
	
								if (!$foundhash) {
									/*
									 * The hash wasn't found, so we'll just set it's data back to 0.
									 */
									if ($extlogtype >= EXTLOG_VERBOSE) { error_log("NOT FOUND.\r\n", 3, $extlogfile); }
									$query = "UPDATE summary SET seeds=0, leechers=0, finished=0, dlbytes=0, speed=0, avgdone=0 WHERE info_hash=\"".$scrapearray[$scrape_url]["info_hash"][$i]."\"";
									if ($extlogtype >= EXTLOG_VERBOSE) { error_log("\t\t\t\t\t--> Query: $query\r\n", 3, $extlogfile); }
									if ($extlogtype >= EXTLOG_VERBOSE) { error_log("\t\t\t\t\t--> Updating database... ", 3, $extlogfile); }
									$queryresult = @mysql_query($query);
									if (!$queryresult) {
										if ($extlogtype >= EXTLOG_VERBOSE) { error_log("FAILED!\r\n", 3, $extlogfile); }									
									} else {
										if ($extlogtype >= EXTLOG_VERBOSE) { error_log("done\r\n", 3, $extlogfile); }
										$updatedtorrents++;
									}
								}
							}

							if ($extlogtype >= EXTLOG_VERBOSE) { error_log("\t\t\tUpdating last update time in external torrents table... ", 3, $extlogfile); }
							$queryresult = @mysql_query("UPDATE trk_ext SET last_update=$script_starttime WHERE scrape_url=\"$scrape_url\"");
							if (!$queryresult) {
								if ($extlogtype >= EXTLOG_VERBOSE) { error_log("FAILED!\r\n", 3, $extlogfile); }									
							} else {
								if ($extlogtype >= EXTLOG_VERBOSE) { error_log("done\r\n", 3, $extlogfile); }
							}
						}
					}
				} else {
					/*
					 * Not enough time has passed, skip this one...
					 */
					$time_to_wait = round((($GLOBALS["external_refresh"] * 60) - ($script_starttime - $scrapearray[$scrape_url]["last_update"])) / 60, 1);
					if ($extlogtype >= EXTLOG_VERBOSE) error_log("\t\tSkipping $scrape_url, have not waited long enough to requery scrape data, waiting another $time_to_wait minute(s)\r\n", 3, $extlogfile);
				}
			}
		}
	} else {
		/*
		 * We will use the info_hash parameter for each torrent individually,
		 * rather than grabbing the whole /scrape results from the tracker.
		 */
//--------------------------------------
		/*
		 * Get a list of external torrents from the database that need to be updated.
		 */
		if ($extlogtype >= EXTLOG_VERBOSE) error_log("\tUsing info_hash parameter on scrape URLs.\r\n", 3, $extlogfile);
		if ($extlogtype >= EXTLOG_VERBOSE) error_log("\tQuerying database for external torrents... ", 3, $extlogfile);
		$recordset = @mysql_query("SELECT info_hash, scrape_url, last_update FROM trk_ext");
		if (!$recordset) {
			if ($extlogtype >= EXTLOG_VERBOSE) error_log("FAILED.\r\n", 3, $extlogfile);
			exit;
		} else {
			$number_to_update = mysql_num_rows($recordset);
			if ($extlogtype >= EXTLOG_VERBOSE) error_log("done, $number_to_update external torrent(s) found.\r\n", 3, $extlogfile);
		}

		/*
		 * Only process if there's actually records to look at...
		 */
		if ($number_to_update == 0) {
			if ($extlogtype >= EXTLOG_MINIMAL) error_log("\tNo torrents to update; script has nothing to do.\r\n", 3, $extlogfile);
		} else {
			if ($extlogtype >= EXTLOG_VERBOSE) error_log("\tStarting to query other trackers for scrape data...\r\n", 3, $extlogfile);

			/*
			 * Go through the recordset and place things into an array for easier traversal...
			 */
			$lastscrapeurl = "";
			while ($row = mysql_fetch_row($recordset)) {
				$scrapearray[] = array('info_hash' => $row[0], 'scrape_url' => $row[1], 'lastupdate' => $row[2]);
			}

			/*
			 * Now that everything is in the array, let's walk through
			 * the array gathering whatever data we need...
			 */
			$updatedtorrents = 0;
			foreach ($scrapearray as $scrape_index => $scrape_info) {
				/*
				 * The variable $GLOBALS["external_refresh"] is set in config.php, and prevents this
				 * script from hammering external trackers /scrape output.
				 *
				 * So, check to make sure enough time has passed before asking for more results.
				 */
				if (($script_starttime + $GLOBALS["external_refresh_tolerance"]) - $scrape_info["lastupdate"] >= $GLOBALS["external_refresh"] * 60) {
					/*
					 * Need to append the info_hash parameter to the scrape url.
					 */
					$scrape_urlhex = $scrape_info["scrape_url"] . "?info_hash=" . $scrape_info["info_hash"];
					$scrape_url = $scrape_info["scrape_url"] . "?info_hash=" . urlencode(pack("H*", $scrape_info["info_hash"]));
					if ($extlogtype >= EXTLOG_VERBOSE) error_log("\t\tContacting $scrape_urlhex... ", 3, $extlogfile);

					/*
					 * OK, enough time passed, get a refreshed scrape response
					 */
					$scrape_metadata = @file_get_contents($scrape_url);
					if ($scrape_metadata == false) {
						if ($extlogtype >= EXTLOG_VERBOSE) error_log("FAILED.\r\n", 3, $extlogfile);
					} else {
						if ($extlogtype >= EXTLOG_VERBOSE) error_log("done\r\n", 3, $extlogfile);				
						if ($extlogtype >= EXTLOG_VERBOSE) error_log("\t\t\tValidating /scrape data... ", 3, $extlogfile);				

						/*
						 * Decode the scrape response
						 */
						$decoded_scrape = BDecode($scrape_metadata);
						if (!$decoded_scrape) {
							if ($extlogtype >= EXTLOG_VERBOSE) error_log("FAILED.\r\n", 3, $extlogfile);				
						} else {
							if ($extlogtype >= EXTLOG_VERBOSE) error_log("done\r\n", 3, $extlogfile);				

							/*
							 * Now the fun begins... walk through the decoded output and see if the info_hash is in
							 * the output, for all of the torrents that belong to this tracker.
							 */
							if ($extlogtype >= EXTLOG_VERBOSE) {error_log("\t\t\t\tLooking for ".$scrape_info["info_hash"]."... ", 3, $extlogfile); }				
							$foundhash = false;
							foreach ($decoded_scrape["files"] as $binaryhash => $scrapevalue) {
								/*
								 * OK, do they match? (stripslashes has to be applied to compare this value,
								 * took me a while to figure that out...
								 */
								if (strcmp($scrape_info["info_hash"], bin2hex(stripslashes($binaryhash))) == 0) {
									if ($extlogtype >= EXTLOG_VERBOSE) { error_log("found!\r\n", 3, $extlogfile); }
									$foundhash = true;
	
									/*
									 * Need to dynamically build a query string.
									 */
									$query = "UPDATE summary SET ";
	
									/*
									 * Seeders
									 */
									if (isset($decoded_scrape["files"][$binaryhash]["complete"])) {
										if (is_numeric($decoded_scrape["files"][$binaryhash]["complete"])) {
											$query .= "seeds=".$decoded_scrape["files"][$binaryhash]["complete"].", ";
										} else {
											$query .= "seeds=0, ";				
										}
									} else {
										$query .= "seeds=0, ";				
									}

									/*
									 * Leechers
									 */
									if (isset($decoded_scrape["files"][$binaryhash]["incomplete"])) {
										if (is_numeric($decoded_scrape["files"][$binaryhash]["incomplete"])) {
											$query .= "leechers=".$decoded_scrape["files"][$binaryhash]["incomplete"].", ";
										} else {
											$query .= "leechers=0, ";				
										}
									} else {
										$query .= "leechers=0, ";				
									}
	
									/*
									 * Total downloads
									 */
									if (isset($decoded_scrape["files"][$binaryhash]["downloaded"])) {
										if (is_numeric($decoded_scrape["files"][$binaryhash]["downloaded"])) {
											$query .= "finished=".$decoded_scrape["files"][$binaryhash]["downloaded"].", ";
										} else {
											$query .= "finished=0, ";				
										}
									} else {
										$query .= "finished=0, ";				
									}

									/*
									 * Average done
									 */
									if (isset($decoded_scrape["files"][$binaryhash]["averagedone"])) {
										if (is_numeric($decoded_scrape["files"][$binaryhash]["averagedone"])) {
											$query .= "avgdone=".$decoded_scrape["files"][$binaryhash]["averagedone"].", ";
										} else {
											$query .= "avgdone=0, ";				
										}
									} else {
										$query .= "avgdone=0, ";				
									}
	
									/*
									 * Speed
									 */
									if (isset($decoded_scrape["files"][$binaryhash]["speed"])) {
										if (is_numeric($decoded_scrape["files"][$binaryhash]["speed"])) {
											$speed = $decoded_scrape["files"][$binaryhash]["speed"];
	
											/*
											 * This actually can have a "unit" attached to it (2 are valid: KiB, and MiB)
											 * so we need to check to see if the unit is there and modify the value accordingly.
											 * No unit defined here means bytes/second.
											 */
											if (isset($decoded_scrape["files"][$binaryhash]["speedunit"])) {
												switch($decoded_scrape["files"][$binaryhash]["speedunit"]) {
													case "KiB":
														$query .= "speed=$speed * 1024, ";
														break;
													case "MiB":
														$query .= "speed=$speed * 1048576, ";
														break;
													default:
														$query .= "speed=$speed, ";
														break;
												}
											} else {
												$query .= "speed=$speed, ";
											}
										} else {
											$query .= "speed=0, ";				
										}
									} else {
										$query .= "speed=0, ";				
									}
	
									/*
									 * Amount downloaded
									 */
									if (isset($decoded_scrape["files"][$binaryhash]["transferred"])) {
										if (is_numeric($decoded_scrape["files"][$binaryhash]["transferred"])) {
											$transferred = $decoded_scrape["files"][$binaryhash]["transferred"];

											/*
											 * This actually can have a "unit" attached to it (3 are valid: KiB, MiB, GiB, and TiB)
											 * so we need to check to see if the unit is there and modify the value accordingly.
											 * No unit defined here means bytes.
											 */
											if (isset($decoded_scrape["files"][$binaryhash]["transferredunit"])) {
												switch($decoded_scrape["files"][$binaryhash]["transferredunit"]) {
													case "KiB":
														$query .= "dlbytes=$transferred * 1024 ";
														break;
													case "MiB":
														$query .= "dlbytes=$transferred * 1048576 ";
														break;
													case "GiB":
														$query .= "dlbytes=$transferred * 1073741824 ";
														break;
													case "TiB":
														$query .= "dlbytes=$transferred * 1099511627776 ";
														break;
													default:
														$query .= "dlbytes=$transferred ";
														break;
												}
											} else {
												$query .= "dlbytes=$transferred ";
											}
										} else {
											$query .= "dlbytes=0 ";				
										}
									} else {
										$query .= "dlbytes=0 ";				
									}
	
									$query .= "WHERE info_hash=\"".$scrape_info["info_hash"]."\"";
									if ($extlogtype >= EXTLOG_VERBOSE) { error_log("\t\t\t\t\t--> Query: $query\r\n", 3, $extlogfile); }
	
									/*
									 * Commit the changes
									 */
									if ($extlogtype >= EXTLOG_VERBOSE) { error_log("\t\t\t\t\t--> Updating database... ", 3, $extlogfile); }
									$queryresult = @mysql_query($query);
									if (!$queryresult) {
										if ($extlogtype >= EXTLOG_VERBOSE) { error_log("FAILED!\r\n", 3, $extlogfile); }									
									} else {
										if ($extlogtype >= EXTLOG_VERBOSE) { error_log("done\r\n", 3, $extlogfile); }
										$updatedtorrents++;
									}

								}
							}
	
							if (!$foundhash) {
								/*
								 * The hash wasn't found, so we'll just set it's data back to 0.
								 */
								if ($extlogtype >= EXTLOG_VERBOSE) { error_log("NOT FOUND.\r\n", 3, $extlogfile); }
								$query = "UPDATE summary SET seeds=0, leechers=0, finished=0, dlbytes=0, speed=0, avgdone=0 WHERE info_hash=\"".$scrape_info["info_hash"]."\"";
								if ($extlogtype >= EXTLOG_VERBOSE) { error_log("\t\t\t\t\t--> Query: $query\r\n", 3, $extlogfile); }
								if ($extlogtype >= EXTLOG_VERBOSE) { error_log("\t\t\t\t\t--> Updating database... ", 3, $extlogfile); }
								$queryresult = @mysql_query($query);
								if (!$queryresult) {
									if ($extlogtype >= EXTLOG_VERBOSE) { error_log("FAILED!\r\n", 3, $extlogfile); }									
								} else {
									if ($extlogtype >= EXTLOG_VERBOSE) { error_log("done\r\n", 3, $extlogfile); }
									$updatedtorrents++;
								}
							}

							if ($extlogtype >= EXTLOG_VERBOSE) { error_log("\t\t\tUpdating last update time for this hash... ", 3, $extlogfile); }
							$queryresult = @mysql_query("UPDATE trk_ext SET last_update=$script_starttime WHERE info_hash=\"". $scrape_info["info_hash"] ."\"");
							if (!$queryresult) {
								if ($extlogtype >= EXTLOG_VERBOSE) { error_log("FAILED!\r\n", 3, $extlogfile); }									
							} else {
								if ($extlogtype >= EXTLOG_VERBOSE) { error_log("done\r\n", 3, $extlogfile); }
							}
						}
					}
				} else {
					/*
					 * Not enough time has passed, skip this one...
					 */
					$time_to_wait = round((($GLOBALS["external_refresh"] * 60) - ($script_starttime - $scrape_info["lastupdate"])) / 60, 1);
					if ($extlogtype >= EXTLOG_VERBOSE) error_log("\t\tSkipping hash ".$scrape_info["info_hash"].", have not waited long enough to requery scrape data, waiting another $time_to_wait minute(s)\r\n", 3, $extlogfile);
				}
			}
		}
	}

	if ($extlogtype >= EXTLOG_MINIMAL) error_log("\tUpdated $updatedtorrents torrent(s).\r\n", 3, $extlogfile);
	$script_stoptime = time();
	$execution_time = $script_stoptime - $script_starttime;
	if ($extlogtype >= EXTLOG_MINIMAL) error_log(date("Y-M-d/H:i:s (l)", $script_stoptime) . " -- Script terminated normally, execution time was $execution_time second(s).\r\n", 3, $extlogfile);
	exit;
?>
