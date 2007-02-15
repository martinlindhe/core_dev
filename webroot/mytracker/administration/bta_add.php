<?php
	/*
	 * Module:	bta_add.php
	 * Description: This is the add torrent screen of the administrative interface.
	 * 		This module inserts a torrent's details in the database so it can
	 * 		be tracked.
	 *   		Various fields added and changed sql statements to update info.. also added 
	 *		session tracking so you don't have to enter user/pass all the time.
	 *		Code was used from the the DeHackEd's script for adding torrents as well.
	 *
	 * Author:	danomac
	 * Written:	17-Febrary-2004
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
	 * If not, redirect them back to main
	 */
	if (!($_SESSION["admin_perms"]["add"] || $_SESSION["admin_perms"]["root"])) {
		admShowMsg("You don't have permission to view this page.", "Redirecting to the main administration panel.",
			       $adm_page_title, true, "bta_main.php", 3);
	}

	/*
	 * Set the message to an empty string. If there is content in this string, it
	 * is displayed in the table. Also sets an "error" variable so skipping processing
	 * can be done.
	 */
	$statusMsg = "";
	$addError = false;

	/*
	 * If the button was pressed, let's attempt to add the torrent.
	 */
	if (isset($_POST["addtorrent"])) {
		/*
		 * Connect to the database
		 */
		if ($GLOBALS["persist"])
			$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		else
			$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		mysql_select_db($database) or die("Can't open the database.");

		/*
		 * Is uploaded torrent file supposed to be processed?
		 */
		if (isset($_POST["retry_torrent"])) {
			$processuploadedtorrent = ( $_POST["retry_torrent"] == 10 ) ? true : false;
		} else {
			$processuploadedtorrent = true;
		}

		/*
		 * If a file was uploaded, get info about the torrent.
		 * If uploading wasn't available, get the torrent size and hash from the form.
		 */
		if (isset($_FILES["torrentfile"]) && $processuploadedtorrent) {
			/*
			 * Okay, let's see if the torrent upload was ok. PHP will delete the file at the end of the
			 * session if it isn't used, so only if the upload is OK will we process it.
			 */
			switch ($_FILES["torrentfile"]["error"]) {
				case UPLOAD_ERR_OK:	//file uploaded ok
					/*
					 * Make sure the file was uploaded through the POST form
					 */
					if (is_uploaded_file($_FILES["torrentfile"]["tmp_name"])) {
						/*
						 * Is it actually a torrent?
						 */
						if (substr($_FILES["torrentfile"]["name"], -8) == '.torrent') {
							/*
							 * The HTML Anchor character (#), the control character (^), and the semicolon (;) is not allowed in torrent names, 
							 * so check for it and report an error if one is found.
							 */
							if (!stristr($_FILES["torrentfile"]["name"], "#") && !stristr($_FILES["torrentfile"]["name"], "^") && !stristr($_FILES["torrentfile"]["name"], ";")) {
								/*
								 * Check to see if the size is too large. If it is, terminate with an error.
								 */
								if ($_FILES["torrentfile"]["size"] < $GLOBALS["max_torrent_size"]) {
									/*
									 * Okay, it seems to be good. Open, the file and decode it, and close it.
									 */
									$torrent_metadata_size = filesize($_FILES["torrentfile"]["tmp_name"]);
									if ($torrent_metadata_size == 0) {
										$statusMsg = "Torrent with length of 0 (zero) bytes uploaded. Please check your torrent.";
										$addError = true;
									} else {
										$fd = fopen($_FILES["torrentfile"]["tmp_name"], "rb") or die("File upload error 1\n");
										$alltorrent = @fread($fd, $torrent_metadata_size);
										$array = BDecode($alltorrent);
										fclose($fd);
		
										/*
										 * Make sure it's been decoded properly.
										 */
										if (!isset($array)) {
											$statusMsg = "There was an error handling your uploaded torrent. The parser didn't like it.";
											$addError = true;
										}
										if (!$array && !addError) {
											$statusMsg = "There was an error handling your uploaded torrent. The parser didn't like it.";
											$addError = true;
										}

										/*
										 * First, we need to decide if this is a mirror torrent. If it is, deal with it first;
										 * otherwise process as normal.
										 */
										$addmirrortorrent = false;
										if (isset($_POST["mirrortorrent"])) {
											if (strcmp($_POST["mirrortorrent"], "enabled") == 0) {
												$addmirrortorrent = true;
											}
										} 

										if ($addmirrortorrent) {
											/*
											 * First check to see if backup tracker are even present...
											 */
											if (!isset($array["announce-list"])) {
												$statusMsg = "There are no backup trackers present in this torrent.";
												$addError = true;
											} else {
												/*
												 * Make sure user is allowed to add a mirror torrent...
												 */
												if (!($_SESSION["admin_perms"]["addmirror"] || $_SESSION["admin_perms"]["root"])) {
													$statusMsg = "You are not allowed to add mirror torrents.";
													$addError = true;
												} else {
													/*
													 * Let's see if the tracker is specified as a mirror; if not throw an error
													 */
													$mirror_ok = false;
													foreach ($array["announce-list"] as $key => $value) {
														foreach($array["announce-list"][$key] as $indice => $mirror_tracker_announce) {
															if (strcmp($mirror_tracker_announce, $GLOBALS["my_tracker_announce"]) == 0) {
																$mirror_ok = true;
															}
														}
													}

													if (!$mirror_ok) {
														$statusMsg = "This torrent does not have this tracker listed as a backup.";
														$addError = true;
													}
												}
											}
										} else {
											/*
											 * Check to see if this is an external torrent, and then check if user is allowed to add
											 * external torrents. If they are not allowed, abort.
											 */
											if (strcmp($GLOBALS["my_tracker_announce"], $array["announce"]) != 0) {
												/*
												 * This appears to be an external torrent, check permissions
												 */
												if (!($_SESSION["admin_perms"]["addext"] || $_SESSION["admin_perms"]["root"])) {
													$statusMsg = "You are not allowed to add external torrents; check your announce url.";
													$addError = true;
												}
											}
										}
									}

									/*
									 * Okay, if it's been decoded OK, so get the hash, calculate the file size,
									 * and if enabled, copy it to the specified folder.
									 */					
									if (!$addError) {
										$hash = sha1(BEncode($array["info"]));

										/*
										 * Do a proper calculation on torrent size...
										 */
										if (isset($array["info"]["files"])) {
											/*
											 * Multiple file torrent
											 */
											$sizectr=0;
											for ($j=0; $j < count($array["info"]["files"]); $j++)
												$sizectr += $array["info"]["files"][$j]["length"];

											$filesize = $sizectr / 1048576;
										} else {
											/*
											 * Single file torrent
											 */
											$filesize = $array["info"]["length"] / 1048576;
										}
										$filesize = round($filesize, 2);

										/*
										 * Copy to server, if needed
										 */
										$copiedTorrent = false;
										if ($GLOBALS["allow_torrent_move"]) {
											/*
											 * Add to database or just copy it?
											 */
											if ($GLOBALS["move_to_db"]) {
												/*
												 * Add to database, if enabled.
												 */
												if (isset($_POST["copytorrent"]) || isset($_POST["copytorrentonly"])) {
													if (strcmp($_POST["copytorrent"], "enabled") == 0 || strcmp($_POST["copytorrentonly"], "enabled") == 0) {
														/*
														 * Put the torrent in the database...
														 * Need to do some preparation first
														 */
														if (!get_magic_quotes_gpc()) {
															$sql_filename = addslashes($_FILES["torrentfile"]["name"]);
														} else {
															$sql_filename = $_FILES["torrentfile"]["name"];
														}

														$sql_metadata = base64_encode($alltorrent);

														if ((strlen($hash) != 40) || !verifyHash($hash)) {
															$statusMsg = "ERROR: Hash value must be exactly 40 hex bytes. Torrent not added to database.<BR>";
														} else {
															$sql_result = mysql_query("INSERT INTO `torrents` (`info_hash`, `name`, `metadata`) VALUES (\"$hash\", \"$sql_filename\", \"$sql_metadata\")");

															if (!$sql_result) {
																if (isset($_POST["copytorrentonly"]) && strcmp($_POST["copytorrentonly"], "enabled") == 0) {
																	admShowMsg("Torrent was NOT uploaded!", "Upload failed - Returning to the main administration page.", "Redirecting", true, "bta_main.php", 2);
																} else {
																	$statusMsg = "WARNING: Torrent not uploaded to database. Has it been added already?<BR>";
																}
															} else {
																if (isset($_POST["copytorrentonly"]) && strcmp($_POST["copytorrentonly"], "enabled") == 0) {
																	/*
																	 * Display the status to the user
																	 */
																	admShowMsg("Changes applied.", "Torrent uploaded successfully - Returning to the main administration page.", "Redirecting", true, "bta_main.php", 2);
																}

																/*
																 * Now that it's successful, build the URL needed to download it.
																 * 2005-08-03: changed this slightly so it uses the directory directly above the admin interface
																 */
																$path = substr($_SERVER["REQUEST_URI"], 0, strrpos($_SERVER["REQUEST_URI"], "/" ));
																$prevpath = substr($path, 0, strrpos($path, "/" ));

																$url = "http://" . $_SERVER["HTTP_HOST"] . $prevpath . "/gettorrent.php?info_hash=" . $hash;
																$copiedTorrent = true;
															}
														}
													}
												}
											} else {
												/*
												 * Move the file, if enabled. docroot is /dev/null by default. so let's not
												 * bother moving it if the http document root isn't specified.
												 * A variable is set if it has been copied. If it is copied successfully,
												 * spaces will be filled with '_' and the URL will be set automagically.
												 */ 
												if (isset($_POST["copytorrent"]) || isset($_POST["copytorrentonly"])) {
													if (strcmp($_POST["copytorrent"], "enabled") == 0 || strcmp($_POST["copytorrentonly"], "enabled") == 0) {
														/*
														 * Build a path using the html document root and the requested subfolder.
														 */
														$movetopath = $_SERVER["DOCUMENT_ROOT"] . "/" . $GLOBALS["torrent_folder"];
														$movetopath = stripslashes($movetopath);

														/*
														 * Take the original filename, and replace all the spaces with underscores.
														 */
														$copyFilename = stripslashes($_FILES["torrentfile"]["name"]);
														$copyFilename = ereg_replace(" ","_", $copyFilename);

														/*
														 * Move the file, supressing warnings. If the file does NOT copy to the dest
														 * folder, it is a permissions problem. The apache processes MUST have write
														 * access to the torrent folder.
														 */
														$movetopath .= "/" . $copyFilename;
														if (!(@move_uploaded_file($_FILES["torrentfile"]["tmp_name"],  $movetopath))) {
															$statusMsg = "WARNING: Torrent was NOT copied to torrent folder.<BR>";
															if (isset($_POST["copytorrentonly"]) && strcmp($_POST["copytorrentonly"], "enabled") == 0) {
																/*
																 * Display the status to the user
																 */
																admShowMsg("Torrent was NOT uploaded!", "Upload failed - Returning to the main administration page.", "Redirecting", true, "bta_main.php", 2);
															}
														} else {
															/*
															 * Now that it's moved, set the URL to that new file name, and set the
															 * flag to true, so it isn't accidentally overwritten later.
															 * Also change the % to %25 for URL encoding when uploading...
															 */		
															$copyFilename = ereg_replace("%","%25", $copyFilename);
															$url = "http://" . $_SERVER["HTTP_HOST"] . "/" . $GLOBALS["torrent_folder"] . "/" . $copyFilename;
															$copiedTorrent = true;
														}

														/*
														 * If only copying the torrent redirect back to the main page here...
														 */
														if (isset($_POST["copytorrentonly"]) && strcmp($_POST["copytorrentonly"], "enabled") == 0) {
															/*
															 * Display the status to the user
															 */
															admShowMsg("Changes applied.", "Torrent uploaded successfully - Returning to the main administration page.", "Redirecting", true, "bta_main.php", 2);
														}
													}
												}
											}
										}
									}
								} else {
									$statusMsg = "Torrent size is too large.";
									$addError = true;
								}
							} else {
								$statusMsg = "Torrents CANNOT contain a HTML Anchor character (#), a control character (^), or a semicolon (;) in the filename.";
								$addError = true;
							}
						} else {
							$statusMsg = "Only torrents can be uploaded.";
							$addError = true;
						}
					}
					/*
					 * Remove the file, if we didn't copy it somewhere.
					 */
					if (!$copiedTorrent) unlink($_FILES["torrentfile"]["tmp_name"]);
					break;
				case UPLOAD_ERR_INI_SIZE:	//php settings denied upload (php.ini)
					$statusMsg = "Upload failed. Torrent size is too big. Please try again.";
					$addError = true;					
					break;
				case UPLOAD_ERR_FORM_SIZE:	//size specified in form exceeded
					$statusMsg = "Upload failed. Torrent size is too big. Please try again.";
					$addError = true;					
					break;
				case UPLOAD_ERR_PARTIAL:	//file upload incomplete
					$statusMsg = "Upload failed. Only a part of the torrent was received. Please try again.";
					$addError = true;
					break;
				case UPLOAD_ERR_NO_FILE:	//no file was uploaded
					$statusMsg = "Upload failed. No file specified.";
					$addError = true;
					break;
			}
		} else {
			/*
			 * No upload, get the file size and the hash from the form.
			 * Ensure the file size is actually numeric.
			 */
			$hash = strtolower($_POST["hash"]);
			if (isset($_POST["filesize"])) {
				$filesize = $_POST["filesize"];
				if (!is_numeric($filesize)) {
					$filesize = 0;
				} else {
					$filesize = round($filesize / 1048576, 2);
				}
			} else
				$filesize = 0;
		}

		/*
		 * Check to see if it the category is a zero length string
		 * If it is, assume "main" as the category.
		 */
		if (isset($_POST["category"]))
			$category = $_POST["category"];

		if (strlen($category)==0) {
			$category = "main";
		} else {
			if (strpos($category, " ") !== false) {
				$statusMsg .= "Category names cannot contain spaces!";
				$addError = true;				
			}
		}

		/*
		 * Now the fun part, we need to take into account advanced sorting... which means adding to
		 * group 0 for the specified category and adding it at the end of the sort list. The code below
		 * asks the database for this information and then figures out what to do.
		 */
		if (isset($_POST["groupname"]) && is_numeric($_POST["groupname"])) {
			$group = $_POST["groupname"];
		} else {
			$group = 0;
		}
		
		$rstAdvSort = @mysql_query("SELECT MAX(`sorting`) FROM `namemap` WHERE `grouping` = $group and `category` = \"$category\"");
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
		 * If there were no processing errors, check the rest of the
		 * data on the form itself.
		 */
		if (!$addError) {
			/*
			 * Check all the text fields in the form. If something was set,
			 * copy it into a variable.
			 */
			if (isset($_POST["filename"]))
				$filename=$_POST["filename"];
			else
				$filename = "";
	
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

			if (isset($_POST["crcinfo"]))
				$crcinfo = $_POST["crcinfo"];
			else
				$crcinfo = "";	

			if (isset($_POST["removeurl"]))
				$removeurldate = $_POST["removeurl"];
			else
				$removeurldate = "";	

			if (isset($_POST["indexhidetorrent"]))
				$hidetorrentdate = $_POST["indexhidetorrent"];
			else
				$hidetorrentdate = "";	

			if (isset($_POST["shortdesc"]))
				$shortdesc = $_POST["shortdesc"];
			else
				$shortdesc = "";

			/*
			 * If a CRC value hasn't been specified, let's try to get it
			 * from the torrent itself....
			 i*/
			$matches = array();
			if (preg_match("/[[{(]([a-fA-F0-9]{8,8})[})\]]/i", $array['info']['name'], $matches) !== false) {
				if (array_key_exists(1, $matches)) {
					$crcinfo = strtoupper($matches[1]);
				}
			}					
			 
			/*
			 * Check to see if there is a forced category to use, if
			 * there is use it.
			 */
			if (isset($_SESSION["admin_perms"]["category"]))
				$category = $_SESSION["admin_perms"]["category"];

			/*
			 * If the option to fill in fields using data from the torrent file is checked,
			 * override the filename entered.
			 */	
			if (isset($_POST["autoset"]) && $processuploadedtorrent) {
				if (strcmp($_POST["autoset"], "enabled") == 0) {
					if (strlen($filename) == 0 && isset($array["info"]["name"])) {
						$filename = $array["info"]["name"];
					}
				}
			}

			/*
			 * If the filename is empty, use the hash as the filename
			 */
			if (strlen($filename) == 0) {
				$filename = $hash;
			}

			/*
			 * If something was entered in the URL box, it overrides any other URL
			 * (even the url when the files is copied, or when the option is checked
			 * to use the URL from the torrent's file name.)
			 */
			if (isset($_POST["url"]))
				if (strlen($_POST["url"]) > 0)
					$url = $_POST["url"];

			/*
			 * Check the dates to make sure they are legitimate dates.
			 * If not, display a warning and don't add it to the database.
			 */
			if (!isDate($removeurldate, true) || !isDate($hidetorrentdate, true)) {
				$statusMsg .= "ERROR: A date is invalid. Please use the format yyyy-mm-dd for dates (ie. '2004-03-24')";
			} else {
				/*
				 * Trying to stay HTML compliant is a pain in the ass sometimes.
				 */
				if (!get_magic_quotes_gpc()) {
					/*
					 * PHP isn't adding slashes to the the POST results, so convert the strings
					 * to HTML-compatible and run addslashes to be a compatible query
					 * string
					 */
					$filename = addslashes(htmlentities($filename));
					$url = addslashes(htmlentities($url));
					$urlmirror = addslashes(htmlentities($urlmirror));
					$urlsfv = addslashes(htmlentities($urlsfv));
					$urlmd5 = addslashes(htmlentities($urlmd5));
					$urlinfo = addslashes(htmlentities($urlinfo));
					$shortdesc = addslashes(htmlentities($shortdesc));
					$crcinfo = addslashes(htmlentities($crcinfo));
				} else {
					/*
					 * PHP is adding slashes to the POST results, so we have to remove the
					 * slashes, then make them HTML compatible, then add the slashes
					 * back to be a compatible query string
					 */
					$filename = addslashes(htmlentities(stripslashes($filename)));
					$url = addslashes(htmlentities(stripslashes($url)));
					$urlmirror = addslashes(htmlentities(stripslashes($urlmirror)));
					$urlsfv = addslashes(htmlentities(stripslashes($urlsfv)));
					$urlmd5 = addslashes(htmlentities(stripslashes($urlmd5)));
					$urlinfo = addslashes(htmlentities(stripslashes($urlinfo)));
					$shortdesc = addslashes(htmlentities(stripslashes($shortdesc)));
					$crcinfo = addslashes(htmlentities(stripslashes($crcinfo)));
				}
				/*
				 * Get today's date (for the torrent add date)
				 */
				$today = date("Y-m-d");

				/*
				 * Check who is adding it
				 */
				if ($_SESSION["admin_perms"]["root"]) {
					$addusername = "root";
				} else {
					$addusername = $_SESSION["admin_perms"]["user"];
				}
				/*
				 * Check the hash value to make sure it's valid. If it is, add to the database.
				 */
				if ((strlen($hash) != 40) || !verifyHash($hash)) {
					$statusMsg = "ERROR: Hash value must be exactly 40 hex bytes.";
				} else {
					/*
					 * Build the query string
					 */
					$query = "INSERT INTO namemap (info_hash, 
								filename, 
								url, 
								mirrorurl, 
								info, 
								size, 
								dateadded, 
								crc32, 
								category, 
								sfvlink, 
								md5link, 
								infolink,
								DateToRemoveURL,
								DateToHideTorrent,
								addedby,
								grouping,
								sorting,
								tsAdded,
								torrent_size)
								VALUES (\"$hash\", 
									\"$filename\", 
									\"$url\", 
									\"$urlmirror\", 
									\"$shortdesc\", 
									$filesize, 
									\"$today\", 
									\"$crcinfo\", 
									\"$category\", 
									\"$urlsfv\", 
									\"$urlmd5\", 
									\"$urlinfo\",
									\"$removeurldate\",
									\"$hidetorrentdate\",
									\"$addusername\",
									$group,
									$advsort, UNIX_TIMESTAMP(), $torrent_metadata_size)";

					/* 
					 * Create the torrent table, then update the namemap table.
					 */
					$status = makeTorrent($hash, true);
					quickQuery($query);

					/*
					 * Display the status to the user, change the hidden flag if necessary
					 */
					if ($status) {
						$statusMsg .= "Torrent was added successfully.";

						/*
						 * If the user requested the torrent to be hidden, hide it.
						 */
						if (isset($_POST["hidetorrent"]))
							if (strcmp($_POST["hidetorrent"], "enabled") == 0) {
								$query = "UPDATE summary SET hide_torrent=\"Y\" WHERE info_hash=\"$hash\"";
								quickQuery($query);
							}

						/*
						 * Check to see if external tracking needs to be done
						 */
						$addexternal = false;
						if ($GLOBALS["auto_add_external_torrents"] && $GLOBALS["allow_external_scanning"] && !$addmirrortorrent) {
							if (strcmp($GLOBALS["my_tracker_announce"], $array["announce"]) != 0) {
								$addexternal = true;
							}
						} else {
							if (isset($_POST["externaladd"]) && !$addmirrortorrent) {
								if (strcmp($_POST["externaladd"], "enabled") == 0) {
									if ($GLOBALS["allow_external_scanning"]) {
										if (strcmp($GLOBALS["my_tracker_announce"], $array["announce"]) != 0) {
											$addexternal = true;
										}
									}
								}
							}
						}

						/*
						 * If external tracking is to be done, set up the tables as needed...
						 */
						if ($addexternal && $processuploadedtorrent) {
							/*
							 * make sure the external tracker is /scrape enabled
	 						 */
							if (strcmp(substr($array["announce"], strrpos($array["announce"], "/"), 9), "/announce") == 0) {
								/*
								 * It is, add/modify tables as needed...
								 */
								$query = "UPDATE summary SET external_torrent=\"Y\" WHERE info_hash=\"$hash\"";
								quickQuery($query);

								/*
								 * Now, we have to replace the '/announce' with '/scrape' in order to get the scrape output
								 * in ext_scan.php later on....
								 */
								$scrapeurl = substr($array["announce"], 0, strlen($array["announce"]) - 9) . "/scrape";

								$now = time();
								$query = "INSERT INTO trk_ext (info_hash, scrape_url, last_update) VALUES (\"$hash\", \"$scrapeurl\", $now)";
								quickQuery($query);
							} else {
								/*
								 * Torrent is still external, so set it in the database, also display a message to the user
								 * stating the stats can't be updated for this torrent.
								 */
								$statusMsg .= "<BR>This torrent is external and does not support /scrape. Stats will not be updated.";
								$query = "UPDATE summary SET external_torrent=\"Y\" WHERE info_hash=\"$hash\"";
								quickQuery($query);
								$query = "UPDATE summary SET ext_no_scrape_update=\"Y\" WHERE info_hash=\"$hash\"";
								quickQuery($query);
							}
						}
					}
					else {
						$statusMsg .= "There were some errors. Check if this torrent had been added previously.";
					}
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
	echo "<TITLE>". $adm_page_title . " - Add torrents</TITLE>\r\n";
?>
</HEAD>

<BODY>
<FORM ENCTYPE="multipart/form-data" METHOD="POST" ACTION="bta_add.php">
<TABLE CLASS="tblAdminOuter">
<TR>
<?php
	/*
	 * Display the page heading.
	 */
	echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>Add torrents</TD>\r\n";
?>
</TR>
<?php admShowURL_Login($ip); ?>
<TR>
	<TD CLASS="data" COLSPAN=15 ALIGN="center"><BR>
	   <A HREF="help/help_add_torrent.php" TARGET="_blank">Need help?</A><BR>
	</TD>
</TR>
<TR>
	<TD CLASS="data" COLSPAN=15 ALIGN="center"><BR>
	   <I>The announce URL for this tracker is</I>: <B><?php echo $GLOBALS["my_tracker_announce"]; ?><BR>
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
	if (function_exists("sha1")) {
		echo "\t<TR>\r\n\t\t<TD><INPUT TYPE=\"hidden\" NAME=\"MAX_FILE_SIZE\" VALUE=\"". $GLOBALS["max_torrent_size"] . "\">\r\n";
		echo "\t\tTorrent file:</TD>\r\n\t\t<TD><INPUT TYPE=\"file\" NAME=\"torrentfile\" SIZE=40>&nbsp;Click the <I>Browse</I> button to select a torrent.</FONT></TD>\r\n\t</TR>\r\n";
	} 

	if ($addError) {
		if (isset($_POST["hash"])) {
			echo "\t<TR>\r\n\t\t<TD>Info Hash:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"hash\" SIZE=40 MAXLENGTH=40 VALUE=\"${_POST["hash"]}\">&nbsp;&nbsp;File uploading is not available, enter the hash here.</TD>\r\n\t</TR>\r\n";
		} else {
			echo "\t<TR>\r\n\t\t<TD>Info Hash:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"hash\" SIZE=40 MAXLENGTH=40>&nbsp;&nbsp;Enter the hash manually here if repeated attempts to add torrent do not work.</TD>\r\n\t</TR>\r\n";
		}

		if (isset($_POST["filesize"])) {
			echo "\t<TR>\t\t<TD>File Size:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"filesize\" SIZE=40 MAXLENGTH=40 VALUE=\"${_POST["filesize"]}\">&nbsp;&nbsp;Enter the exact size of the torrent, in bytes.</TD>\r\n\t</TR>\r\n";
		} else {
			echo "\t<TR>\t\t<TD>File Size:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"filesize\" SIZE=40 MAXLENGTH=40>&nbsp;&nbsp;Enter the exact size of the torrent, in bytes.</TD>\r\n\t</TR>\r\n";
		}
	}
?>
	<TR>
		<TD>File name:</TD>
		<TD><INPUT TYPE=text NAME="filename" SIZE=40 MAXLENGTH=200 <?php if ($addError) { echo "VALUE=\"${_POST["filename"]}\""; } ?>>&nbsp;&nbsp;<FONT SIZE=-1><B>(Optional)</B></FONT> If you want to use a different name, enter it here.</TD>
	</TR>
	<TR>
		<TD>URL to torrent:</TD>
		<TD><INPUT TYPE=text NAME="url" SIZE=40 MAXLENGTH=200 <?php if ($addError) { echo "VALUE=\"${_POST["url"]}\""; } ?>>&nbsp;&nbsp;<FONT SIZE=-1><B>(Optional)</B></FONT> Enter the URL to the torrent.</TD>
	</TR>
	<TR>
		<TD>Mirror for torrent:</TD>
		<TD><INPUT TYPE=text NAME="urlmirror" SIZE=40 MAXLENGTH=200 <?php if ($addError) { echo "VALUE=\"${_POST["urlmirror"]}\""; } ?>>&nbsp;&nbsp;<FONT SIZE=-1><B>(Optional)</B></FONT> Enter the URL to the mirror site.</TD>
	</TR>
	<TR>
		<TD>URL to SFV file:</TD>
		<TD><INPUT TYPE=text NAME="sfv" SIZE=40 MAXLENGTH=200 <?php if ($addError) { echo "VALUE=\"${_POST["sfv"]}\""; } ?>>&nbsp;&nbsp;<FONT SIZE=-1><B>(Optional)</B></FONT> Enter the URL to the SFV file.</TD>
	</TR>
	<TR>
		<TD>URL to MD5 file:</TD>
		<TD><INPUT TYPE=text NAME="md5" SIZE=40 MAXLENGTH=200 <?php if ($addError) { echo "VALUE=\"${_POST["md5"]}\""; } ?>>&nbsp;&nbsp;<FONT SIZE=-1><B>(Optional)</B></FONT> Enter the URL to the MD5 file.</TD>
	</TR>
	<TR>
		<TD>Info URL:</TD>
		<TD><INPUT TYPE=text NAME="urlinfo" SIZE=40 MAXLENGTH=200 <?php if ($addError) { echo "VALUE=\"${_POST["urlinfo"]}\""; } ?>>&nbsp;&nbsp;<FONT SIZE=-1><B>(Optional)</B></FONT> Enter the URL to an information link.</TD>
	</TR>
	<TR>
		<TD>Description:</TD>
		<TD><INPUT TYPE=text NAME="shortdesc" SIZE=40 MAXLENGTH=200 <?php if ($addError) { echo "VALUE=\"${_POST["shortdesc"]}\""; } ?>>&nbsp;&nbsp;<FONT SIZE=-1><B>(Optional)</B></FONT> Enter a short description.</TD>
	</TR>
<?php
	if ($_SESSION["admin_perms"]["root"])
		if ($addError) {
			echo "\t<TR>\r\n\t\t<TD>Category:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"category\" SIZE=11 MAXLENGTH=10 VALUE=\"${_POST["category"]}\">&nbsp;&nbsp;<FONT SIZE=-1><B>(Optional)</B></FONT> Enter a category.</TD>\r\n\t</TR>";
		} else {
			echo "\t<TR>\r\n\t\t<TD>Category:</TD>\r\n\t\t<TD><INPUT TYPE=text NAME=\"category\" SIZE=11 MAXLENGTH=10>&nbsp;&nbsp;<FONT SIZE=-1><B>(Optional)</B></FONT> Enter a category.</TD>\r\n\t</TR>";
		}
	else
		echo "\t<INPUT TYPE=\"hidden\" NAME=\"category\" VALUE=\"". $_SESSION["admin_perms"]["category"]."\">\r\n";
?>
	<TR>
		<TD>CRC32 checksum:</TD>
		<TD><INPUT TYPE=text NAME="crcinfo" SIZE=40 MAXLENGTH=254 <?php if ($addError) { echo "VALUE=\"${_POST["crcinfo"]}\""; } ?>>&nbsp;&nbsp;<FONT SIZE=-1><B>(Optional)</B></FONT> Enter CRC32 checksum information.</TD>
	</TR>
	<TR>
		<TD>Remove URL:</TD>
		<TD><INPUT TYPE=text NAME="removeurl" SIZE=15 MAXLENGTH=254 <?php if ($addError) { echo "VALUE=\"${_POST["removeurl"]}\""; } ?>>&nbsp;&nbsp;<FONT SIZE=-1><B>(Optional)</B></FONT> Enter a date (format: yyyy-mm-dd) to remove the URL from the index page.</TD>
	</TR>
	<TR>
		<TD>Hide from index:</TD>
		<TD><INPUT TYPE=text NAME="indexhidetorrent" SIZE=15 MAXLENGTH=254 <?php if ($addError) { echo "VALUE=\"${_POST["indexhidetorrent"]}\""; } ?>>&nbsp;&nbsp;<FONT SIZE=-1><B>(Optional)</B></FONT> Enter a date (format: yyyy-mm-dd) to hide the URL from the index page. <B>Note: Torrent stays active.</B></TD>
	</TR>
<?php
	/*
	 * Special case: adding immediately to a group.
	 */
	if (isset($_SESSION["admin_perms"]["advsort"]) && $_SESSION["admin_perms"]["advsort"]) {
		/*
		 * Connect to the database
		 */
		if ($GLOBALS["persist"])
			$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		else
			$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
		mysql_select_db($database) or die("Can't open the database.");
	
		/*
		 * Grab a list of groups and display them to the user...
		 */
		$rstGrpList = @mysql_query("SELECT `group_id`, `heading` FROM `subgrouping` WHERE `category` = \"".$_SESSION["admin_perms"]["category"]."\" ORDER BY `heading`");
		
		if ($rstGrpList === false) {
			$grpCombo = false;
		} else {
			if (mysql_num_rows($rstGrpList) == 0) {
				$grpCombo = false;
			} else {
				$grpCombo = "\t\t<SELECT NAME=\"groupname\"><OPTION VALUE=\"0\">Don't group</OPTION>";
				while ($rowGrp = mysql_fetch_row($rstGrpList)) {
					$grpCombo .= "<OPTION VALUE=\"$rowGrp[0]\">$rowGrp[1]</OPTION>";
				}
				$grpCombo .= "</SELECT>\r\n";
			}
		}
		
		/*
		 * Now, only if there is groups added, show the box
		 */
		if ($grpCombo !== false) {
			echo "\t<TR>\r\n\t\t<TD>Add to group:</TD>\r\n\t\t<TD>$grpCombo&nbsp;&nbsp;<FONT SIZE=-1><B>(Optional)</B></FONT> Choose the torrent group to insert the torrent into.</TD>\r\n\t</TR>\r\n";
		} else {
			echo "\t<TR>\r\n\t\t<TD>Add to group:</TD>\r\n\t\t<TD>No torrent groups available, add some first!</TD>\r\n\t</TR>\r\n";
		}
	}
	
	/*
	 * Hide torrent checkbox
	 */
	if ($addError) {
		$cbStatus = ( strcmp($_POST["hidetorrent"], "enabled") == 0 ) ? " CHECKED" : "";
	} else {
		$cbStatus = "";
	}
	echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=\"checkbox\" NAME=\"hidetorrent\" VALUE=\"enabled\" $cbStatus> Hide this torrent</TD>\r\n\t</TR>\r\n";
	 
	if ($_SESSION["admin_perms"]["root"] || $_SESSION["admin_perms"]["addmirror"]) {
		if ($addError) {
			$cbStatus = ( strcmp($_POST["mirrortorrent"], "enabled") == 0 ) ? " CHECKED" : "";
		} else {
			$cbStatus = "";
		}

		echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=\"checkbox\" NAME=\"mirrortorrent\" VALUE=\"enabled\" $cbStatus> This is a backup torrent from another tracker</TD>\r\n\t</TR>\r\n";
	}

	if ($GLOBALS["allow_torrent_move"] && function_exists("sha1")) {
		if ($addError) {
			$cbStatus = ( strcmp($_POST["copytorrent"], "enabled") == 0 ) ? " CHECKED" : "";
		} else {
			$cbStatus = "CHECKED";
		}

		echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=\"checkbox\" NAME=\"copytorrent\" VALUE=\"enabled\" $cbStatus> Copy torrent to webserver?&nbsp;<FONT SIZE=-1>Note: URL will be added automatically if selected.</FONT></TD>\r\n\t</TR>\r\n";

		if ($addError) {
			$cbStatus = ( strcmp($_POST["copytorrentonly"], "enabled") == 0 ) ? " CHECKED" : "";
		} else {
			$cbStatus = "";
		}

		echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=\"checkbox\" NAME=\"copytorrentonly\" VALUE=\"enabled\" $cbStatus> Copy torrent to webserver <U>only</U></TD>\r\n\t</TR>\r\n";
	}

	if ($GLOBALS["allow_external_scanning"] && !$GLOBALS["auto_add_external_torrents"] && function_exists("sha1")) {
		if ($addError) {
			$cbStatus = ( strcmp($_POST["externaladd"], "enabled") == 0 ) ? " CHECKED" : "";
		} else {
			$cbStatus = "CHECKED";
		}

		echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=\"checkbox\" NAME=\"externaladd\" VALUE=\"enabled\" $cbStatus> Add as an external reference (if applicable?)?&nbsp;<FONT SIZE=-1>Note: External tracker's announce URL must end with '/announce'.</FONT></TD>\r\n\t</TR>\r\n";
	}

	if (function_exists("sha1")) {
		if ($addError) {
			$cbStatus = ( strcmp($_POST["autoset"], "enabled") == 0 ) ? " CHECKED" : "";
		} else {
			$cbStatus = "CHECKED";
		}

		echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=\"checkbox\" NAME=\"autoset\" VALUE=\"enabled\" $cbStatus> Fill in fields using data from the torrent file</TD>\r\n\t</TR>\r\n"; 
	}

	if ($addError) {
		echo "\t<TR>\r\n\t\t<TD ALIGN=CENTER COLSPAN=2><FONT COLOR=\"RED\">Retry uploading torrent file:</FONT> <INPUT TYPE=\"radio\" NAME=\"retry_torrent\" VALUE=\"10\" CHECKED>Yes&nbsp;&nbsp;&nbsp;<INPUT TYPE=\"radio\" NAME=\"retry_torrent\" VALUE=\"11\"> No </TD>\r\n\t</TR>\r\n"; 
	}
?>
	<TR>
		<TD ALIGN=CENTER COLSPAN=2><INPUT TYPE=submit NAME="addtorrent" VALUE="Add Torrent" CLASS="button">&nbsp;&nbsp;<INPUT TYPE=reset VALUE="Clear settings" CLASS="button"></TD>
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
