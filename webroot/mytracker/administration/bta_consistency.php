<?php
	/*
	 * Module:	bta_consistency.php
	 * Description: This checks the database for consistency.
	 * 		It has been rewritten to operate in "silent" HTML
	 * 		mode, so it can be used elsewhere.
	 *
	 * Author:	danomac
	 * Written:	30-March-2004
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
	 * Required modules
	 */
	require_once("../funcsv2.php");
	require_once("../version.php");
	require_once("../config.php");
	require_once("bta_funcs.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<META NAME="Author" CONTENT="danomac">
<LINK REL="stylesheet" HREF="admin.css" TYPE="text/css" TITLE="Default">
<?php
	/*
	 * Set the page title.
	 */
	echo "<TITLE>". $adm_page_title . " - Database Consistency Check</TITLE>\r\n";
?>
</HEAD>
<BODY>
<TABLE CLASS="tblAdminOuter">
<TR>
<?php
	/*
	 * Display the page heading.
	 */
	echo "\t<TD CLASS=\"pgheading\" COLSPAN=15>".$adm_page_title."<BR>Database consistency Check</TD>\r\n";
?>
</TR>
<TR>
	<TD>&nbsp;</TD>
</TR>
<TR>
	<TD CLASS="data" COLSPAN=15 ALIGN="center"><BR>
	   <A HREF="help/help_consistency.php" TARGET="_blank">Need help?</A><BR>
	</TD>
</TR>
<TR>
	<TD CLASS="data">This checks the database and:<BR><BR>1. Removes stale clients,<BR>
	2. Makes sure the seeder/leecher count is accurate, and <BR>3. Resets the Average % Done, if needed.<BR>
	4. Checks to make sure the downloaded amount is not negative.<BR>
   5. Resets the speed on the torrent to zero if there are no leechers present.<BR><BR>
	You may want to have this automatically run every <i>x</i> hours.<BR><BR>
	</TD>
</TR>
<TR>
	<TD CLASS="data">
<?php

	/*
	 * Connect to the database
	 */
	if ($GLOBALS["persist"])
		$db = @mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	else
		$db = @mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	mysql_select_db($database) or die("Tracker error: can't open database $database - ".mysql_error());

	/*
	 * Do the check, outputting HTML
	 */
	consistencyCheck(true);
?>
	</TD>
</TR>
</TABLE>
</BODY>
</HTML>
