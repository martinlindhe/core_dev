<?php
	/*
	 * Module:	makecache.php
	 * Description: This script creates cache tables.
	 *
	 * Only minor changes made (mostly html changes.)
	 *
	 * Peer cache table creator.
	 *
	 * This program will partially hang the tracker during
	 * its execution as it tears through each torrent.
	 * It shouldn't take long, but might cause the system to jolt.
	 *
	 * This program only needs to be executed as part of the upgrade
	 * process for an earlier version of the tracker. Running it later
	 * will do nothing, so delete it when finished.
	 *
	 *
	 * Author:	danomac
	 * Written:	10-May-2005
	 *
	 * Copyright (C) 2004 deHackEd, danomac
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
	require_once("config.php");
	require_once("funcsv2.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<TITLE>PHPBTTracker+ Upgrade script</TITLE>
</HEAD>
<BODY>
	<H1>PHPBTTracker+ Upgrade - Peer cache table creator</H1>

	This script will create the peer caching tables required for the caching component of the tracker to be used. It does require that
	the configuration is set to peer caching before running this script.<BR><BR>

	<B>NOTE:</B> This script may cause the tracker to become unresponsive as it goes through all the active torrents on the tracker! This is normal.<BR><BR>
<?php
	if (!isset($GLOBALS["peercaching"])) {
		echo "<B>Peer caching configuration directive not found. Not proceeding.</B><BR>See config-sample.php included in the distribution tarball for the setting.<BR><BR>Short answer:<BR>\$GLOBALS[\"peercaching\"] = true;<BR>\r\n";
	} else {
		if (!$GLOBALS["peercaching"]) {
			echo "<B>Peer caching is disabled in the config file. Not proceeding.</B><BR>\r\n";
		} else {
			$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("\n\nCan't connect to database: ".mysql_error());
			@mysql_select_db($database) or die("Can't select database: ".mysql_error());

			$summary = mysql_query("SELECT info_hash FROM summary");

			while ($hash = mysql_fetch_row($summary))	{
				$info_hash = $hash[0];
	
				echo "\t<B>$info_hash</B> --- ";
	
				$query = "CREATE TABLE y$info_hash (sequence int unsigned NOT NULL default 0, with_peerid char(101) NOT NULL default '', without_peerid char(40) NOT NULL default '', compact binary(6) NOT NULL DEFAULT '', unique k (sequence)) DELAY_KEY_WRITE=1 CHECKSUM=0";
				$res = mysql_query($query);
				if (!$res) {
					echo "<FONT COLOR=RED>FAILED: </FONT>" . mysql_error() ."<BR>\r\n";
					continue;
				}

				mysql_query("LOCK TABLES x$info_hash READ, y$info_hash WRITE");

				$result = mysql_query("SELECT ip, port, peer_id, sequence FROM x$info_hash");

				$counter = 0;
				$cmd = "";	
				while ($row = mysql_fetch_assoc($result)) {
					$compact = mysql_escape_string(pack('Nn', ip2long($row["ip"]), $row["port"]));
					$peerid = mysql_escape_string('2:ip' . strlen($row["ip"]) . ':' . $row["ip"] . '7:peer id20:' . hex2bin($row["peer_id"]) . "4:porti{$row["port"]}e");
					$no_peerid = mysql_escape_string('2:ip' . strlen($row["ip"]) . ':' . $row["ip"] . "4:porti{$row["port"]}e");
	
					$cmd .= ", (${row["sequence"]}, \"$compact\", \"$peerid\", \"$no_peerid\")";
					$counter++;
					if ($counter >= 10) {
						mysql_query("INSERT INTO y$info_hash (sequence, compact, with_peerid, without_peerid) VALUES " . substr($cmd, 1));
						$cmd = "";
						$counter = 0;
					}
				}

				if ($counter > 0)
					mysql_query("INSERT INTO y$info_hash (sequence, compact, with_peerid, without_peerid) VALUES " . substr($cmd, 1));

				mysql_query("UNLOCK TABLES");
				echo "OK!<BR>\r\n";
			}
		}
	}
?>
</BODY>
</HTML>

