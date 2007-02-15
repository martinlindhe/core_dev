<?php

	/*
	 * Module:	help_seeder_summary.php
	 * Description: This module displays help for the seeder summary.
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
	<?php echo "<TITLE>Seeder summary help - $phpbttracker_id $phpbttracker_ver</TITLE>\r\n"; ?>
</HEAD>
<BODY CLASS="help">
	<P CLASS="help_title"><?php echo "$phpbttracker_id $phpbttracker_ver - Seeder summary help"; ?></P>
	
	The seeder summary shows you details on the people seeding for you, on a specific torrent. This page does
	not require you to be logged in to the administrative interface to view it, however you need to specify the
	info_hash value of the torrent to be displayed.<BR><BR>

	Most of the information is fairly straight forward. It shows:
	<ul>
	<li>the IP address of who is seeding</li>
	<li>the port that they are using</li>
	<li>how much they have contributed (note: not all clients report this properly...)</li>
	<li>when the last	time they checked in to the tracker</li>
	<li>and the client ID string of the client they are using.</li>
	</ul>
</BODY>
</HTML>