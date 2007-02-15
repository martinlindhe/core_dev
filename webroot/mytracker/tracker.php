<?php 
	header("Content-type: text/plain");
	header("Pragma: no-cache"); 
	/*
	 * danomac's changelog 08-May-2005:
	 *   -brought core to phpbttracker 1.5e.
	 *
	 * danomac's changelog 28-May-2004:
	 *   -scrape interface: reports completed downloads, hides hidden torrents from reporting
	 *   -start function: now accepts the "uploaded" parameter for tracking peer uploads
	 *   -events section: now tracks peer uploads
	 *   -events section: records the client version on connect
	 *   -ip banning has been added
	 *   -scrape interface can now report an error when disabled
	 *   -speed refresh intervals and average refresh intervals controlled by global parameters
	 *   -filters clients on connect
	 *   -sends out a minimum scrape request interval on scrape requests
	 *   -automatically runs a consistency check when scrape report <0 results
	 *
	 * Copyright (C) 2004 by DeHacked
	 * Portions Copyright (C) 2004 by danomac
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
	ignore_user_abort(1);

	$GLOBALS["peer_id"] = "";
	$summaryupdate = array();

	require_once("config.php");
	require_once("funcsv2.php");

	/*
	 * Get the client version string, if it is reported
	 */
	if (isset($_SERVER["HTTP_USER_AGENT"]))
		$clientVer = $_SERVER["HTTP_USER_AGENT"];
	else
		$clientVer = "Not reported";

	/*
	 * Connect to the database server
	 */
	if ($GLOBALS["persist"])
		$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or showError("Tracker error: can't connect to database. Contact the webmaster.");
	else
		$db = @mysql_connect($dbhost, $dbuser, $dbpass) or showError("Tracker error: can't connect to database. Contact the webmaster.");

	/*
	 * Open required database
	 */
	@mysql_select_db($database) or showError("Tracker error: can't open database. Contact the webmaster");

	/* 
	 * A version of the BitTorrent tracker written in PHP and using
	 * MySQL as a manager. These paragraphs outline my design decisions.
	 * 
	 * BTTrack uses a whole database which can be shared. Each torrent uses its
	 * own table while a single "summary" table shows overall information
	 * on each individual torrent at a glance. Putting all the torrent
	 * data in one table is easy enought to do (a primary key), but
	 * that would have some speed implications on a server with a lot of
	 * torrents.
	 * 
	 * Before you begin, you must have a MySQL server configured with
	 * an appropriate user for BitTorrent, a database with permissions, etc.
	 * Create a summary table using the command below, and run 
	 * maketorrents.php for each torrent you want the server to handle.
	 * You can use the install.php script to prepare the database if you
	 * have appropriate database permission.
	 * 
	 */

	/*
	 * First check to see if a scrape request is present
	 */
	if (isset ($_SERVER["PATH_INFO"])) {
		/*
		 * Scrape interface requested
		 */
		if (substr($_SERVER["PATH_INFO"],-7) == '/scrape') {
			/*
			 * Is the scrape interface even enabled?
			 */
			if ($GLOBALS["allow_scrape"]) {
				$usehash = false;

				/*
				 * Was an individual hash requested?
				 */
				if (isset($_GET["info_hash"]))
				{
					if (get_magic_quotes_gpc()) {
						$info_hash = stripslashes($_GET["info_hash"]);
					} else {
						$info_hash = $_GET["info_hash"];
					}

					if (strlen($info_hash) == 20) {
						$info_hash = bin2hex($info_hash);
					} else if (strlen($info_hash) == 40) {
						verifyHash($info_hash) or showError("Invalid info hash value.");
					} else {
						showError("Invalid info hash value.");
					}

					//make sure torrent isn't hidden
					scrapeVerifyHash($info_hash) or showError("Invalid info hash value.");

					$usehash = true;
				}

				/*
				 * Get requested info
				 */
				if ($usehash)
  		             $query = mysql_query("SELECT summary.info_hash, summary.seeds, summary.finished, summary.leechers, namemap.filename, summary.dlbytes, summary.avgdone, summary.speed FROM summary LEFT JOIN namemap ON summary.info_hash = namemap.info_hash WHERE summary.info_hash=\"$info_hash\" AND summary.hide_torrent=\"N\" AND summary.external_torrent=\"N\"") or showError("Database error. Cannot complete request.");
				else
  		             $query = mysql_query("SELECT summary.info_hash, summary.seeds, summary.finished, summary.leechers, namemap.filename, summary.dlbytes, summary.avgdone, summary.speed FROM summary LEFT JOIN namemap ON summary.info_hash = namemap.info_hash WHERE summary.hide_torrent=\"N\" AND summary.external_torrent=\"N\" ORDER BY summary.info_hash") or showError("Database error. Cannot complete request.");

				/*
				 * Begin bencoded scrape output
				 */
				echo "d5:filesd";

				/*
				 * If invalid /scrape data is detected, this is set to false
				 */
				$scrapeConsistent = true;

				/*
				 * Go through the records selected
				 */
				while ($row = mysql_fetch_row($query))
				{
					/*
					 * If either seeder/leecher count is below 0, reset to zeros, rather than
					 * reporting other data
					 */
					if ($row[1] < 0 || $row[3] < 0) {
						$scrapeConsistent = false;
						$row[1] = 0;
						$row[3] = 0;
					}

					/*
					 * Spit out the basic scrape information
					 */
					$hash = hex2bin($row[0]);
					echo "20:".$hash."d";
					echo "8:completei".$row[1]."e";
      		   echo "10:downloadedi".$row[2]."e";
		         echo "10:incompletei".$row[3]."e";

					/*
					 * If extra info is allowed, show it
					 */
					if ($GLOBALS["scrape_extras"]) {
						if (isset($row[4]))
							echo "4:name".strlen($row[4]).":".$row[4];

						$transferred = round($row[5] / 1073741824, 3);
						echo "11:transferred".strlen($transferred).":".$transferred; // always GiB - default 
						echo "15:transferredunit3:GiB"; // GiB
						echo "11:averagedone".strlen($row[6]).":".$row[6];
						$speed = round($row[7] / 1024, 3);
						echo "5:speed".strlen($speed).":".$speed; // always KiB - default speed
						echo "9:speedunit3:KiB"; //KiB
					}

					echo "e";
				}
	
				/*
				 * Request the client not request another interval before a set value
				 */
				echo "e5:flagsd20:min_request_intervali" . $GLOBALS["scrape_min_interval"] . "eee";

				/*
				 * If bad data was found, and auto-consistency checking is on,
				 * run the consistency check
				 */
				if (!$scrapeConsistent && $GLOBALS["auto_db_check_scrape"]) {
					consistencyCheck();
				}
				exit;
			} else {
				/*
				 * Send error message to client
				 */
				showError("The scrape interface is disabled.");
			}
		}
	}

	///////////////////////////////////////////////////////////////////
	// Handling of parameters from the URL and other setup

	// Error: no web browsers allowed
	if (!isset($_GET["info_hash"]) || !isset($_GET["peer_id"])) {
		header("HTTP/1.0 400 Bad Request");
		die("This file is for BitTorrent clients.\n");
	}
		
	// Many thanks to KktoMx for figuring out this head-ache causer, 
	// and to bideomex for showing me how to do it PROPERLY... :)
	if (get_magic_quotes_gpc()) {
		$info_hash = bin2hex(stripslashes($_GET["info_hash"]));
		$peer_id = bin2hex(stripslashes($_GET["peer_id"]));
	} else {
		$info_hash = bin2hex($_GET["info_hash"]);
		$peer_id = bin2hex($_GET["peer_id"]);
	}

	if (!isset($_GET["port"]) || !isset($_GET["downloaded"]) || !isset($_GET["uploaded"]) || !isset($_GET["left"]))
		showError("Invalid information received from BitTorrent client");

	$port = $_GET["port"];
	$ip = mysql_real_escape_string(str_replace("::ffff:", "", $_SERVER["REMOTE_ADDR"]));
	$iplong = ip2long(str_replace("::ffff:", "", $_SERVER["REMOTE_ADDR"]));
	$downloaded = $_GET["downloaded"];
	$uploaded = $_GET["uploaded"];
	$left = $_GET["left"];

	/*
	 * Need to validate IP before looking at bans...
	 */
	if (isset($_GET["ip"]) && $GLOBALS["ip_override"]) {
		/*
		 * compact check: valid IP address
		 */
		if (ip2long($_GET["ip"]) == -1)
			showError("Invalid IP address. Must be standard dotted decimal (hostnames not allowed)");
		$ip = mysql_real_escape_string($_GET["ip"]);
		$iplong = ip2long($_GET["ip"]);
	} else {
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
	      foreach(explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $address) {
				$addr = ip2long(trim($address));
				if ($addr != -1) {
					if ($addr >= -1062731776 && $addr <= -1062666241) {
						// 192.168.x.x
					} elseif ($addr >= -1442971648 && $addr <= -1442906113) {
						// 169.254.x.x
					} elseif ($addr >= 167772160 && $addr <= 184549375) {
						// 10.x.x.x
					} elseif ($addr >= 2130706432 && $addr <= 2147483647) {
						// 127.0.0.1
					} elseif ($addr >= -1408237568 && $addr <= -1407188993) {
						// 172.[16-31].x.x
					} else {
						// Finally, we can accept it as a "real" ip address.
						$ip = mysql_real_escape_string(trim($address));
						$iplong = ip2long(trim($address));
						break;
					}
				}
			}
		}
	}	

	/*
	 * If ip banning is enabled, check to see if client is allowed
	 */
	if ($GLOBALS["enable_ip_banning"]) {
		/*
		 * Ask the database if this IP is banned
		 */
		$query = "SELECT ip, 
				iplong,
				bandate, 
				reason, 
				autoban,
				banexpiry,
				banautoexpires FROM ipbans 
				WHERE iplong = $iplong AND (CURDATE() < banexpiry OR banautoexpires = 'N')";


		$recordset = mysql_query($query);
		
		if (mysql_num_rows($recordset) == 1) {
			/*
			 * Oh hey, a ban!
			 */
			$row = mysql_fetch_row($recordset);

			if ($row[4] == 'Y') {
				/*
				 * Autoban, check to see if it has an expiry date
				 */
				if ($row[6] == 'Y') {
					$message = "Autoban on: ". $row[2] . "/Expires " . $row[5] .". Reason: " . $row[3] ;
				} else {
					$message = "Autoban on: ". $row[2] . " Reason: " . $row[3] ;
				}
			} else {
				/*
				 * Normal ban, check to see if it has an expiry date
				 */
				if ($row[6] == 'Y') {
					$message = "Banned on: ". $row[2] . "/Expires " . $row[5] .". Reason: " . $row[3] ;
				} else {
					$message = "Banned on: ". $row[2] . " Reason: " . $row[3] ;
				}
			}	

			showError($message);
		}
	}	

	/*
	 * If set, run a check on the client before doing any real work
	 */
	if ($GLOBALS["filter_clients"]) {
			require_once("tracker_client.php");
			filterClient($clientVer, $ip, $iplong, $peer_id);
	}

	if (isset($_GET["event"]))
		$event = $_GET["event"];
	else
		$event = "";

	if (!isset($GLOBALS["ip_override"]))
		$GLOBALS["ip_override"] = true;

	if (isset($_GET["numwant"]))
		if ($_GET["numwant"] < $GLOBALS["maxpeers"] && $_GET["numwant"] >= 0)
			$GLOBALS["maxpeers"]=$_GET["numwant"];

	if (isset($_GET["trackerid"])) {	
		if (is_numeric($_GET["trackerid"]))
			$GLOBALS["trackerid"] = mysql_real_escape_string($_GET["trackerid"]);
	}

	if (!is_numeric($port) || !is_numeric($downloaded) || !is_numeric($uploaded) || !is_numeric($left))
		showError("Invalid numerical field(s) from client");


	/////////////////////////////////////////////////////
	// Checks
	
	// Upgrade holdover: check for unset directives
	if (!isset($GLOBALS["countbytes"]))
		$GLOBALS["countbytes"] = true;
	if (!isset($GLOBALS["doavg"]))
		$GLOBALS["doavg"] = true;
	if (!isset($GLOBALS["peercaching"]))
		$GLOBALS["peercaching"] = false;
	if (!isset($GLOBALS["heavyload"]))
		$GLOBALS["heavyload"] = false;
	if (!isset($GLOBALS["compactonly"]))
		$GLOBALS["compactonly"] = false;

	/*
	 * Allow only clients that support the compact protocol to connect?
	 * Do note this requires peer caching to be enforced...
	 */
	if ($GLOBALS["peercaching"] && $GLOBALS["compactonly"]) {
		if (!isset($_GET["compact"])) {
			showError("This tracker requires clients that support the compact protocol.");
		}

		if ($_GET["compact"] != '1') {
			showError("This tracker requires clients that support the compact protocol.");
		}
	}

	/////////////////////////////////////////////////////
	// Any section of code might need to make a new peer, so this is a function here.
	// I don't want to put it into funcsv2, even though it should, just for consistency's sake.

	function start($info_hash, $ip, $port, $peer_id, $left, $uploaded, $clientVer) {
		if ($left == 0)
			$status = "seeder";
		else
			$status = "leecher";

		if (@isFireWalled($info_hash, $peer_id, $ip, $port))
			$nat = "'Y'";
		else
			$nat = "'N'";
	
		$results = @mysql_query("INSERT INTO x$info_hash SET peer_id=\"$peer_id\", port=\"$port\", ip=\"$ip\", lastupdate=UNIX_TIMESTAMP(), bytes=\"$left\", status=\"$status\", natuser=$nat, uploaded=$uploaded, clientversion=\"$clientVer\"");

		// Special case: duplicated peer_id. 
		if (!$results) {
			$error = mysql_error();
			if (stristr($error, "key")) {
				// Duplicate peer_id! Check IP address
				$peer = getPeerInfo($peer_id, $info_hash);

				if ($ip == $peer["ip"]) {
					// Same IP address. Tolerate this error.
					updatePeer($peer_id, $info_hash);
					return "WHERE natuser='N'";
				}

				//showError("Duplicated peer_id or changed IP address. Please restart BitTorrent.");
				// Different IP address. Assume they were disconnected, and alter the IP address.
				quickQuery("UPDATE x$info_hash SET ip=\"$ip\", uploaded=$uploaded, clientversion=\"$clientVer\" WHERE peer_id=\"$peer_id\"");
				return "WHERE natuser='N'";
			}
			error_log("PHPBTTracker: start: ".$error);
			showError("Tracker/database error. The details are in the error log.");
		}

		$GLOBALS["trackerid"] = mysql_insert_id();

		if ($GLOBALS["peercaching"]) {
			$compact = mysql_real_escape_string(pack('Nn', ip2long($ip), $port));
			$peerid = mysql_real_escape_string('2:ip' . strlen($ip) . ':' . $ip . '7:peer id20:' . hex2bin($peer_id) . "4:porti{$port}e");
			$no_peerid = mysql_real_escape_string('2:ip' . strlen($ip) . ':' . $ip . "4:porti{$port}e");
			mysql_query("INSERT INTO y$info_hash SET sequence=\"{$GLOBALS["trackerid"]}\", compact=\"$compact\", with_peerid=\"$peerid\", without_peerid=\"$no_peerid\"");
			// Let's just assume success... :/
		}

		if ($left == 0) {
			summaryAdd("seeds", 1);
			return "WHERE status=\"leecher\" AND natuser='N'";
		} else {
			summaryAdd("leechers", 1);
			return "WHERE natuser='N'";
		}
	} /// End of function start

	////////////////////////////////////////////////////////////////////////////////////////
	// Actual work. Depends on value of $event. (Missing event is mapped to '' above)
	
	if ($event == '') {
		verifyTorrent($info_hash) or evilReject($ip, $peer_id,$port);
		$peer_exists = getPeerInfo($peer_id, $info_hash);
		$where = "WHERE natuser='N'";

		if (!is_array($peer_exists))
			$where = start($info_hash, $ip, $port, $peer_id, $left, $uploaded, $clientVer);

		if ($peer_exists["bytes"] != 0 && $left == 0) {
			quickQuery("UPDATE x$info_hash SET bytes=0, status=\"seeder\", uploaded=$uploaded WHERE sequence=\"${GLOBALS["trackerid"]}");
			if (mysql_affected_rows() == 1) {
				summaryAdd("leechers", -1);
				summaryAdd("seeds", 1);
				summaryAdd("finished", 1);
			}
		}

		updatePeer($peer_id, $info_hash);
		collectBytes($peer_exists, $info_hash, $left, $uploaded);

		if ($GLOBALS["peercaching"])
			sendRandomPeers($info_hash);
		else {
			$peers = getRandomPeers($info_hash, "");
			sendPeerList($peers);
		}
	} elseif ($event == "started") {
		verifyTorrent($info_hash) or evilReject($ip, $peer_id,$port);

		$start = start($info_hash, $ip, $port, $peer_id, $left, $uploaded, $clientVer);
	
	
		// Don't send the tracker id for newly started clients. Send it next time. Make sure
		// they get a good random list of peers to begin with.
		if ($GLOBALS["peercaching"])
			sendRandomPeers($info_hash);
		else {
			$peers = getRandomPeers($info_hash, "");
			sendPeerList($peers);
		}
	} elseif ($event == "stopped") {
		verifyTorrent($info_hash) or evilReject($ip, $peer_id,$port);
		killPeer($peer_id, $info_hash, $left);	

		// I don't know why, but the real tracker returns peers on event=stopped
		// but I'll just send an empty list. On the other hand, 
		// TheSHADOW asked for this.
		if (isset($_GET["tracker"]))
			$peers = getRandomPeers($info_hash);
		else
			$peers = array("size" => 0);

		sendPeerList($peers);
	} elseif ($event == "completed") {
		// now the same as an empty string
		verifyTorrent($info_hash) or evilReject($ip, $peer_id,$port);
		$peer_exists = getPeerInfo($peer_id, $info_hash);

		if (!is_array($peer_exists))
			start($info_hash, $ip, $port, $peer_id, $left, $uploaded, $clientVer);
		else {
			quickQuery("UPDATE x$info_hash SET bytes=0, status=\"seeder\", uploaded=$uploaded WHERE sequence=\"${GLOBALS["trackerid"]}\"");

			// Race check
			if (mysql_affected_rows() == 1) {
				summaryAdd("leechers", -1);
				summaryAdd("seeds", 1);
				summaryAdd("finished", 1);
			}
		}
		updatePeer($peer_id, $info_hash);
		collectBytes($peer_exists, $info_hash, $left, $uploaded);
		$peers=getRandomPeers($info_hash);

		sendPeerList($peers);

	} else
		showError("Invalid event= from client.");


	if ($GLOBALS["countbytes"] && !$GLOBALS["heavyload"]) {
		// Once every minute or so, we run the speed update checker.
		$query = @mysql_query("SELECT UNIX_TIMESTAMP() - lastSpeedCycle FROM summary WHERE info_hash=\"$info_hash\"");
		$results = mysql_fetch_row($query);
		if ($results[0] >= $GLOBALS["spdrefresh"]) {
			if (Lock("SPEED:$info_hash")) {
				@runSpeed($info_hash, $results[0]);
				Unlock("SPEED:$info_hash");
			}
		}
	}

	if ($GLOBALS["doavg"] && !$GLOBALS["heavyload"]) {
		// Once every minute or so, we run the speed update checker.
		$query = @mysql_query("SELECT UNIX_TIMESTAMP() - lastAvgCycle FROM summary WHERE info_hash=\"$info_hash\"");
		$results = mysql_fetch_row($query);
		if ($results[0] >= $GLOBALS["avgrefresh"]) {
			if (Lock("AVG:$info_hash")) {
				@quickQuery("UPDATE summary SET lastAvgCycle = UNIX_TIMESTAMP() WHERE info_hash=\"$info_hash\"");
				@runAvg($info_hash);
				Unlock("AVG:$info_hash");
			}
		}
	}

	/* 
	 * Under heavy loads, this will lighten the load slightly... very slightly...
	 */
	if ($GLOBALS["heavyload"]) {
		if (mt_rand(1,10) == 4) {
			trashCollector($info_hash, $report_interval);
		}
	} else {
		trashCollector($info_hash, $report_interval);
	}

	/*
	 * Finally, it's time to do stuff to the summary table.
	 */
	if (!empty($summaryupdate)) {
		$stuff = "";
		foreach ($summaryupdate as $column => $value) {
			$stuff .= ', '.$column. ($value[1] ? "=" : "=$column+") . $value[0];
		}

		mysql_query("UPDATE summary SET ".substr($stuff, 1)." WHERE info_hash=\"$info_hash\"");
	}
?>
