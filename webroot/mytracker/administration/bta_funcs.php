<?php
	/*
	 * Module:	bta_funcs.php
	 * Description: Helper functions and variables used throughout
	 * 		the Administration interface.
	 *
	 * Author:	danomac
	 * Written:	14-February-2004
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
	 * Variables used in most of the admin pages.
	 */
	$adm_pageerr_title = $phpbttracker_id . " " . $phpbttracker_ver." Administration Error";
	$adm_page_title = $phpbttracker_id . " " . $phpbttracker_ver . " Administration";

	/*
	 * Action constants used in the forms
	 */
	define("ACTION_DELETE", 1);
	define("ACTION_RETIRE", 2);
	define("ACTION_UNHIDE", 3);
	define("ACTION_HIDE", 9);
	define("ACTION_REVIVE", 4);
	define("ACTION_RDELETE", 5);
	define("ACTION_PBAN", 6);
	define("ACTION_PDELETE", 7);
	define("ACTION_UDELETE", 8);
	define("ACTION_SORT_GROUP", 10);
	define("ACTION_SORT_UNGROUP", 11);


	/* 
	 * A list of clients used by the decodePeerID() function
	 */
	$client_name["A"] = "ABC";
	$client_name["O"] = "Osprey Permaseed";
	$client_name["R"] = "Tribler";
	$client_name["S"] = "Shadow's Client";
	$client_name["T"] = "BitTornado";
	$client_name["U"] = "UPnP NAT Bit Torrent";
	$client_name["AR"] = "Arctic";
	$client_name["AX"] = "BitPump";
	$client_name["AZ"] = "Azureus";
	$client_name["BB"] = "BitBuddy";
	$client_name["BC"] = "BitComet";
	$client_name["BF"] = "Bitflu";
	$client_name["BS"] = "BTSlave";
	$client_name["BX"] = "Bittorrent X";
	$client_name["CD"] = "Enhanced CTorrent";
	$client_name["CT"] = "CTorrent";
	$client_name["EB"] = "EBit";
	$client_name["ES"] = "electric sheep";
	$client_name["KT"] = "KTorrent";
	$client_name["LP"] = "Lphant";
	$client_name["LT"] = "libTorrent";
	$client_name["lt"] = "libTorrent";
	$client_name["MP"] = "MooPolice";
	$client_name["MT"] = "MoonlightTorrent";
	$client_name["qB"] = "qBittorrent";
	$client_name["QT"] = "QT4 Torrent example";
	$client_name["RT"] = "Retriever";
	$client_name["SB"] = "~Swiftbit";
	$client_name["SS"] = "Swarmscope";
	$client_name["SZ"] = "Shareaza";
	$client_name["TN"] = "TorrentDotNET";
	$client_name["TR"] = "Transmission";
	$client_name["TS"] = "Torrentstorm";
	$client_name["UL"] = "uLeecher";
	$client_name["UT"] = "uTorrent";
	$client_name["XT"] = "XanTorrent";
	$client_name["ZT"] = "ZipTorrent";
	$client_name["BOWA"] = "Bits on Wheels";
	$client_name["OP"] = "Opera";
	$client_name["XBT"] = "XBT Client";
	$client_name["exbcLORD"] = "BitLord";
	$client_name["exbc"] = "BitComet";
	$client_name["FUTB"] = "BitComet";
	$client_name["M"] = "Mainline"; 

	/*
	 * Called if mysql gives an error.
	 */
	function sqlErr($err) {
		echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE><FONT COLOR='red' SIZE='+2'>mysql reported this error: $err</FONT></BODY></HTML>";
		exit;
	}

	/*
	 * My take on sorting an multidimensional array by a specified key
	 *
	 * Added: 24May04
	 */
	function array_sort($array, $sortkey) {

		/*
		 * Counter to keep track of elements to sort
		 */
	   $i=0;

		/*
		 * Traverse through array getting the values needed
		 * to sort
		 */
		foreach ($array as $key => $value) {
      	$sortlist[$i] = $array[$key][$sortkey];
			$i++;
   	} 

		/*
		 * Now that we have all of the elements, sort the elements,
		 * then reset the array pointer
		 */
	   asort ($sortlist);
	   reset ($sortlist);

		/*
		 * Okay, now go through the sorted elements, and assign
		 * them in order in a new array
		 */
		foreach ($sortlist as $key => $value) {
   	      $sorted_array[] = $array[$key];
	   }
   	return $sorted_array;
	}

	/*
	 * Resets the SESSION array and destroys it.
	 *
	 * Added: 14-February-2004
	 */
	function admKillSession() {
		$_SESSION = array();
		session_destroy();
	}

	/*
	 * Spits out some formatted HTML with an error message and description.
	 * This also destroys the current session as a safety precaution.
	 *
	 * Added: 14-February-2004
	 */
	function admShowError($errmsg, $errdetail, $errpgtitle) {
		echo "<HTML>\r\n<HEAD>\r\n<TITLE>" . $errpgtitle . "</TITLE>\r\n";
		echo "<META NAME=\"Author\" CONTENT=\"danomac\">\r\n";
		echo "<LINK REL=\"stylesheet\" HREF=\"admin.css\" TYPE=\"text/css\" TITLE=\"Default\">\r\n</HEAD>\r\n\r\n";
		echo "<BODY CLASS=\"adm_error\">\r\n<P CLASS=\"adm_error\">".$errmsg."</P>\r\n";
		echo $errdetail;
		echo "\r\n<BR><BR><CENTER>Session terminated. You will need to go to the <A HREF=\"index.php\">login screen</A> to try again.</CENTER>";
		echo "\r\n</BODY>\r\n</HTML>";
		admKillSession();
	}

	/*
	 * Checks to see if a user is logged in and that their IP matches
	 * the ip the session started with.
	 * 
	 * Returns TRUE if they are logged in; FALSE otherwise.
	 *
	 * Added: 14-February-2004
	 */
	function admIsLoggedIn($clientIP) {
		if (!isset($_SESSION['authenticated']))
			return false;
		if ($_SESSION['authenticated'] != true)
			return false;
		if ($_SESSION['clientIP'] != $clientIP)
			return false;
		return true;
	}

	/*
	 * This shows a message, and redirects to an URL if needed
	 *
	 * Added: 14-February-2004
	 */
	function admShowMsg($msg, $detail, $pgtitle, $redirect = false, $redirURL = "", $redirWaitTime = 0) {
		echo "<HTML>\r\n<HEAD>\r\n<TITLE>$pgtitle</TITLE>\r\n";
		echo "<META NAME=\"Author\" CONTENT=\"danomac\">\r\n";

		if ($redirect)
			echo "<META http-equiv=\"refresh\" content=\"$redirWaitTime;URL=$redirURL\">";

		echo "<LINK REL=\"stylesheet\" HREF=\"admin.css\" TYPE=\"text/css\" TITLE=\"Default\">\r\n</HEAD>\r\n\r\n";
		echo "<BODY>\r\n<P CLASS=\"adm_title\">$msg</P><CENTER>$detail</CENTER>";
		if ($redirect) {
			echo "<BR<BR><CENTER>If the browser doesn't automatically redirect <A HREF=\"$redirURL\">click here.</A></CENTER>\r\n</BODY>\r\n</HTML>";
			exit;
		}
	}

	/*
	 * PHP function used to check the MD5 hash sent by JavaScript.
	 *
	 * Added: 14-February-2004
	 */
	function hmac_md5($key, $data) {
		if (extension_loaded("mhash"))
			return bin2hex(mhash(MHASH_MD5, $data, $key));
	
		// RFC 2104 HMAC implementation for php. Hacked by Lance Rushing
		$b = 64; // byte length for md5
		if (strlen($key) > $b)
			$key = pack("H*", md5($key));
		$key = str_pad($key, $b, chr(0x00));
		$ipad = str_pad("", $b, chr(0x36));
		$opad = str_pad("", $b, chr(0x5c));
		$k_ipad = $key ^ $ipad ;
		$k_opad = $key ^ $opad;
	
		return md5($k_opad . pack("H*", md5($k_ipad . $data)));
	}

	/*
	 * Function to spit out the administrative function URLs, and login information
	 * All this does is spit out <TR> headings, it expects to be in a TABLE structure already.
	 *
	 * Added: 16Mar04
	 */
	function admShowURL_Login($ip) {
		echo "<TR>\r\n\t<TD CLASS=\"data\" ALIGN=\"center\" COLSPAN=15><BR><BR><HR><B>Administrative functions</B></TD>\r\n</TR>\r\n";

		echo "<TR>\r\n";

		if ($_SESSION["admin_perms"]["add"] || $_SESSION["admin_perms"]["root"])
			echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\"><A HREF=\"bta_add.php\">Add new torrent</A></TD>\r\n";
		else
			echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\">&nbsp;</TD>\r\n";

		if ($_SESSION["admin_perms"]["retiredmgmt"] || $_SESSION["admin_perms"]["root"])
			echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\"><A HREF=\"bta_retired.php\">View/revive/delete Retired torrents</A></TD>\r\n";
		else
			echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\">&nbsp;</TD>\r\n";

		echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\"><A HREF=\"bta_consistency.php\" TARGET=\"_blank\">Manual database consistency check</A></TD>\r\n</TR>\r\n";

		echo "<TR>\r\n";
		if ($_SESSION["admin_perms"]["ipban"] || $_SESSION["admin_perms"]["root"])
			echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\"><A HREF=\"bta_banlist.php\">IP Banning</A></TD>\r\n";
		else
			echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\">&nbsp;</TD>\r\n";

		if ($_SESSION["admin_perms"]["usermgmt"] || $_SESSION["admin_perms"]["root"])
			echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\"><A HREF=\"bta_usermgmt.php\">User management</A></TD>\r\n";
		else
			echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\">&nbsp;</TD>\r\n";

		if ($_SESSION["admin_perms"]["advsort"] || $_SESSION["admin_perms"]["root"] && !$GLOBALS["dynamic_torrents"])
			echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\"><A HREF=\"bta_advsort.php\">Advanced Sorting</A></TD>\r\n</TR>\r\n";
		else
			echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\">&nbsp;</TD>\r\n</TR>\r\n";

		echo "<TR>\r\n";
		echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\">&nbsp;</TD>\r\n";
		echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\"><A HREF=\"bta_logout.php\">Log off</A></TD>\r\n";
		echo "\t<TD CLASS=\"data\" WIDTH=\"25%\" ALIGN=\"center\">&nbsp</TD>\r\n</TR>\r\n";

		echo "<TR>\r\n\t<TD CLASS=\"data\" ALIGN=\"center\" COLSPAN=15><HR><B>Login Information</B><BR><BR>\r\n";

		/*
		 * Display information on the client.
		 */
		echo "\t\tYou are logging in from IP <B>"; 
		echo $ip . "</B><BR>Using <B>" . $_SERVER['HTTP_USER_AGENT'] . "</B> as a web client.";
		echo "<BR><B><FONT SIZE=-1>This information has been logged.</FONT></B><HR>\r\n"; 

		echo "\t</TD>\r\n</TR>\r\n";
	}

	/*
	 * Function to check to see if a date is valid. Return TRUE if OK, FALSE otherwise.
	 *
	 * Added: 18Mar04
	 */
	function isDate($dateInput, $allowNullEmpty=false) {
		/*
		 * If null or empty strings are allowed, and it IS null or empty, return true
		 */
		if ($allowNullEmpty)
			if (is_null($dateInput) || strlen($dateInput) == 0)
				return true;

		if (strpos($dateInput, " ") !== false) {
			return false;
		}

		$dateArray = explode("-", $dateInput);
		if (count($dateArray) != 3)
			return false;
		if (!is_numeric($dateArray[0]) || !is_numeric($dateArray[1]) || !is_numeric($dateArray[2]))
			return false;
		return checkdate($dateArray[1], $dateArray[2], $dateArray[0]);
	}
	
	/*
	 * This will renumber the torrents for the category specified by decreasing each value by 1 for all records greater than the position
	 * specified. Useful for removing a hole in the sort  mechanism. You need to specify the category and grouping level (group level 0 
	 * exists for ALL categories!) and optionally the position in the sort list. Returns false on failure, true for success.
	 *
	 * Added 06Aug05
	 */
	function advSortDelete($category = "main", $grouping = 0, $position = 0) {
		/*
		 * The easiest way to do this is to tell mysql to do it.
		 */
		$rstSortList = @mysql_query("UPDATE `namemap` SET `sorting` = `sorting` - 1 
									WHERE `category` = \"$category\" AND `grouping` = $grouping AND `sorting` > $position");
						
		if ($rstSortList === false)
			return false;

		return true;
	}

	/*
	 * This will renumber the torrents for the category specified by decreasing each value by 1 for all records greater than the position
	 * specified. Useful for removing a hole in the sort  mechanism. You need to specify the category and grouping level (group level 0 
	 * exists for ALL categories!) and optionally the position in the sort list. Returns false on failure, true for success.
	 *
	 * Added 06Aug05
	 */	
	function advSortInsert($category = "main", $grouping = 0, $from = 0) {
		/*
		 * The easiest way to do this is grab all the torrents in that category and group, and order by the sort
		 * field. Also, we will specify name sorting at the end, just in case the sorting is messed up badly and
		 * we can't order items properly. There really isn't a fast way of doing this....
		 */
		 
		$rstSortList = @mysql_query("UPDATE `namemap` SET `sorting` = `sorting` + 1 
									WHERE `category` = \"$category\" AND `grouping` = $grouping AND `sorting` >= $position");

		if ($rstSortList === false)
			return false;

		return true;
	}
	
	/*
	 * Resorts a specified category and group, by filename.
	 *
	 * Returns false if the resort fails; true otherwise.
	 *
	 * Added 06Aug05
	 */
	function advSortResortGrp($category = "main", $grouping = 0) {
		/*
		 * Grab all torrents in the group, and run through them and give a new order
		 */
		$rstSortList = @mysql_query("SELECT `info_hash` 
						FROM `namemap` 
						WHERE `category` = \"$category\" AND `grouping` = $grouping
						ORDER BY `filename`") or die(mysql_error());

		
		if ($rstSortList === false)
			return false;

		$counter = 1;
		while ($row = mysql_fetch_row($rstSortList)) {
			@mysql_query("UPDATE `namemap` SET `sorting` = $counter WHERE `info_hash` = \"$row[0]\"");
			$counter++;
		}
		
		return true;
	}
	
	/*
	 * Resorts all groupings in a specified category
	 *
	 * Added 06Aug05
	 */
	function advSortResortCategory($category = "main") {
		/*
		 * First, grab all groups that have the category
		 */
		$rstGroupList = @mysql_query("SELECT `group_id` from `subgrouping` WHERE `category` = \"$category\"");
		
		if ($rstGroupList === false)
			return false;
			
		/*
		 * Sort ungrouped torrents first
		 */
		advSortResortGrp($category, 0);
			
		/*
		 * Sort the remaining groups
		 */
		while ($rowGroup = mysql_fetch_row($rstGroupList)) {
			if (!advSortResortGrp($category, $rowGroup[0]))
				return false;
				
		}
		
		return true;
	}
	
	/*
	 * Resorts everything. Can reset a mess if needed.
	 *
	 * Added 06Aug05
	 */
	function advSortResortAll() {
		/*
		 * Grab all categories active on the tracker...
		 */
		$rstCategoryList = @mysql_query("SELECT DISTINCT `category` FROM `namemap`");
		
		if ($rstCategoryList === false)
			return false;
		
		/*
		 * Run through each category, resorting everything
		 */
		while ($rowCategory = mysql_fetch_row($rstCategoryList)) {
			if (!advSortResortCategory($rowCategory[0]))
				return false;
		}
		
		return true;
	}
	
	/*
	 * Resorts group names for a specified category
	 */
	function advSortResortGroupNames($category = "main") {
		/*
		 * First, grab all groups that have the category
		 */
		$rstGroupList = @mysql_query("SELECT `group_id` from `subgrouping` WHERE `category` = \"$category\" ORDER BY `heading`") or die(mysql_error());

		if ($rstGroupList === false)
			return false;

		$counter=1;
		/*
		 * Sort the remaining groups
		 */
		while ($rowGroup = mysql_fetch_row($rstGroupList)) {
			@mysql_query("UPDATE `subgrouping` SET `groupsort`=$counter WHERE `group_id` = $rowGroup[0]");
			$counter++;
		}
		
		return true;		
	}
	
	/*
	 * Resorts all group names for all categories
	 */
	function advSortResortAllGroupNames() {
		/*
		 * Grab all categories active on the tracker...
		 */
		$rstCategoryList = @mysql_query("SELECT DISTINCT `category` FROM `namemap`");
		
		if ($rstCategoryList === false)
			return false;
		
		/*
		 * Run through each category, resorting everything
		 */
		while ($rowCategory = mysql_fetch_row($rstCategoryList)) {
			if (!advSortResortGroupNames($rowCategory[0]))
				return false;
		}
		
		return true;	
	}

	function decodePeerID($peerid) {
		$decoded_peer_id = pack("H*", $peerid);
		global $client_name;

		/*
		 * Check for the mainline client
		 */
		$match_array = array();
		$regexp_code = preg_match("/^(M)([0-9]-[0-9]{1,2}-[0-9])-/i", $decoded_peer_id, $match_array);
		if ($regexp_code !== false && $regexp_code !== 0) {
			if (array_key_exists($match_array[1], $client_name)) {
				$fullversion = $client_name[$match_array[1]] . " " . $match_array[2];
			} else {
				$fullversion = "Unknown: " . $match_array[1] . " " . $match_array[2];
			}
			$results = array('pattern' => $match_array[0], 'client' => $match_array[1], 'version' => $match_array[2], 'full' => $fullversion, 'raw' => $decoded_peer_id);
			return $results;
		} 

		/*
		 * Check for Azureus-style encoding
		 */
		$match_array = array();
		$regexp_code = preg_match("/^-([A-Z]{2})([0-9]{4})-/i", $decoded_peer_id, $match_array);
		if ($regexp_code !== false && $regexp_code !== 0) {
			if (array_key_exists($match_array[1], $client_name)) {
				$fullversion = $client_name[$match_array[1]] . " " . $match_array[2];
			} else {
				$fullversion = "Unknown: " . $match_array[1] . " " . $match_array[2];
			}
			$results = array('pattern' => $match_array[0], 'client' => $match_array[1], 'version' => $match_array[2], 'full' => $fullversion, 'raw' => $decoded_peer_id);
			return $results;
		} 

		/*
		 * Check for Shadow's style encoding
		 */
		$match_array = array();
		$regexp_code = preg_match("/^([A-Z])([0-9]{3})----/i", $decoded_peer_id, $match_array);
		if ($regexp_code !== false && $regexp_code !== 0) {
			if (array_key_exists($match_array[1], $client_name)) {
				$fullversion = $client_name[$match_array[1]] . " " . $match_array[2];
			} else {
				$fullversion = "Unknown: " . $match_array[1] . " " . $match_array[2];
			}
			$results = array('pattern' => $match_array[0], 'client' => $match_array[1], 'version' => $match_array[2], 'full' => $fullversion, 'raw' => $decoded_peer_id);
			return $results;
		} 

		/*
		 * Check for Bits on Wheels
		 */
		$match_array = array();
		$regexp_code = preg_match("/^-([A-Z]{4})([0-9A-Z]{2})-/i", $decoded_peer_id, $match_array);
		if ($regexp_code !== false && $regexp_code !== 0) {
			if (array_key_exists($match_array[1], $client_name)) {
				$fullversion = $client_name[$match_array[1]] . " " . $match_array[2];
			} else {
				$fullversion = "Unknown: " . $match_array[1] . " " . $match_array[2];
			}
			$results = array('pattern' => $match_array[0], 'client' => $match_array[1], 'version' => $match_array[2], 'full' => $fullversion, 'raw' => $decoded_peer_id);
			return $results;
		} 

		/*
		 * Check for Opera
		 */
		$match_array = array();
		$regexp_code = preg_match("/^([A-Z]{2})([0-9]{4})/i", $decoded_peer_id, $match_array);
		if ($regexp_code !== false && $regexp_code !== 0) {
			if (array_key_exists($match_array[1], $client_name)) {
				$fullversion = $client_name[$match_array[1]] . " " . $match_array[2];
			} else {
				$fullversion = "Unknown: " . $match_array[1] . " " . $match_array[2];
			}
			$results = array('pattern' => $match_array[0], 'client' => $match_array[1], 'version' => $match_array[2], 'full' => $fullversion, 'raw' => $decoded_peer_id);
			return $results;
		} 

		/*
		 * Check for XBT Client
		 */
		$match_array = array();
		$regexp_code = preg_match("/^([A-Z]{3})([0-9]{3}[d-])-/i", $decoded_peer_id, $match_array);
		if ($regexp_code !== false && $regexp_code !== 0) {
			if (array_key_exists($match_array[1], $client_name)) {
				$fullversion = $client_name[$match_array[1]] . " " . $match_array[2];
			} else {
				$fullversion = "Unknown: " . $match_array[1] . " " . $match_array[2];
			}
			$results = array('pattern' => $match_array[0], 'client' => $match_array[1], 'version' => $match_array[2], 'full' => $fullversion, 'raw' => $decoded_peer_id);
			return $results;
		} 

		/*
		 * Check for BitLord clients
		 */
		$pos = strpos($decoded_peer_id, "LORD");
		if ($pos !== false && $pos == 6) {
			$idpos = strpos($decoded_peer_id, "exbc");
			if ($idpos !== false && $idpos == 0) {
				// Yep, it's bitlord
				$client = "exbcLORD";
				$versionmajor = hexdec(bin2hex(substr($decoded_peer_id, 4, 1)));
				$versionminor = hexdec(bin2hex(substr($decoded_peer_id, 5, 1)));
				$fullversion = $client_name[$client] . " " . $versionmajor .".". $versionminor;
				$pattern = substr($decoded_peer_id, 0, 12);

				$results = array('pattern' => $pattern, 'client' => $client, 'version' => $version, 'full' => $fullversion, 'raw' => $decoded_peer_id);
				return $results;
			}
		}

		/*
		 * Check for old BitComet clients
		 */
		$pos = strpos($decoded_peer_id, "exbc");
		$pos2 = strpos($decoded_peer_id, "FUTB");
		if (($pos !== false && $pos == 0) || ($pos2 !== false && $pos2 == 0)) {
			$client = substr($decoded_peer_id, 0, 4);
			$versionmajor = hexdec(bin2hex(substr($decoded_peer_id, 4, 2)));
			$versionminor = hexdec(bin2hex(substr($decoded_peer_id, 6, 2)));
			$fullversion = $client_name[$client] . " " . $versionmajor .".". $versionminor;
			$pattern = substr($decoded_peer_id, 0, 12);

			$results = array('pattern' => $pattern, 'client' => $client, 'version' => $version, 'full' => $fullversion, 'raw' => $decoded_peer_id);
			return $results;
		}

		$results = array('pattern' => $decoded_peer_id, 'full' => "Unknown client", 'raw' => $decoded_peer_id);
		return $results;
	}
?>
