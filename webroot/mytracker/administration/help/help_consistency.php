<?php

	/*
	 * Module:	help_consistency.php
	 * Description: This module displays help on the database consistency screen.
	 *
	 * Author:	danomac
	 * Written:	1-April-2004
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
	 * List of the external modules required
	 */
	require_once ("../../config.php");
	require_once ("../../version.php");
	require_once ("../bta_funcs.php");
?>
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<LINK REL="stylesheet" HREF="../admin.css" TYPE="text/css" TITLE="Default">
	<?php echo "<TITLE>Tracker consistency help - $phpbttracker_id $phpbttracker_ver</TITLE>\r\n"; ?>
</HEAD>
<BODY CLASS="help">
	<P CLASS="help_title"><?php echo "$phpbttracker_id $phpbttracker_ver - Tracker consistency help"; ?></P>
	
	The purpose of the tracker consistency page is to correct displayed statistics if they are incorrect. It also
	removes peers that have not reported to the tracker in a reasonable amount of time.<BR><BR>

	Please note that this script <B>DOES NOT</B> require you to be logged in to the administrative interface.<BR><BR>

	Although this tracker can correct incorrect stats if clients use the /scrape output, this may not be the best
	way to keep the stats correct. The /scrape method adds overhead to the tracker responses (in execution time).
	If you have a lot of peers on the tracker this will limit how many in total you can have online at any time. 
	The better solution would be for the tracker owner to set up crontab to run this file every 3 hours or so. (Keep
	in mind that you should not use the webserver for this. Use the php executable.) A sample crontab entry that would
	repeat every three hours is similar to:<BR><BR>
	<DIV CLASS="helpexample">
	* */3 * * * * php /path/to/bta_consistency.php
	</DIV><BR>
	Here is a bit of an explanation of what is displayed, by each column.<BR><BR>
	<DIV CLASS="helpexample">
	<B>Name/Info Hash</B>: This is the name of the torrent, or simply the info hash value.<BR>
	<B>Size</B>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This is the size of the torrent.<BR>
	<B>UL</B>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This is the amount of seeders on the torrent. The background will change to black if it is corrected.<BR>
	<B>DL</B>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This is the amount of leechers on the torrent. The background will change to black if it is corrected.<BR>
	<B>XFER</B>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This is the amount of transfer over the life of the torrent. The background will change to black if it is corrected.<BR>
	<B>Speed</B>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This is the current speed of the torrent. The background will change to black if it is corrected.<BR>
	<B>Avg % Done</B>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Amongst all the peers, the is the average progress on the torrent. The background will change to black if it is corrected.<BR>
	<B>Stale Clients</B>:&nbsp;&nbsp;If clients have not reported in a long time and are removed, it's indicated how many were removed here.<BR>
	<B>Peer Cache</B>:&nbsp;&nbsp;If the peer cache was changed by this script it is indicated here.<BR>
	</DIV>
</BODY>
</HTML>