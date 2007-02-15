<?php
	/* Tracker Configuration - config.php
	 *
	 * This file provides configuration information for
	 * the tracker. The user-editable variables are at the top. It is
	 * recommended that you do not change the database settings
	 * unless you know what you are doing.
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
	 * Maximum reannounce interval
	 *
	 * The tracker expects the client to report before this time expires
	 * from the previous contact (in seconds.)
	 *
	 * Default: 1800 seconds.
	 */
	$GLOBALS["report_interval"] = 1800;

	/*
	 * Minimum reannounce interval (Optional)
	 *
	 * The tracker does not expect contact from the client until at least
	 * this much time has elapsed from the previous contact (in seconds.)
	 *
	 * Default: 300 seconds.
	 */
	$GLOBALS["min_interval"] = 300;

	/*
	 * Maximum Peers
	 *
	 * Maximum number of peers to send in one request.
	 *
	 * Default: 50.
	 */
	$GLOBALS["maxpeers"] = 50;

	/*
	 * Require torrent authorization?
	 *
	 * If set to true, then the tracker will accept any and all
	 * torrents given to it, it will not need to be specifically
	 * added through the Administration interface.
	 *
	 * NOT RECOMMENDED, but available if you need it.
	 *
	 * Default: No anonymous torrents (false).
	 */
	$GLOBALS["dynamic_torrents"] = false;

	/*
	 * Check for NAT clients?
	 *
	 * If set to true, NAT checking will be performed.
	 * This may cause trouble with some providers!
	 * Also,  so it's
	 * off by default.
	 *
	 * Default: No checking (false).
	 */
	$GLOBALS["NAT"] = false;

	/*
	 * Use a persistent database connection?
	 *
	 * Check with your webmaster to see if you're allowed to use these.
	 *
	 * Recommended if the database server is not on the same machine as
	 * the web server software, or if there is a high load on the tracker.
	 *
	 * NOTE: If you share a server with someone else, this is not a good idea.
	 *
	 * Default: Normal connection (false).
	 */
	$GLOBALS["persist"] = false;

	/*
	 * Allow IP override?
	 *
	 * Allow users to override the IP address reported. Usually not needed,
	 * but if you seed torrents on the same intranet on the tracker, this
	 * needs to be enabled.
	 *
	 * Default: No (false).
	 */
	$GLOBALS["ip_override"] = false;

	/*
	 * Enable peer caching?
	 *
	 * Table caches!
	 * Lowers the load on all systems, but takes up more disk space.
	 * You win some, you lose some. But since the load is the big problem,
	 * grab this.
	 *
	 * Warning! Enable this BEFORE making torrents, or else run makecache.php
	 * immediately, or else you'll be in deep trouble. The tables will lose
	 * sync and the database will be in a somewhat "stale" state.
	 *
	 * Default: Enabled (true)
	 */
	$GLOBALS["peercaching"] = true;

	/*
	 * How often to refresh tracker speed data?
	 *
	 * The tracker will update speed data on each torrent based on this interval
	 * (in seconds.)
	 *
	 * For heavily loaded trackers, set $GLOBALS["countbytes"] to false. It will  
	 * stop counting the number of downloaded bytes and the calculation of the 
	 * speed of the torrent; but it will significantly reduce the load.
	 *
	 * Default: spdrefresh: 60 seconds.
	 *          countbytes: true.
	 */
	$GLOBALS["spdrefresh"] = 60;
	$GLOBALS["countbytes"] = true;

	/*
	 * How often to refresh torrent average progress?
	 *
	 * The tracker will update average torrent progress based on this interval
	 * (in seconds.) To disable altogether set $GLOBALS["doavg"] to false. 
	 * Disabling will reduce the load on the tracker.
	 *
	 * Default: avgrefresh: 100 seconds.
	 *          doavg:      true.
	 */
	$GLOBALS["avgrefresh"] = 100;
	$GLOBALS["doavg"] = true;

	/*
	 * Optimize for heavy loads?
	 *
	 * Setting this to true will disable:
	 *   -torrent average progress
	 *   -amount transferred on torrent
	 *   -torrent speed
	 *
	 * It will also enable (very slight) benefits in tracker.php.
	 *
	 * Default: normally loaded trackers (false)
	 */
	$GLOBALS["heavyload"] = false;

	/*
	 * Only allow clients that support the compact protocol to connect?
	 *
	 * This offers bandwidth savings, but be aware that peer caching
	 * HAS to be ENABLED for this to work.
	 *
	 * Default: allow all clients (false)
	 */
	$GLOBALS["compactonly"] = false;

	/*
	 * Check client versions when they connect?
	 *
	 * Set to true to check to see which BT clients the leechers are using.
	 * If the version is not recognized, it isn't allowed access.
	 *
	 * This can add a significant amount of processing overhead if a lot of
	 * clients are connected to the tracker.
	 *
	 * Default: No filtering (false).
	 */
	$GLOBALS["filter_clients"] = false;

	/*
	 * Scrape interval
	 *
	 * This is the minimum time for clients to wait before requesting another
	 * scrape output from the tracker (in seconds). Maximum allowed is one hour 
	 * (3600 seconds).
	 *
	 * NOTE: Not all clients respect this.
	 *
	 * Default: 1/2 hour (1800 seconds.)
	 */
	$GLOBALS["scrape_min_interval"] = 1800;

	/*
	 * Multi-user mode?
	 *
	 * Allow users specified in the User Management section to access the 
	 * Administration interface. See README for more details on this.
	 *
	 * Even with this disabled, the 'root' user can still access the Administration
	 * interface.
	 *
	 * Default: No (false).
	 */
	$GLOBALS["allow_group_admin"] = false;

	/*
	 * Automatic statistic consistency check?
	 *
	 * Set to true to run the consistency check if a client requests scrape output
	 * and the script detects the statistics are not accurate. If there are no
	 * clients on a torrent, this obviously won't do anything!
	 *
	 * NOTE: This can be processor-intensive!
	 *
	 * Default: No (false).
	 */
	$GLOBALS["auto_db_check_scrape"] = false;

	/*
	 * Allow IP Banning?
	 *
	 * Set to true to check the client's ip address when connecting
	 * and refusing a connection if they are banned.
	 *
	 * Default: No (false).
	 */
	$GLOBALS["enable_ip_banning"] = false;

	/*
	 * Allow Automatic IP Banning?
	 *
	 * If client filtering is enabled, and this is enabled, will temporary ban
	 * a client that is very old or refusing to identify itself.
	 * 
	 * autobanlength is how long IP is temporarily banned when the tracker 
	 * automatically bans a client, in days
	 *
	 * Default: allow_unidentified_clients: No automatic banning (true).
	 *          autobanlength: 3 days.
	 */
	$GLOBALS["allow_unidentified_clients"] = true;
	$GLOBALS["autobanlength"] = 3;

	/*
	 * Allow torrent uploading?
	 *
	 * This tracker can upload the torrent automatically to the webserver, 
	 * provided the apache process has write access to your torrent folder. 
	 * Ideally, having ssh access is recommended in case you need to change the 
	 * owner of the files. 
	 * IF YOU DON'T KNOW WHAT ssh IS, DON'T TRY USING THIS.
	 *
	 * -Set 'allow_torrent_move' to true to copy the torrent to the specified 
	 *  folder.
	 * -Set 'torrent_folder' to the folder that you wish to use, but remember that
	 *  the apache processes need to have write access to this folder. This is
	 *  relative to the root of your webserver (ie. setting to 'torrents' will
	 *  use http://mysite.com/torrents as the destination.)
	 * -Set 'max_torrent_size' to the maximum torrent size you want to allow (in 
	 *  bytes).
	 * -Set 'move_to_db' to true to move the torrent into the database. If false,
	 *  it will be placed in the folder specified on the server.
	 *
	 * Default: No (false); max torrent size of 100000 bytes.
	 */ 
	$GLOBALS["allow_torrent_move"] = false;
	$GLOBALS["move_to_db"] = false;
	$GLOBALS["max_torrent_size"] = 100000;
	$GLOBALS["torrent_folder"] = "torrents";

	/*
	 * Allow /scrape requests?
	 *
	 * Set this to false to report an error if a client tries to use the
	 * scrape output. NOTE: If you make torrents without the trailing
	 * '/announce' in the tracker URL you don't need to use this. If you
	 * expect a high load torrent, you can use this to disable it temporarily.
	 *
	 * Default: Yes (true).	
	 */
	$GLOBALS["allow_scrape"] = true;

	/*
	 * Allow extra stats to be reported in /scrape data?
	 *
	 * Set this to true to report extra stats in scrape output.
	 * This consists of the torrent name, average progress, speed,
	 * and amount transferred.
	 *
	 * This uses extra bandwidth.
	 *
	 * Default: No (false).
	 */
	$GLOBALS["scrape_extras"] = false;

	/*
	 * Allow the scrape scanning script to be used?
	 * 
	 * Set this to true to allow scrape_scan.php to function.
	 *
	 * Default: No (false).
	 */
	$GLOBALS["scrape_scanning"] = false;

	/*
	 * Set this to what your announce URL will be. An example would be
	 * 'http://myweb.org/tracker/tracker.php/announce'. The administration
	 * interface uses this to check if torrents are yours so it MUST be set.
	 */
	$GLOBALS["my_tracker_announce"] = "";

	/*
	 * Allow external torrents to be added to this tracker?
	 *
	 * This tracker supports showing stats for external torrents by using
	 * the external torrents' /scrape output. Note, in order for this to work,
	 * the external tracker's announce url HAS TO END with '/announce'.
	 * PHP needs to have it's allow_url_fopen ENABLED for this to work, or
	 * ext_scan.php will fail to open the remote streams. See your php.ini file
	 * and/or http://www.php.net/manual/en/ref.filesystem.php#ini.allow-url-fopen
	 * for details.
	 *
	 * With that out of the way, set 'allow_external_scanning' to true to allow
	 * you to add an external torrent.
	 *
	 * Set 'auto_add_external_torrents' to true to assume that if it is an
	 * external torrent, just to add it. If this is false there will be an option
	 * to add the torrent in admin as an external reference IF APPLICABLE (users 
	 * that aren't allowed to add external torrents will not see this option).
	 * This requires the 'my_tracker_announce' to be set above. 
	 *
	 * Set the 'external_refresh' to the amount of minutes before contacting
	 * external sites. DO NOT SET THIS to a really low interval or you will find 
	 * yourself being banned from tracker sites.
	 * 
	 * The 'external_refresh_tolerance' should be left at its default (5).
	 *
	 * Set the 'ext_batch_scrape' to false if you want the script to use the
	 * info_hash parameter when querying external trackers, otherwise it will
	 * request all the scrape data and parse through it.
	 *
	 * YOU NEED TO ADD external.php TO YOUR CRONTAB for every 15 minutes or so for
	 * this to work. This script will use these setting here and contact the 
	 * external sites only when needed; it won't contact each site at the interval
	 * you set in crontab.
	 *
	 * Defaults: allow_external_scanning = false
	 *           auto_add_external_torrents = false
	 *           external_refresh = 30
	 *           ext_batch_scrape = false
	 */
	$GLOBALS["allow_external_scanning"] = false;
	$GLOBALS["auto_add_external_torrents"] = false;
	$GLOBALS["external_refresh"] = 30;
	$GLOBALS["external_refresh_tolerance"] = 5;
	$GLOBALS["ext_batch_scrape"] = false;

	/*
	 * Enable RSS system? Set to true if you want to use the builtin RSS support.
	 * Configuration items for RSS are in rss_conf.php.
	 *
	 * Default: false
	 */
	$GLOBALS["enable_rss"] = false;
	
	/*
	 * If your website is part of a webserver farm, you aren't guaranteed to get the
	 * same server when you use sessions. In this case, set 
	 * $GLOBALS["webserver_farm"] to true and set 
	 * $GLOBALS["webserver_farm_session_path"] to a path that ALL webservers have
	 * write access to (don't use a trailing slash on the path.)
	 *
	 * Default: false
	 */
	$GLOBALS["webserver_farm"] = false;
	$GLOBALS["webserver_farm_session_path"] = "/tmp";

	/*
	 * Administration root username/password.
	 * YOU HAVE TO SET THIS; THE ADMINISTRATION SYSTEM STAYS DISABLED UNTIL THIS
	 * IS SET! It doesn't hurt to use a strong username/password combination...
	 */
	$admin_user="";
	$admin_pass="";

	/*
	 * Database settings - These are used to connect to the database 
	 *
	 * Don't change these unless you know what you are doing.
	 */
	$dbhost = "";  $dbuser = "";  $dbpass = "";  $database = "";
?>
