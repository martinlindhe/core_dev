<?php
	/*
	 * danomac's changelog may-2005
	 *    -brought core functions to phpbt 1.5
	 * danomac's changelog oct-2004
	 *    -added autoBanByIP()
	 * danomac's changelog 15-Nov-2003: 
	 *    -changed the collectBytes() function to track peer uploads
	 *    -changed the runSpeed() function to also computer the avf % done on torrent
	 *    -added scrapeVerifyTorrent() -- checks to see if the hash requested is "hidden"
	 *
	 * Copyright (C) 2004 DeHackEd
	 * Portions Copyright (C) 2004 danomac
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
	 * Worker functions
	 */

	if (function_exists("bcadd")) {
		function sqlAdd($left, $right) {
			return bcadd($left, $right,0);
		}

		function sqlSubtract($left, $right) {
			return bcsub($left, $right,0);
		}

		function sqlMultiply($left, $right) {
			return bcmul($left, $right,0);
		}

		function sqlDivide($left, $right) {
			return bcdiv($left, $right,0);
		}
	} else {
		/*
		 * BC vs SQL math
		 *
		 * Uses the mysql database connection to perform string math. :)
		 * Used by byte counting functions
		 * No error handling as we assume nothing can go wrong. :|
		 */
		function sqlAdd($left, $right) {
			$query = 'SELECT '.$left.'+'.$right;
			$results = mysql_query($query) or showError("Database error.");
			return mysql_result($results,0,0);
		}

		function sqlSubtract($left, $right) {
			$query = 'SELECT '.$left.'-'.$right;
			$results = mysql_query($query) or showError("Database error");
			return mysql_result($results,0,0);
		}

		function sqlDivide($left, $right) {
			$query = 'SELECT '.$left.'/'.$right;
			$results = mysql_query($query) or showError("Database error");
			return mysql_result($results,0,0);
		}

		function sqlMultiply($left, $right) {
			$query = 'SELECT '.$left.'*'.$right;
			$results = mysql_query($query) or showError("Database error");
			return mysql_result($results,0,0);
		}
	}

	/*
	 * Function to return microtime as a float. Used to calculate
	 * page generation times.
	 */
	function mtime_float() {
	   list($usec, $sec) = explode(" ", microtime());
	   return ((float)$usec + (float)$sec);
	}

	/*
	 * Runs a query with no regard for the result
	 */
	function quickQuery($query) {
		$results = @mysql_query($query);
		if (!is_bool($results))
			mysql_free_result($results);
		else
			return $results;
		return true;
	}

	function hex2bin ($input, $assume_safe=true) {
		if ($assume_safe !== true && ! ((strlen($input) % 2) === 0 || preg_match ('/^[0-9a-f]+$/i', $input)))
			return "";
		return pack('H*', $input );
	}

	/*
	 * Reports an error to the client in $message.
	 * Any other output will confuse the client, so please don't do that.
	 */
	function showError($message, $log=false) {
	  if ($log)
		  error_log("PHPBTTracker: Sent error ($message)");
	  echo "d14:failure reason".strlen($message).":$message"."e";
	  exit;
	}


	/*
	 * Used by newtorrents.php and the dynamic_torrents setting
	 * Returns true/false, depending on if there were errors.
	 */
	function makeTorrent($hash, $tolerate = false) {
		if (strlen($hash) != 40)
			showError("makeTorrent: Received an invalid hash");

		$result = true;

		$query = "CREATE TABLE x$hash (peer_id char(40) NOT NULL default '', bytes bigint NOT NULL default 0, ip char(50) NOT NULL default 'error.x', port smallint UNSIGNED NOT NULL default \"0\", status enum('leecher','seeder') NOT NULL, lastupdate int unsigned NOT NULL default 0, sequence int unsigned AUTO_INCREMENT NOT NULL, natuser enum('N', 'Y') not null default 'N', uploaded bigint NOT NULL default 0, clientversion varchar(250) default \"\", primary key(sequence), unique(peer_id))";
		if (!@mysql_query($query))
			$result = false;

		if (!$result && !$tolerate)
			return false;

		if (isset($GLOBALS["peercaching"]) && $GLOBALS["peercaching"]) {
			$query = "CREATE TABLE y$hash (sequence int unsigned NOT NULL default 0, with_peerid char(101) NOT NULL default '', without_peerid char(40) NOT NULL default '', compact binary(6) NOT NULL DEFAULT '', unique k (sequence)) DELAY_KEY_WRITE=1 CHECKSUM=0";
			mysql_query($query);
		}

		$query = "INSERT INTO summary set info_hash=\"".$hash."\", lastSpeedCycle=UNIX_TIMESTAMP()";
		if (!@mysql_query($query))
			$result = false;

		return $result;
	}

	/*
	 * Returns true if the torrent exists.
	 * Currently checks by locating the row in "summary"
	 * Always returns true if $dynamic_torrents=="1" unless an error occured
	 */
	function verifyTorrent($hash) {
		if ($GLOBALS["dynamic_torrents"])
			$query = "SELECT COUNT(`info_hash`) FROM `summary` WHERE `info_hash`=\"$hash\"";
		else
			$query = "SELECT COUNT(`info_hash`) FROM `summary` WHERE `info_hash`=\"$hash\" AND `external_torrent` = \"N\"";
		
		$results = mysql_query($query);
	
		$res = mysql_result($results,0,0);
	
		if ($res == 1)
			return true;
	
		if ($GLOBALS["dynamic_torrents"])
			return makeTorrent($hash);

		return false;
	}

	function verifyHash($input) {
		if (strlen($input) === 40 && preg_match('/^[0-9a-f]+$/', $input))
			return true;
		else
			return false;
	}




	/*
	 * Returns info on one peer
	 */
	function getPeerInfo($user, $hash) {
		// If "trackerid" is set, let's try that
		if (isset($GLOBALS["trackerid"])) {
			$query = "SELECT peer_id,bytes,ip,port,status,lastupdate,sequence FROM x$hash WHERE sequence=${GLOBALS["trackerid"]}";
			$results = mysql_query($query) or showError("Tracker error: invalid torrent");
			$data = mysql_fetch_assoc($results);

			if (!$data || $data["peer_id"] != $user) {
				// Damn, but don't crash just yet.
				$query = "SELECT peer_id,bytes,ip,port,status,lastupdate,sequence from x$hash where peer_id=\"$user\"";
				$results = mysql_query($query) or showError("Tracker error: invalid torrent"); 
				$data = mysql_fetch_assoc($results);
				$GLOBALS["trackerid"] = $data["sequence"];
			}
		} else {
			$query = "SELECT peer_id,bytes,ip,port,status,lastupdate,sequence from x$hash where peer_id=\"$user\"";
			$results = mysql_query($query) or showError("Tracker error: invalid torrent");
			$data = mysql_fetch_assoc($results);
			$GLOBALS["trackerid"] = $data["sequence"];
		}
	
		if (!($data))
			return false;
	
		return $data;
	}

	/*
	 * Slight redesign of loadPeers
	 */
	function getRandomPeers($hash, $where="") {
		// Don't want to send a bad "num peers" for new seeds

		if ($GLOBALS["NAT"])
			$results = mysql_query("SELECT COUNT(*) FROM x$hash WHERE natuser = 'N'");
		else
			$results = mysql_query("SELECT COUNT(*) FROM x$hash");

		$peercount = mysql_result($results, 0,0);

		// ORDER BY RAND() is expensive. Don't do it when the load gets too high
		if ($peercount < 500)
			$query = "SELECT ".((isset($_GET["no_peer_id"]) && $_GET["no_peer_id"] == 1) ? "" : "peer_id,")."ip, port, status FROM x$hash ".$where." ORDER BY RAND() LIMIT ${GLOBALS['maxpeers']}";
		else
			$query = "SELECT ".((isset($_GET["no_peer_id"]) && $_GET["no_peer_id"] == 1) ? "" : "peer_id,")."ip, port, status FROM x$hash LIMIT ".@mt_rand(0, $peercount - $GLOBALS["maxpeers"]).", ${GLOBALS['maxpeers']}";

		$results = mysql_query($query);
		if (!$results)
			return false;

		$peerno = 0;
		while ($return[] = mysql_fetch_assoc($results))
			$peerno++;

		array_pop ($return);
		mysql_free_result($results);
		$return['size'] = $peerno;
 
		return $return;
	}
	
	/*
	 * Deletes a peer from the system and performs all cleaning up
	 *
	 *  $assumepeer contains the result of getPeerInfo, or false
	 *  if we should grab it ourselves.
	 */
	function killPeer($userid, $hash, $left, $assumepeer = false) {
		if (!$assumepeer) {
			$peer = getPeerInfo($userid, $hash);
			if (!$peer)
				return;
			if ($left != $peer["bytes"])
				$bytes = sqlSubtract($peer["bytes"], $left);
			else
				$bytes = 0;
		} else {
			$bytes = 0;
			$peer = $assumepeer;
		}
	
		quickQuery("DELETE FROM x$hash WHERE peer_id=\"$userid\"");
		if (mysql_affected_rows() == 1) {
			if ($GLOBALS["peercaching"])
				quickQuery("DELETE FROM y$hash WHERE sequence=" . $peer["sequence"]);

			if ($peer["status"] == "leecher")
				summaryAdd("leechers", -1);
			else
				summaryAdd("seeds", -1);

			if ($GLOBALS["countbytes"] && ((float)$bytes) > 0)
				summaryAdd("dlbytes",$bytes);

			if ($peer["bytes"] != 0 && $left == 0)
				summaryAdd("finished", 1);
		}
	}

	/*
	 * Updates the peer user's info.
	 * Currently it does absolutely nothing. lastupdate is set in collectBytes
	 * as well.
	 */
	function updatePeer($peerid, $hash) {
		return;
	}

	/*
	 * Transfers bytes from "left" to "dlbytes" when a peer reports in.
	 */
	function collectBytes($peer, $hash, $left, $uploaded) {
		$peerid=$peer["peer_id"];

		if (!$GLOBALS["countbytes"]) {
			quickQuery("UPDATE x$hash SET lastupdate=UNIX_TIMESTAMP(), uploaded=$uploaded where " . (isset($GLOBALS["trackerid"]) ? "sequence=\"${GLOBALS["trackerid"]}\"" : "peer_id=\"$peerid\""));
			return;
		}

		$diff = sqlSubtract($peer["bytes"], $left);
		quickQuery("UPDATE x$hash set " . (($diff != 0) ? "bytes=\"$left\"," : ""). " lastupdate=UNIX_TIMESTAMP(), uploaded=$uploaded where " . (isset($GLOBALS["trackerid"]) ? "sequence=\"${GLOBALS["trackerid"]}\"" : "peer_id=\"$peerid\""));

		// Anti-negative clause
		if (((float)$diff) > 0) {
			summaryAdd("dlbytes", $diff);
		}
	}

	/*
	 * Transmits the actual data to the peer. No other output is permitted if
	 * this function is called, as that would break BEncoding.
	 * I don't use the bencode library, so watch out! If you add data,
	 * rules such as dictionary sorting are enforced by the remote side.
	 */
	function sendPeerList($peers) {
		echo "d";
	  	echo "8:intervali".$GLOBALS["report_interval"]."e";
		if (isset($GLOBALS["min_interval"]))
			echo "12:min intervali".$GLOBALS["min_interval"]."e";

		echo "5:peers";
		$size=$peers["size"];
		if (isset($_GET["compact"]) && $_GET["compact"] == '1') {
			$p = '';
			for ($i=0; $i < $size; $i++)
				$p .= pack("Nn", ip2long($peers[$i]['ip']), $peers[$i]['port']);
			echo strlen($p).':'.$p;
		} else {
			// no_peer_id or no feature supported
			echo 'l';
			for ($i=0; $i < $size; $i++) {
				echo "d2:ip".strlen($peers[$i]["ip"]).":".$peers[$i]["ip"];
				if (isset($peers[$i]["peer_id"]))
					echo "7:peer id20:".hex2bin($peers[$i]["peer_id"]);
				echo "4:porti".$peers[$i]["port"]."ee";
			}
			echo "e";
		}

		if (isset($GLOBALS["trackerid"])) {
			// Now it gets annoying. trackerid is a string
			echo "10:tracker id".strlen($GLOBALS["trackerid"]).":".$GLOBALS["trackerid"];
		}

		echo "e";
	}

	/*
	 * Faster pass-through version of getRandompeers => sendPeerList
	 * It's the only way to use cache tables. In fact, it only uses it.
	 */
	function sendRandomPeers($info_hash) {
		$result = mysql_query("SELECT COUNT(*) FROM y$info_hash");
		$count = mysql_result($result, 0, 0);
	
		if (isset($_GET["compact"]) && $_GET["compact"] == '1')
			$column = "compact";
		elseif (isset($_GET["no_peer_id"]) && $_GET["no_peer_id"] == '1')
			$column = "without_peerid";
		else
			$column = "with_peerid";
	
		if ($count < $GLOBALS["maxpeers"])
			$query = "SELECT $column FROM y$info_hash";
		elseif ($count > 500) {
			do	{
				$rand1 = mt_rand(0, $count-$GLOBALS["maxpeers"]);
				$rand2 = mt_rand(0, $count-$GLOBALS["maxpeers"]);
			} while (abs($rand1 - $rand2) < $GLOBALS["maxpeers"]/2);

			$query = "(SELECT $column FROM y$info_hash LIMIT $rand1, ".($GLOBALS["maxpeers"]/2). ") UNION (SELECT $column FROM y$info_hash LIMIT $rand2, ".($GLOBALS["maxpeers"]/2). ")";
		} else
			$query = "SELECT $column FROM y$info_hash ORDER BY RAND() LIMIT ".$GLOBALS["maxpeers"];

		echo "d";
  		echo "8:intervali".$GLOBALS["report_interval"]."e";

		if (isset($GLOBALS["min_interval"]))
			echo "12:min intervali".$GLOBALS["min_interval"]."e";

		echo "5:peers";

		$result = mysql_query($query);
		if ($column == "compact") {
			echo (mysql_num_rows($result) * 6) . ":";
			while ($row = mysql_fetch_row($result))
				echo str_pad($row[0], 6, chr(32));
		} else {
			echo "l";
			while ($row = mysql_fetch_row($result))
				echo "d".$row[0]."e";
			echo "e";
		}

		if (isset($GLOBALS["trackerid"]))
			echo "10:tracker id".strlen($GLOBALS["trackerid"]).":".$GLOBALS["trackerid"];
		echo "e";
	}


	/*
	 * Returns a $peers array of all peers that have timed out (2* report interval seems fair
	 * for any reasonable report interval (900 or larger))
	 */
	function loadLostPeers($hash, $timeout) {
		$results = mysql_query("SELECT peer_id,bytes,ip,port,status,lastupdate,sequence from x$hash where lastupdate < (UNIX_TIMESTAMP() - 2 * $timeout)");
		$peerno = 0;
		if (!$results)
			return false;
	
		while ($return[] = mysql_fetch_assoc($results))
			$peerno++;	
		array_pop($return);
		$return["size"] = $peerno;
		mysql_free_result($results);
		return $return;
	}

	function trashCollector($hash, $timeout) {
		if (isset($GLOBALS["trackerid"]))
			unset($GLOBALS["trackerid"]);

		if (!Lock($hash))
			return;
	
		$results = mysql_query("SELECT lastcycle FROM summary WHERE info_hash='$hash'");
		$lastcheck = (mysql_fetch_row($results));
	
		// Check once every re-announce cycle
		if (($lastcheck[0] + $timeout) < time()) {
			$peers = loadLostPeers($hash, $timeout);
			for ($i=0; $i < $peers["size"]; $i++)
				killPeer($peers[$i]["peer_id"], $hash, $peers[$i]["bytes"]);
			summaryAdd("lastcycle", "UNIX_TIMESTAMP()", true);
		}
		Unlock($hash);
	}

	/*
	 * Attempts to aquire a lock by name.
	 * Returns true on success, false on failure
	 */
	function Lock($hash, $time = 0) {
		$results = mysql_query("SELECT GET_LOCK('$hash', $time)");
		$string = mysql_fetch_row($results);
		if (strcmp($string[0], "1") == 0)
			return true;
		return false;
	}

	/*
	 * Releases a lock. Ignores errors.
	 */
	function Unlock($hash) {
		quickQuery("SELECT RELEASE_LOCK('$hash')");
	}

	/*
	 * Returns true if the lock is available
	 */
	function isFreeLock($lock) {
		if (Lock($lock, 0)) {
			Unlock($lock);
			return true;
		}
		return false;
	}


	/* 
	 * Returns true if the user is firewalled, NAT'd, or whatever.
	 * The original tracker had its --nat_check parameter, so
	 * here is my version.
	 *
	 * This code has proven itself to be sufficiently correct,
	 * but will consume system resources when a lot of httpd processes
	 * are lingering around trying to connect to remote hosts.
	 * Consider disabling it under higher loads.
	 */
	function isFireWalled($hash, $peerid, $ip, $port) {
		// NAT checking off?
		if (!$GLOBALS["NAT"])
			return false;

		$protocol_name = 'BitTorrent protocol';
		$theError = "";
		// Hoping 10 seconds will be enough
		$fd = fsockopen($ip, $port, $errno, $theError, 10);
		if (!$fd)
			return true;

		stream_set_timeout($fd, 5, 0);
		fwrite($fd, chr(strlen($protocol_name)).$protocol_name.hex2bin("0000000000000000").
			hex2bin($hash));
	
		$data = fread($fd, strlen($protocol_name)+1+20+20+8); // ideally...
	
		fclose($fd);
		$offset = 0;

		// First byte: strlen($protocol_name), then the protocol string itself
		if (ord($data[$offset]) != strlen($protocol_name))
			return true;

		$offset++;
		if (substr($data, $offset, strlen($protocol_name)) != $protocol_name)
			return true;

		$offset += strlen($protocol_name);
		// 8 bytes reserved, ignore
		$offset += 8;
	
		// Download ID (hash)
		if (substr($data, $offset, 20) != hex2bin($hash))
			return true;

		$offset+=20;
	
		// Peer ID
		if (substr($data, $offset, 20) != hex2bin($peerid))
			return true;

		return false;
	}

	/*
	 * It's cruel, but if people abuse my tracker, I just might do it.
	 * It pretends to accept the torrent, and reports that you are the
	 * only person connected.
	 */
	function evilReject($ip, $peer_id, $port) {
		// For those of you who are feeling evil, comment out this line.
		showError("Torrent is not authorized for use on this tracker.");

		$peers[0]["peer_id"] = $peer_id;
		$peers[0]["ip"] = $ip;
		$peers[0]["port"] = $port;
		$peers["size"] = 1;
		$GLOBALS["report_interval"] = 86400;
		$GLOBALS["min_interval"] = 86000;
		sendPeerList($peers);
		exit(0);
	}


	function runSpeed($info_hash, $delta) {
		//stick in our latest data before we calc it out
		quickQuery("INSERT IGNORE INTO timestamps (info_hash, bytes, delta, sequence) SELECT '$info_hash' AS info_hash, dlbytes, UNIX_TIMESTAMP() - lastSpeedCycle, NULL FROM summary WHERE info_hash=\"$info_hash\"");

		// mysql blows sometimes so we have to read the data into php before updating it
		$results = mysql_query('SELECT (MAX(bytes)-MIN(bytes))/SUM(delta), COUNT(*), MIN(sequence) FROM timestamps WHERE info_hash="'.$info_hash.'"' );
		$data = mysql_fetch_row($results);
	
		summaryAdd("speed", $data[0], true);
		summaryAdd("lastSpeedCycle", "UNIX_TIMESTAMP()", true);

		// if we have more than 20 drop the rest
		if ($data[1] == 21)
			quickQuery("DELETE FROM timestamps WHERE info_hash=\"$info_hash\" AND sequence=${data[2]}");
		elseif ($data[1] > 21)
			// This query requires MySQL 4.0.x, but should rarely be used.
			quickQuery ('DELETE FROM timestamps WHERE info_hash="'.$info_hash.'" ORDER BY sequence LIMIT '.($data['1'] - 20));
	}

	/*
	 * Schedules an update to the summary table. It gets so much traffic
	 * that we do all our changes at once.
	 * When called, the column $column for the current info_hash is incremented
	 * by $value, or set to exactly $value if $abs is true.
	 */
	function summaryAdd($column, $value, $abs = false) {
		if (isset($GLOBALS["summaryupdate"][$column])) {
			if (!$abs)
				$GLOBALS["summaryupdate"][$column][0] += $value;
			else
				showError("Tracker bug calling summaryAdd");
		} else {
			$GLOBALS["summaryupdate"][$column][0] = $value;
			$GLOBALS["summaryupdate"][$column][1] = $abs;
		}
	}

	/*
	 * Bans the IP address indicated
	 */
	function autoBanByIP($ip, $iplong, $reason, $length) {
		$expiry = date("Y-m-d", time() + ($length * 86400));

		@mysql_query("INSERT INTO ipbans (ip, iplong, bandate, reason, autoban, banlength, banexpiry, banautoexpires) 
									VALUES (\"$ip\",
										$iplong, 
										\"". date("Y-m-d") ."\", 
										\"$reason\", 
										\"Y\", $length, \"$expiry\", \"Y\")");
	}

	/*
	 * Checks to see if a string IP address is valid.
	 *
	 * Added: 5-Apr-2004
	 */
	function checkIP($ip) {
		$iparray = explode(".", $ip);
		if (count($iparray) != 4)
			return false;

		if (!is_numeric($iparray[0]) ||
			!is_numeric($iparray[1]) ||
			!is_numeric($iparray[2]) ||
			!is_numeric($iparray[3]))
			return false;

		foreach ($iparray as $ipnum) {
			if ($ipnum > 255 || $ipnum < 0)
				return false;
		}
		return true;
	}

	/*
	 * Compute average torrent progress for given hash
	 */
	function runAvg($info_hash) {
		// start to get the data needed to compute avg % done
		//get file size (in MiB)
		$queryresults = mysql_query("SELECT size FROM namemap WHERE info_hash=\"$info_hash\"");
		$file_size_MiB = mysql_fetch_row($queryresults);
		$file_size_MiB = $file_size_MiB[0];

		//get the average done from the database to compare calculation with
		$queryresults = mysql_query("SELECT avgdone FROM summary WHERE info_hash=\"$info_hash\"");
		$storedavgdone = mysql_fetch_row($queryresults);
		$storedavgdone = $storedavgdone[0];

		//..and get the average # of bytes left
		$queryresults = mysql_query("SELECT peer_id, bytes FROM x$info_hash WHERE status=\"leecher\"");
		$rowcountavg = mysql_num_rows($queryresults);

		$avgcompute=0;

		//well, the avg() function in mysql was producing some odd results... let's try it manually
		for ($i=0; $i < $rowcountavg; $i++) {
			$rowavg = mysql_fetch_row($queryresults);
			$avgcompute += $rowavg[1];
		}

		$avgcompute = round(($avgcompute / $rowcountavg) / 1048576, 2);

		//make sure the file size is valid, then compute...
		if ($file_size_MiB==0) {
			// error, possible div/0 runtime error
			$avgdone = 0;
		} else {
			//ok, compute (in MiB)
			$avgdone = round(100 - (($avgcompute / $file_size_MiB) * 100), 2);
		}

		//eh, if it's 100%, that usually means no leechers are present, so set to 0...
		if ($avgdone == 100.0) $avgdone = 0;

		// Make database changes if needed
		if ($storedavgdone != $avgdone) 
			quickQuery("UPDATE summary SET avgdone=$avgdone WHERE info_hash=\"$info_hash\"");
	}

	/*
	 * for the scrape function: return true if the hash is NOT hidden, false if we aren't gonna send info! :-)
	 */
	function scrapeVerifyHash($hash) {
		$query = mysql_query("SELECT info_hash, hide_torrent FROM summary WHERE info_hash=\"$hash\"");
		$row = mysql_fetch_row($query);
	
		if (!$row) return false;	
	
		//if it's hidden return false.
		if ($row[1]=="Y") return false;

		//nothing to hide here!
		return true;
	}

	/*
	 * Function that does the consistency check on the database
	 */
	function consistencyCheck($outputHTML = false, $locking = false) {
		/*
		 * These are the alternating Cascadying Style Sheet classes used for the data.
		 */
		$classRowBGClr[0] = 'CLASS="odd"';
		$classRowBGClr[1] = 'CLASS="even"';

		$summaryupdate = array();

		/*
		 * Note: this function assumes a database connection is present already...
		 */

		/*
		 * Lock tables if requested
		 */
		if ($locking) {
			quickQuery("LOCK TABLES summary WRITE, namemap READ");
		}

		/*
		 * Throughout this function, we will check if HTML output is
		 * necessary and only show it if requested
		 */
		if ($outputHTML) {
			echo "\t<TABLE CLASS=\"tblAdminOuter\">\r\n";
			echo "\t<TR>\r\n";
			echo "\t\t<TD CLASS=\"heading\">Name/Info Hash</TD>\r\n";
			echo "\t\t<TD CLASS=\"heading\">Size</TD>\r\n";
			echo "\t\t<TD CLASS=\"heading\">UL</TD>\r\n";
			echo "\t\t<TD CLASS=\"heading\">DL</TD>\r\n";
			echo "\t\t<TD CLASS=\"heading\">XFER</TD>\r\n";
			echo "\t\t<TD CLASS=\"heading\">Speed</TD>\r\n";
			echo "\t\t<TD CLASS=\"heading\">Avg %<BR>done</TD>\r\n";
			echo "\t\t<TD CLASS=\"heading\">Stale clients</TD>\r\n";
			echo "\t\t<TD CLASS=\"heading\">Peer Cache</TD>\r\n";
			echo "\t</TR>\r\n";
		}

		/*
		 * OK, grab a list of items to check...
		 */
		$recordset = mysql_query("SELECT summary.info_hash, seeds, leechers, dlbytes, speed, avgdone, namemap.filename, namemap.size FROM summary LEFT JOIN namemap ON summary.info_hash = namemap.info_hash WHERE summary.external_torrent = 'N'");

		$counter = 0;
		while ($row = mysql_fetch_row($recordset)) {
			/*
			 * Figure out which background colour to use
			 */
			$cellBG = $classRowBGClr[$counter % 2];

			list($hash, $seeders, $leechers, $bytes, $speed, $avgdone, $filename, $fsize) = $row;

			/*
			 * Lock tables if needed
			 */
			if ($locking) {
				if ($GLOBALS["peercaching"]) {
					quickQuery("LOCK TABLES x$hash WRITE, y$hash WRITE, summary WRITE");
				} else {
					quickQuery("LOCK TABLES x$hash WRITE, summary WRITE");
				}
			}

			$statusresults = mysql_query("SELECT status, COUNT(status) from x$hash GROUP BY status");

			if ($outputHTML) {
				echo "\t<TR>\r\n\t\t<TD $cellBG>";
				if (!is_null($filename))
					echo $filename;
				else
					echo $hash;
				echo "</TD>\r\n\t\t<TD $cellBG>$fsize</TD>\r\n";
			}

			if (!$statusresults) {
				if ($outputHTML) {
					echo "\t\t<TD $cellBG COLSPAN=\"2\">Unable to process UL/DL: ".mysql_error()."</TD></TR>\r\n";	
				}
			} else {
				/*
				 * OK, now get the amount of seeders and leechers
				 */
				$counts = array();
				while ($statusrow = mysql_fetch_row($statusresults)) {
					$counts[$statusrow[0]] = $statusrow[1];
				}
	
				/*
				 * If there is nothing set, the values are 0 (zero)
				 */
				if (!isset($counts["leecher"]))
					$counts["leecher"] = 0;

				if (!isset($counts["seeder"]))
					$counts["seeder"] = 0;

				/*
				 * Check and fix seeder count if needed
				 */
				if ($counts["seeder"] != $seeders)
				{
					quickQuery("UPDATE summary SET seeds=".$counts["seeder"]." WHERE info_hash=\"$hash\"");
					if ($outputHTML) {
						echo "\t\t<TD $cellBG>$seeders -> ".$counts["seeder"]."</TD>\r\n";
					}
				} else {
					if ($outputHTML) {
					echo "\t\t<TD $cellBG>$seeders</TD>\r\n";
					}
				}

				/*
				 * Check and fix leecher count if needed
				 */
				if ($counts["leecher"] != $leechers) {
					quickQuery("UPDATE summary SET leechers=".$counts["leecher"]." WHERE info_hash=\"$hash\"");
					if ($outputHTML) {
						echo "\t\t<TD $cellBG>$leechers -> ".$counts["leecher"]."</TD>";
					}
				} else {
					if ($outputHTML) {
						echo "\t\t<TD $cellBG>$leechers</TD>\r\n";
					}
				}
			}

			/*
			 * Check the amount transferred
			 */
			if ($bytes < 0) {
				quickQuery("UPDATE summary SET dlbytes=0 WHERE info_hash=\"$hash\"");
				if ($outputHTML) {
					echo "\t\t<TD $cellBG>$bytes -> Zero</TD>\r\n";
				}
			}
			else {
				if ($outputHTML) {
					echo "\t\t<TD $cellBG>". round($bytes/1048576/1024,3) ." GB</TD>\r\n";
				}
			}

			/*
			 * Yes, the speed should never be below zero... but let's check anyway
			 * Also, if there are no leechers it can be reset.
			 */
			if ($speed < 0 || $counts["leecher"] == 0) {
				if ($outputHTML) {
					echo "\t\t<TD $cellBG>Reset to zero</TD>\r\n";
				}

				quickQuery("UPDATE summary SET speed=0 WHERE info_hash=\"$hash\"");
			} else {
				if ($outputHTML) {
					echo "\t\t<TD $cellBG>". round($speed/1024,1) ." KB/sec</TD>\r\n";
				}
			}

			/*
			 * Reset the average % done to zero if negative or no peers on torrent
			 */
			if ($avgdone < 0 || $counts["leecher"] == 0) {
				if ($outputHTML) {
					echo "\t\t<TD $cellBG>Reset to zero</TD>\r\n";
				}

				quickQuery("UPDATE summary SET avgdone=0 WHERE info_hash=\"$hash\"");
			} else {
				if ($outputHTML) {
					echo "\t\t<TD $cellBG>". round($avgdone, 1) ." %</TD>\r\n";
				}
			}

			/*
			 * Okay, now remove stale peers if needed....
			 */
			checkForStalePeers($hash, $GLOBALS['report_interval'], time(), $cellBG, $outputHTML);

			if ($outputHTML) {
				echo "\t\t<TD $cellBG>";
			}
		

			if ($GLOBALS["peercaching"]) {
	
				$result = mysql_query("SELECT x$hash.sequence FROM x$hash LEFT JOIN y$hash ON x$hash.sequence=y$hash.sequence WHERE y$hash.sequence IS NULL") or die(mysql_error());
				if (mysql_num_rows($result) > 0) {
					if ($outputHTML) {
						echo "Added ", mysql_num_rows($result);
						$row = array();
					}
			
					while ($data = mysql_fetch_row($result))
						$row[] = "sequence=\"${data[0]}\"";
					$where = implode(" OR ", $row);
					$query = mysql_query("SELECT * FROM x$hash WHERE $where");
			
					while ($row = mysql_fetch_assoc($query)) {
						$compact = mysql_real_escape_string(pack('Nn', ip2long($row["ip"]), $row["port"]));
						$peerid = mysql_real_escape_string('2:ip' . strlen($row["ip"]) . ':' . $row["ip"] . '7:peer id20:' . hex2bin($row["peer_id"]) . "4:porti{$row["port"]}e");
						$no_peerid = mysql_real_escape_string('2:ip' . strlen($row["ip"]) . ':' . $row["ip"] . "4:porti{$row["port"]}e");
						mysql_query("INSERT INTO y$hash SET sequence=\"{$row["sequence"]}\", compact=\"$compact\", with_peerid=\"$peerid\", without_peerid=\"$no_peerid\"");
					}
				}	
				else {
					if ($outputHTML) {
						echo "Added none";
					}
				}
	
				$result = mysql_query("SELECT y$hash.sequence FROM y$hash LEFT JOIN x$hash ON y$hash.sequence=x$hash.sequence WHERE x$hash.sequence IS NULL");
				if (mysql_num_rows($result) > 0)	{
					if ($outputHTML) {
						echo ", Deleted ",mysql_num_rows($result);
					}
	
					$row = array();
				
					while ($data = mysql_fetch_row($result))
						$row[] = "sequence=\"${data[0]}\"";
					$where = implode(" OR ", $row);
					$query = mysql_query("DELETE FROM y$hash WHERE $where");
				}
				else {
					if ($outputHTML) {
						echo ", Deleted none";
					}
				}
			} else {
				if ($outputHTML) {
					echo "Peer caching disabled";
				}
			}

			if ($outputHTML) {
				echo "</TD>\r\n";
				echo "\t</TR>\r\n";
			}
	
			$counter++;

			/*
			 * If locking was requested, release the tables
			 */
			if ($locking) {
				quickQuery("UNLOCK TABLES");
			}
		}

		if ($outputHTML) {
			echo "\t</TABLE>\r\n";
		}
	}

	/*
	 * Part of the consistency check, this will purge peers
	 * that have not reported in after the timeout specified
	 */
	function checkForStalePeers($hash, $timeout, $now, $cellBG, $outputHTML = false)	{
		/*
		 * Get a list of stale peers...
		 */ 	
 		$peers = loadLostPeers($hash, $timeout);

		/*
		 * ... and remove them
		 */
	 	for ($i=0; $i < $peers["size"]; $i++) {
			killPeer($peers[$i]["peer_id"], $hash, $peers[$i]["bytes"], $peers[$i]);
		}

		/*
		 * Show status, if needed...
		 */
		if ($outputHTML) {
			if ($i != 0) {
				echo "\t\t<TD CLASS=\"consistency\">Removed $i</TD>\r\n";
			} else {
				echo "\t\t<TD $cellBG>Removed 0</TD>\r\n";
			}
		}

	 	quickQuery("UPDATE summary SET lastcycle='$now' WHERE info_hash='$hash'");
	}

	/*
	 * Runs a RSS request. Can specify to be silent and output to a file OR
	 * output XML.
	 *
	 * Written: 28-Aug-05
	 */
	function doRSS($connectToDB = false, $xmlOutput = false, $category = 'all', $fhRSS = false, $subcat = false) {
		global $dbhost, $dbuser, $dbpass, $database, $rss_report_limit, $rss_xml_title, $rss_xml_desc, $rss_xml_link, $rss_xml_lang;
		
		if ($xmlOutput) {
			/*
			 * XML file output - no caching!
			 */
			if ($connectToDB) {
				/*
				 * Connect to the database server
				 */
				if ($GLOBALS["persist"])
					$db = mysql_pconnect($dbhost, $dbuser, $dbpass) or die("Error: can't connect to database - " . mysql_error());
				else
					$db = mysql_connect($dbhost, $dbuser, $dbpass) or die("Error: can't connect to database - " . mysql_error());
			
				/*
				 * Open the database
				 */
				mysql_select_db($database) or die("Error: can't open the database - " . mysql_error());
			}

			/*
			 * Check to see if a subcategory was requested
			 */
			if ($subcat !== false && is_numeric($subcat)) {
				/*
				 * A subcategory was requested, set up the WHERE clause accordingly
				 */
				$where = " AND `grouping` =  $subcat";
			} else {
				/*
				 * Get the new torrent information, taking into account the category requested.
				 */
				if ($category == 'all') 
					$where = '';
				else
					$where = " AND `category` = \"$category\"";
			}
	
			$rstRSS = mysql_query("SELECT `namemap`.`filename`, `namemap`.`url`, `namemap`.`info`, `namemap`.`torrent_size`, `summary`.`hide_torrent` FROM `namemap` LEFT JOIN `summary` ON summary.info_hash = namemap.info_hash WHERE `summary`.`hide_torrent` = 'N' $where ORDER BY `tsAdded` DESC LIMIT 0, $rss_report_limit") or die("Error: Can't run query. " . mysql_error());

			/*
			 * Only output if there is something to output!
			 */
			if (mysql_num_rows($rstRSS) > 0) {
				header("Content-Type: text/xml");
				echo "<?xml version=\"1.0\" ?>\r\n<rss version=\"2.0\">\r\n<channel>\r\n";
				echo "\t<title>$rss_xml_title</title>\r\n\t<link>$rss_xml_link</link>\r\n\t<description>$rss_xml_desc This output is limited to $rss_report_limit entries.</description>\r\n\t<language>$rss_xml_lang</language>\r\n";

				while ($row = mysql_fetch_row($rstRSS)) {
					if (is_null($row[2]) || strlen($row[2] == 0)) {
						$row[2] = "No description provided.";
					}

					/*
					 * Check to see if URL is encoded already.
					 */
					if (strpos($row[1], "%") === false) {
						/*
						 * To make the RSS 'valid' we need to encode the URL; first strip off the http:// part, then process
						 */
						if (strpos($row[1], "http://") == 0) {
							$row[1] = substr($row[1], 7);

							/*
							 * Now split up the URL and encode things seperately
							 */
							$splitURL = explode("/", $row[1]);
							foreach ($splitURL as $key => $URLfragment) {
								$splitURL[$key] = urlencode($URLfragment);
							}
						
							$encodedURL = "http://" . implode("/", $splitURL);
						} else {
							/*
							 * Now split up the URL and encode things seperately
							 */
							$splitURL = explode("/", $row[1]);
							foreach ($splitURL as $key => $URLfragment) {
								$splitURL[$key] = urlencode($URLfragment);
							}
						
							$encodedURL = implode("/", $splitURL);
						}					
					} else {
						$encodedURL = $row[1];
					}
					
					echo "\t<item>\r\n\t\t<title>$row[0]</title>\r\n\t\t<link>$encodedURL</link>\r\n\t\t<description>$row[2]</description>\r\n";
					echo "\t\t<enclosure url=\"$encodedURL\" length=\"$row[3]\" type=\"application/x-bittorrent\"/>\r\n";
					echo "\t</item>\r\n";
				}
				
				echo "</channel>\r\n</rss>\r\n";
				return true;
			}
			return false;
		} else {
			/*
			 * Output to cache location...
			 */
			if ($connectToDB) {
				/*
				 * Connect to the database server
				 */
				if ($GLOBALS["persist"])
					$db = mysql_pconnect($dbhost, $dbuser, $dbpass) or die("Error: can't connect to database - " . mysql_error());
				else
					$db = mysql_connect($dbhost, $dbuser, $dbpass) or die("Error: can't connect to database - " . mysql_error());
			
				/*
				 * Open the database
				 */
				mysql_select_db($database) or die("Error: can't open the database - " . mysql_error());
			}

			/*
			 * Check to see if a subcategory was requested
			 */
			if ($subcat !== false && is_numeric($subcat)) {
				/*
				 * A subcategory was requested, set up the WHERE clause accordingly
				 */
				$where = " AND `grouping` =  $subcat";
			} else {
				/*
				 * Get the new torrent information, taking into account the category requested.
				 */
				if ($category == 'all') 
					$where = '';
				else
					$where = " AND `category` = \"$category\"";
			}
	
			$rstRSS = mysql_query("SELECT `namemap`.`filename`, `namemap`.`url`, `namemap`.`info`, `namemap`.`torrent_size`, `summary`.`hide_torrent` FROM `namemap` LEFT JOIN `summary` ON summary.info_hash = namemap.info_hash WHERE `summary`.`hide_torrent` = 'N' $where ORDER BY `tsAdded` DESC LIMIT 0, $rss_report_limit") or die("Error: Can't run query. " . mysql_error());

			/*
			 * Only output if there is something to output!
			 */
			if (mysql_num_rows($rstRSS) > 0) {
				fwrite($fhRSS, "<?xml version=\"1.0\" ?>\r\n<rss version=\"2.0\">\r\n<channel>\r\n");
				fwrite($fhRSS, "\t<title>$rss_xml_title</title>\r\n\t<link>$rss_xml_link</link>\r\n\t<description>$rss_xml_desc This output is limited to $rss_report_limit entries.</description>\r\n\t<language>$rss_xml_lang</language>\r\n");

				while ($row = mysql_fetch_row($rstRSS)) {
					if (is_null($row[2]) || strlen($row[2] == 0)) {
						$row[2] = "No description provided.";
					}

					/*
					 * Check to see if URL is encoded already.
					 */
					if (strpos($row[1], "%") === false) {
						/*
						 * To make the RSS 'valid' we need to encode the URL; first strip off the http:// part, then process
						 */
						if (strpos($row[1], "http://") == 0) {
							$row[1] = substr($row[1], 7);

							/*
							 * Now split up the URL and encode things seperately
							 */
							$splitURL = explode("/", $row[1]);
							foreach ($splitURL as $key => $URLfragment) {
								$splitURL[$key] = urlencode($URLfragment);
							}
						
							$encodedURL = "http://" . implode("/", $splitURL);
						} else {
							/*
							 * Now split up the URL and encode things seperately
							 */
							$splitURL = explode("/", $row[1]);
							foreach ($splitURL as $key => $URLfragment) {
								$splitURL[$key] = urlencode($URLfragment);
							}
						
							$encodedURL = implode("/", $splitURL);
						}					
					} else {
						$encodedURL = $row[1];
					}
				
					fwrite($fhRSS, "\t<item>\r\n\t\t<title>$row[0]</title>\r\n\t\t<link>$encodedURL</link>\r\n\t\t<description>$row[2]</description>\r\n");
					fwrite($fhRSS, "\t\t<enclosure url=\"$encodedURL\" length=\"$row[3]\" type=\"application/x-bittorrent\"/>\r\n");
					fwrite($fhRSS, "\t</item>\r\n");
				}
				
				fwrite($fhRSS, "</channel>\r\n</rss>\r\n");
				return true;
			}			
		}
		return false;
	}
?>
