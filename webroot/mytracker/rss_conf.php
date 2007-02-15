<?php
	/* Tracker RSS Configuration - rss_conf.php
	 *
	 * This file provides configuration information for
	 * the RSS component of the tracker. 
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
	 * Set this to true to enable RSS caching.	 
	 */
	$enable_rss_cache = false;
	
	/*
	 * Paths needed to locate RSS-related items.
	 * Set $path_to_rss_script to point to the directory where rss.php is located. If in root, leave string empty.
	 * Set $path_to_rss_cache to point to where the output directory is for the caching (a filesystem path!!!).
	 */
	$path_to_rss_script = '..';
	$path_to_rss_cache = '/rss';
	
	
	/*
	 * RSS Output XML options. These values affect how the RSS is output.
	 */
	$rss_report_limit = 30;
	$rss_xml_title = "tracker RSS";
	$rss_xml_link = "http://mytracker.org/";
	$rss_xml_desc = "This is a bittorrent RSS feed. Please set the refresh at a reasonable rate.";
	$rss_xml_lang = "en-us";
	
	/*
	 * This is an index of friendlier names for the categories that exist on the tracker.
	 * If the category doesn't exist in this array the group name will be shown.
	 *
	 * Duplicate these as needed.
	 */
	$rss_heading["Main"] = "Torrents";
?>
