<?php
	/* Non-cached RSS generator - rss.php
	 *
	 * This file provides realtime RSS XML output.
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

	/*
	 * Load required includes
	 */
	require_once("config.php");
	require_once("rss_conf.php");
	require_once("funcsv2.php");
	
	/*
	 * Check to see if a category was requested and if the request is valid. If not
	 * check for subcategories, otherwise show the whole feed.
	 */
	if (!$enable_rss_cache) {
		if (isset($_GET["category"]) && strlen($_GET["category"]) > 0 && strpos($_GET["category"], " ") === false) {	
			doRSS(true, true, $_GET["category"]);
		} elseif (isset($_GET["subcategory"]) && is_numeric($_GET["subcategory"])) {
			doRSS(true, true, "", false, $_GET["subcategory"]);
		} else{
			doRSS(true, true);	
		}
	}
?>
