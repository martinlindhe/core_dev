<?php
	/*
	 * Module:	gettorrent.php
	 * Description: This file retrieves a torrent from the database.
	 *
	 * Author:	danomac
	 * Written:	26-April-2005
	 *
	 * Copyright (C) 2005 danomac
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
	require_once ("config.php");

	/*
	 * Make sure required parameter was passed
	 */
	$getError = false;
	if (!isset($_GET["info_hash"])) {
		header("HTTP/1.0 400 Bad Request", 400);
		$errMsg = "A bad request was sent to the webserver.";
		$getError = true;
	} else {
		/*
		 * Check to make sure the hash string is 40 characters long and DOES NOT have spaces
		 */
		if (strlen($_GET["info_hash"]) != 40 && strpos($_GET["info_hash"], " ") !== false) {
			header("HTTP/1.0 404 Not Found");
			$errMsg = "File requested was not found.";
		} else {
			/*
			 * All is good, let's try to find the torrent, start by
			 * connecting to the database
			 */
			if ($GLOBALS["persist"]) {
				$db = @mysql_pconnect($dbhost, $dbuser, $dbpass);
				if (!$db) {
					header("HTTP/1.0 404 Not Found");
					$errMsg = "An error occured and the requested file was not found.";
					$getError = true;
				}
			} else {
				$db = @mysql_connect($dbhost, $dbuser, $dbpass);
				if (!$db) {
					header("HTTP/1.0 404 Not Found");
					$errMsg = "An error occured and the requested file was not found.";
					$getError = true;
				}
			}

			/*
			 * Open the database needed
			 */
			if (!$getError) {
				$dbselresult = @mysql_select_db($database);
				if (!$dbselresult) {
						header("HTTP/1.0 404 Not Found");
						$errMsg = "An error occured and the requested file was not found.";
						$getError = true;
				} else {
					/*
					 * Okay, now look for the torrent...
					 */
					$recordset = @mysql_query("SELECT name, metadata FROM torrents WHERE info_hash=\"${_GET["info_hash"]}\"");
					if (!$recordset) {
						header("HTTP/1.0 404 Not Found");
						$errMsg = "The requested file was not found.";
						$getError = true;
					} else {
						/*
						 * Torrent was found, so let's decode it and output to user
						 */
						$row = mysql_fetch_row($recordset);
						if (!$row) {
							header("HTTP/1.0 404 Not Found");
							$errMsg = "An error occured and the requested file was not found.";
							$getError = true;
						} else {
							header("Content-type: application/x-bittorrent");
						   header("Pragma: public");
						   header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
						   header("Content-Disposition: attachment; filename=\"${row[0]}\"");
						   print( base64_decode($row[1]) );
							exit;
						}
					}
				}
			}

		}

	}

	echo "<HTML><BODY><CENTER>$errMsg</CENTER></BODY></HTML>";
?>
