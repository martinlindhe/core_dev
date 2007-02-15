<?php
	/*
	 * Module: rsscache.php - Provides caching for RSS and live feeds
	 *
	 * Written: 11-Sept-05
	 *
	 * Copyright (C)2005 danomac.
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
	 *
	 */

	/*
	 * Required includes
	 */
	require_once("config.php");
	require_once("rss_conf.php");
	require_once("funcsv2.php");
	
	/*
	 * Only run when caching is enabled!
	 */
	if ($GLOBALS["enable_rss"] && $enable_rss_cache) {
		/*
		 * Logging constants: NONE is self explanatory, MINIMAL is basically start/stop/execution time of script,
		 *   VERBOSE is a detailed (debugging) log.
		 */
		define("EXTLOG_NONE", 0);
		define("EXTLOG_MINIMAL", 1);
		define("EXTLOG_VERBOSE", 2);

		$extlogfile = '/home/danomac/out.txt';
		$extlogtype = EXTLOG_NONE;

		/*
		 * Disable error reporting... 
		 */
		error_reporting(0);

		$script_starttime = time();
		if ($extlogtype >= EXTLOG_MINIMAL) error_log("-----------------------------------------------------\r\n" . date("Y-M-d/H:i:s (l)", $script_starttime) . " -- Script started\r\n", 3, $extlogfile);
		
		/*
		 * Connect to the database server
		 */
		if ($GLOBALS["persist"])
			$db = mysql_pconnect($dbhost, $dbuser, $dbpass);
		else
			$db = mysql_connect($dbhost, $dbuser, $dbpass);
		
		if ($db === false) {
			if ($extlogtype >= EXTLOG_MINIMAL) error_log("\tCan't connect to database: ".mysql_error()."\r\n", 3, $extlogfile);	
			exit;
		}

		/*
		 * Open the database
		 */
		if (mysql_select_db($database) === false) {
			if ($extlogtype >= EXTLOG_MINIMAL) error_log("\tCan't open database: ".mysql_error()."\r\n", 3, $extlogfile);	
			exit;
		}
	
		/*
		 * Get a list of categories to process...
		 */
		$rstCategories = mysql_query("SELECT DISTINCT `category` FROM `namemap`");
		if ($rstCategories === false) {
			if ($extlogtype >= EXTLOG_MINIMAL) error_log("\tCan't get list of categories: ".mysql_error()."\r\n", 3, $extlogfile);	
			exit;
		}

		/*
		 * Get a list of subcategories to process...
		 */
		$rstSubCategories = mysql_query("SELECT `group_id` FROM `subgrouping`");
		if ($rstSubCategories === false) {
			if ($extlogtype >= EXTLOG_MINIMAL) error_log("\tCan't get list of subcategories: ".mysql_error()."\r\n", 3, $extlogfile);	
			exit;
		}
		
		/*
		 * Check to see if we have write access to the directory...
		 */
		$rssfile = $path_to_rss_cache . "/index.xml";
		$fpRSS = fopen($rssfile, "wb");
		if ($fpRSS === false) {
			if ($extlogtype >= EXTLOG_MINIMAL) error_log("\tFailed to open directory ". $_SERVER["DOCUMENT_ROOT"] ."$path_to_rss_cache/index.xml for writing! Are permissions correct?\r\n", 3, $extlogfile);
			exit;
		}

		if ($extlogtype >= EXTLOG_MINIMAL) error_log("\tFile opened, starting to process rss...\r\n", 3, $extlogfile);
		if ($extlogtype >= EXTLOG_VERBOSE) error_log("\tWriting index.xml rss info to file\r\n", 3, $extlogfile);
		doRSS(false, false, "all", $fpRSS);
		fclose($fpRSS);

		/*
		 * Process the individual groups
		 */
		while ($recordRSS = mysql_fetch_row($rstCategories)) {
			if ($extlogtype >= EXTLOG_VERBOSE) error_log("\tWriting $recordRSS[0].xml rss info to file\r\n", 3, $extlogfile);
			$rssfile = $path_to_rss_cache . "/" . $recordRSS[0] . ".xml";
			$fpRSS = fopen($rssfile, "wb");
			if ($fpRSS === false) {
				if ($extlogtype >= EXTLOG_MINIMAL) error_log("\tFailed to open file ". $_SERVER["DOCUMENT_ROOT"] ."$path_to_rss_cache/$recordRSS[0].xml for writing! Are permissions correct?\r\n", 3, $extlogfile);
			}
			doRSS(false, false, $recordRSS[0], $fpRSS);
		}
		
		/*
		 * Process the subgroups
		 */
		while ($recordRSS = mysql_fetch_row($rstSubCategories)) {
			if ($extlogtype >= EXTLOG_VERBOSE) error_log("\tWriting subcategory $recordRSS[0].xml rss info to file\r\n", 3, $extlogfile);
			$rssfile = $path_to_rss_cache . "/" . $recordRSS[0] . ".xml";
			$fpRSS = fopen($rssfile, "wb");
			if ($fpRSS === false) {
				if ($extlogtype >= EXTLOG_MINIMAL) error_log("\tFailed to open file ". $_SERVER["DOCUMENT_ROOT"] ."$path_to_rss_cache/$recordRSS[0].xml for writing! Are permissions correct?\r\n", 3, $extlogfile);
			}
			doRSS(false, false, false, $fpRSS, $recordRSS[0]);
		}
		
		$script_stoptime = time();
		$execution_time = $script_stoptime - $script_starttime;
		if ($extlogtype >= EXTLOG_MINIMAL) error_log(date("Y-M-d/H:i:s (l)", $script_stoptime) . " -- Script terminated normally, execution time was $execution_time second(s).\r\n", 3, $extlogfile);
		exit;
	}
?>
