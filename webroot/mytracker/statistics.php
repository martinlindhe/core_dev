<?php
	header("Pragma: no-cache"); 
	/*
	 * Module: statistics.php - Shows statistics on the tracker's state.
	 *
	 * Copyright (C) 2004 by danomac
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

	/*
	 * Connect to the database server
	 */
	if ($GLOBALS["persist"])
		$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("Tracker error: can't connect to database. Contact the webmaster.");
	else
		$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("Tracker error: can't connect to database. Contact the webmaster.");

	/*
	 * Open required database
	 */
	@mysql_select_db($database) or die("Tracker error: can't open database. Contact the webmaster");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<LINK REL="stylesheet" HREF="tracker.css" TYPE="text/css" TITLE="Default">
	<TITLE>Tracker statistics</TITLE>
</HEAD>

<BODY>
<?php
	echo "<H1>TRACKER STATISTICS</H1>\r\n";
	echo "<BR><A HREF=\"#int\">Internal torrents</A>&nbsp;&nbsp;&nbsp;<A HREF=\"#ext\">External torrents</A>&nbsp;&nbsp;&nbsp;<A HREF=\"#ret\">Retired torrents</A>&nbsp;&nbsp;&nbsp;<A HREF=\"#sum\">Summary</A>&nbsp;&nbsp;&nbsp;<A HREF=\"#db\">Database status</A><BR>\r\n";
	echo "<A NAME=\"int\"><H2>Internal torrents</H2></A>\r\n";

	
	if (!($rstStats = @mysql_query("SELECT SUM(namemap.size), SUM(summary.seeds), SUM(summary.finished), SUM(summary.leechers), SUM(summary.dlbytes / 1073741824), SUM(summary.speed / 1024), COUNT(namemap.info_hash) FROM summary LEFT JOIN namemap ON summary.info_hash = namemap.info_hash WHERE summary.external_torrent=\"N\""))) {
		echo "Cannot get internal torrent tracker statistics.<BR><BR>\r\n";
	} else {
		$row = mysql_fetch_row($rstStats);
		list($size, $seeds, $finished, $leechers, $xferred, $speed, $total_torrents) = $row;


		if ($size > 100000000)
			$sizes = number_format($grand_total_size / 1073741824, 2) . " Petabytes";
		elseif ($size > 1000000) 
			$sizes = number_format($grand_total_size / 1048576, 2) . " Terabytes";
		elseif ($size > 1000)
			$sizes = number_format($size / 1024, 2) . " Gigabytes";
		else 
			$sizes = number_format($size, 2) . " Megabytes";

		echo "<B>Total active torrents</B>: $total_torrents<BR>\r\n";
		echo "<B>Total amount shared</B>: $sizes<BR><BR>\r\n";

		if ($xferred > 105000000)
			$xferreds = number_format($xferred / 1073741824, 3) . " Exabytes";
		elseif ($xferred > 1050000)
			$xferreds = number_format($xferred / 1048576, 3) . " Petabytes";
		elseif($xferred > 10500)
			$xferreds = number_format($xferred / 1024, 3) . " Terabytes";
		else
			$xferreds = number_format($xferred, 3) . " Gigabytes";

		echo "<B>Total amount transferred</B>: $xferreds<BR>\r\n";
		echo "<B>Total copies downloaded</B>: " . number_format($finished) . "<BR>\r\n";

		if ($speed > 1000)
			$speed = number_format($speed / 1024, 2) . " Megabytes per second";
		else
			$speed = number_format($speed, 2) . "Kilobytes per second";

		echo "<B>Total speed</B>: $speed<BR><BR>\r\n";

		echo "<B>Total seeds</B>: ". number_format($seeds) ."<BR>\r\n";
		echo "<B>Total leechers</B>: " . number_format($leechers) ."<BR>\r\n";

		$peers = number_format($leechers+$seeds);
		echo "<B>Total peers</B>: $peers<BR><BR>\r\n\r\n";
		
	}

	echo "<A NAME=\"ext\"><H2>External torrents</H2></A>\r\n";
	
	if (!($rstStats = @mysql_query("SELECT SUM(namemap.size), SUM(summary.seeds), SUM(summary.finished), SUM(summary.leechers), SUM(summary.dlbytes / 1073741824), SUM(summary.speed / 1024), COUNT(namemap.info_hash) FROM summary LEFT JOIN namemap ON summary.info_hash = namemap.info_hash WHERE summary.external_torrent=\"Y\""))) {
		echo "Cannot get external torrent tracker statistics.<BR><BR>\r\n";
	} else {
		$row = mysql_fetch_row($rstStats);
		list($esize, $eseeds, $efinished, $eleechers, $exferred, $espeed, $total_ext_torrents) = $row;

		if ($esize > 100000000)
			$esize = number_format($esize / 1073741824, 2) . " Petabytes";
		elseif ($esize > 1000000) 
			$esize = number_format($esize / 1048576, 2) . " Terabytes";
		elseif ($esize > 1000)
			$esize = number_format($esize / 1024, 2) . " Gigabytes";
		else 
			$esize = number_format($esize, 2) . " Megabytes";

		echo "<B>Total external torrents</B>: $total_ext_torrents<BR>\r\n";
		echo "<B>Total shared</B>: $esize<BR>\r\n";

		if ($exferred == 0) {
			$exferred = "unknown";
		} else {
			if ($exferred > 105000000)
				$exferred = number_format($exferred / 1073741824, 3) . " Exabytes";
			elseif ($exferred > 1050000) 
				$exferred = number_format($exferred / 1048576, 3) . " Petabytes";
			elseif ($exferred > 10500)
				$exferred = number_format($exferred / 1024, 3) . " Terabytes";
			else
				$exferred = number_format($exferred, 3) . " Gigabytes";
		}

		echo "<B>Total transferred</B>: $exferred<BR>\r\n";

		if ($efinished == 0)
			$efinished = "unknown";
		else
			$efinished = number_format($efinished);

		echo "<B>Total copies downloaded</B>: $efinished<BR>\r\n";

		if ($espeed == 0) {
			$espeed = "unknown";
		} else {
			if ($espeed > 1000)
				$espeed = number_format($espeed / 1024, 2) . " Megabytes per second";
			else
				$espeed = number_format($espeed, 2) . " Kilobytes per second";
		}

		echo "<B>Total speed</B>: $espeed<BR><BR>\r\n";

		echo "<B>Total seeds</B>: ". number_format($eseeds) ."<BR>\r\n";
		echo "<B>Total leechers</B>: " . number_format($eleechers) ."<BR>\r\n";

		$epeers = number_format($eleechers+$eseeds);
		echo "<B>Total peers</B>: $epeers<BR><BR>\r\n\r\n";
		
	}

	echo "<A NAME=\"ret\"><H2>Retired torrent statistics</H2></A>";
	if (!($rstStats = @mysql_query("SELECT SUM(size), SUM(completed), SUM(transferred) / 1073741824, COUNT(info_hash) FROM retired"))) {
		echo "Cannot get retired torrent tracker statistics.<BR><BR>\r\n";
	} else {
		$row = mysql_fetch_row($rstStats);
		list($rsize, $rdone, $rxfer, $total_retired_torrents) = $row;

		$rdones = number_format($rdone);

		if ($rsize > 100000000)
			$rsizes = number_format($rsize / 1073741824, 2) . " Petabytes";
		elseif ($rsize > 1000000) 
			$rsizes = number_format($rsize / 1048576, 2) . " Terabytes";
		elseif ($rsize > 1000)
			$rsizes = number_format($rsize / 1024, 2) . " Gigabytes";
		else 
			$rsizes = number_format($rsize, 2) . " Megabytes";

		if ($rxfer > 105000000)
			$rxfers = number_format($rxfer / 1073741824, 3) . " Exabytes";
		if ($rxfer > 1050000) 
			$rxfers = number_format($rxfer / 1048576, 3) . " Petabytes";
		elseif ($rxfer > 10500)
			$rxfers = number_format($rxfer / 1024, 3) . " Terabytes";
		else
			$rxfers = number_format($rxfer, 3) . " Gigabytes";

		echo "<B>Total retired torrents</B>: $total_retired_torrents<BR>\r\n";	
		echo "<B>Total size of retired torrents</B>: $rsizes<BR><BR>\r\n";

		echo "<B>Total completed downloads</B>: $rdones<BR>\r\n";
		echo "<B>Total amount transferred</B>: $rxfers<BR><BR>\r\n";
	}

	echo "<A NAME=\"sum\"><H2>Summary</H2></A>\r\n";

	if (isset($total_torrents) && isset($total_retired_torrents)) {
		$grand_total_torrents = number_format($total_torrents + $total_retired_torrents);
		$grand_total_size = $size + $rsize;

		if ($grand_total_size > 100000000)
			$gts = number_format($grand_total_size / 1073741824, 2) . " Petabytes";
		elseif ($grand_total_size > 1000000) 
			$gts = number_format($grand_total_size / 1048576, 2) . " Terabytes";
		elseif ($grand_total_size > 1000)
			$gts = number_format($grand_total_size / 1024, 2) . " Gigabytes";
		else 
			$gts = number_format($grand_total_size, 2) . " Megabytes";
		
		$grand_total_xfer = $xferred + $rxfer;
		if ($grand_total_xfer > 105000000)
			$grand_total_xfer = number_format($grand_total_xfer / 1073741824, 3) . " Exabytes";		
		elseif ($grand_total_xfer > 1050000) 
			$gtx = number_format($grand_total_xfer / 1048576, 3) . " Petabytes";
		elseif ($grand_total_xfer > 10500)
			$gtx = number_format($grand_total_xfer / 1024, 3) . " Terabytes";
		else
			$gtx = number_format($grand_total_xfer, 3) . " Gigabytes";

		
		$grand_total_done = number_format($rdone + $finished);

		echo "Since installation, this tracker has seen <B>$grand_total_torrents</B> torrents with a total size of <B>$gts</B>. In total, <B>$grand_total_done</B> copies were downloaded, with <B>$gtx</B> transferred.<BR><BR>\r\n";
		echo "Note: This includes retired torrent statistics, but <B>not</B> external torrent statistics.<BR><BR>\r\n";
	} else {
		echo "Can't calculate summary; either internal torrent or retired torrent statistics are missing.<BR><BR>\r\n";
	}

	echo "<A NAME=\"db\"><H2>Database status</H2></A>\r\n";
	
	if (!($rstStats = @mysql_query("SHOW STATUS"))) {
		echo "Cannot get database statistics.<BR><BR>\r\n";
	} else {
		$statArray = array();
		while ($stats = mysql_fetch_row($rstStats)) {
			if ($stats[0]	== "Uptime" ||
					$stats[0]	== "Slow_queries" ||
					$stats[0]	== "Questions" ||
					$stats[0]	== "Open_tables" ||
					$stats[0]	== "Bytes_received" ||
					$stats[0]	== "Bytes_sent") {
				$statArray[$stats[0]] = $stats[1];
			}
		}

		if ($statArray["Uptime"] != 0)
			$qps = round($statArray["Questions"] / $statArray["Uptime"], 2);
		else
			$qps = "Can't calculate";

		$days = floor($statArray["Uptime"] / 86400);
		$hours = floor(($statArray["Uptime"] - ($days * 86400)) / 3600);
		$minutes = floor(($statArray["Uptime"] - ($days * 86400) - ($hours * 3600)) / 60);
		$seconds = floor(($statArray["Uptime"] - ($days * 86400) - ($hours * 3600) - ($minutes * 60)));

		echo "<B>Uptime</B>: $days day(s), $hours hour(s), $minutes minute(s) and $seconds second(s).<BR><BR>\r\n";
		
		$received = round($statArray["Bytes_received"] / 1073741824, 3);
		$sent = round($statArray["Bytes_sent"] / 1073741824, 3);

		echo "<B>Bytes received</B>: $received GiB<BR>\r\n";
		echo "<B>Bytes sent</B>: $sent GiB<BR><BR>\r\n";

		echo "<B>Slow queries</B>: " . $statArray["Slow_queries"] . "<BR>\r\n";
		echo "<B>Queries</B>: " . number_format($statArray["Questions"]) . "<BR>\r\n";
		echo "<B>Queries per second average</B>: $qps <BR>\r\n";
	}
?>
</BODY>
</HTML>
