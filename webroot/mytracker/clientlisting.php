<?php
	/*
	 * clientlisting.php - a list of bittorrent clients supported by the
	 *   client filtering module
	 *
	 * author: danomac
	 * Date: 30.sept.04
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
	<META NAME="Author" CONTENT="danomac">
	<LINK REL="stylesheet" HREF="tracker.css" TYPE="text/css" TITLE="Default">
	<TITLE>BitTorrent client list</TITLE>
</HEAD>

<BODY>
	<H1>Bittorrent client list</H1>

	There are several clients for BitTorrent out there. Each possess at least the basic functionality
	of downloading torrents; they also have some unique features of their own. View the website for each
	client for more information. The clients in this list are known to work with this tracker.

	<CENTER>
	<TABLE BORDER=1>
	<TR>
		<TH ALIGN=LEFT>Client name</TH>
		<TH>Version</TH>
		<TH>Platforms</TH>
		<TH ALIGN=LEFT>Description</TH>
		<TH>Filtered<SUP>1</SUP></TH>
	</TR>
	<TR>
		<TD><A HREF="http://bittorrent.com/">BitTorrent</A> <FONT SIZE="-2">(Recommended)</FONT></TD>
		<TD ALIGN=CENTER>>=3.4.2</TD>
		<TD ALIGN=CENTER>linux, Windows, MAC OS X</TD>
		<TD>The original BitTorrent client</TD>
		<TD ALIGN=CENTER>Yes</TD>
	</TR>
	<TR>
		<TD><A HREF="http://bittornado.com/">BitTornado</A> <FONT SIZE="-2">(Recommended)</FONT></TD>
		<TD ALIGN=CENTER>&gt;=0.2.0</TD>
		<TD ALIGN=CENTER>linux, Windows, MAC OS X</TD>
		<TD>This client is one of the most popular clients.</TD>
		<TD ALIGN=CENTER>Yes</TD>
	</TR>
	<TR>
		<TD><A HREF="http://azureus.sourceforge.net/">Azureus</A> <FONT SIZE="-2">(Recommended)</FONT></TD>
		<TD ALIGN=CENTER>&gt;=2.0.8.4</TD>
		<TD ALIGN=CENTER>linux, Windows, MAC OS X</TD>
		<TD>Written in Java, can be used on multiple platforms.</TD>
		<TD ALIGN=CENTER>Yes</TD>
	</TR>
	<TR>
		<TD><A HREF="http://pingpong-abc.sourceforge.net/">ABC - Yet Another Bittorrent Client</A> <FONT SIZE="-2">(Recommended)</FONT></TD>
		<TD ALIGN=CENTER>&gt;=2.6.8</TD>
		<TD ALIGN=CENTER>linux, Windows</TD>
		<TD>Another popular client, based off of BitTornado.</TD>
		<TD ALIGN=CENTER>Yes</TD>
	</TR>
	<TR>
		<TD><A HREF="http://www.bytelinker.com/intl/">BitSpirit</A></TD>
		<TD ALIGN=CENTER>&gt;=2.6 Final</TD>
		<TD ALIGN=CENTER>Windows</TD>
		<TD>&nbsp;</TD>
		<TD ALIGN=CENTER>Yes</TD>
	</TR>
	<TR>
		<TD><S>SHAD0W's BitTorrent Client</S></TD>
		<TD ALIGN=CENTER><S>No version allowed</S></TD>
		<TD ALIGN=CENTER><S>linux, Windows, MAC OS X</S></TD>
		<TD>This client is now depracated. Please upgrade to BitTornado.</TD>
		<TD ALIGN=CENTER>Yes</TD>
	</TR>
	<TR>
		<TD><A HREF="http://sourceforge.net/projects/turbobt">TurboBT</A></TD>
		<TD ALIGN=CENTER>&gt;=5.0</TD>
		<TD ALIGN=CENTER>Windows</TD>
		<TD>&nbsp;</TD>
		<TD ALIGN=CENTER>Yes</TD>
	</TR>
	<TR>
		<TD><A HREF="http://libtorrent.rakshasa.no/">rTorrent</A></TD>
		<TD ALIGN=CENTER>&gt;=0.5.4</TD>
		<TD ALIGN=CENTER>Linux</TD>
		<TD>Written in C so it isn't dependent on Python.</TD>
		<TD ALIGN=CENTER>Yes</TD>
	</TR>
	</TABLE>
	<BR><SUP>1</SUP>: This tracker checks the version of this client.<BR><BR>If the client isn't listed here it may not be allowed on the tracker.
	</CENTER>
</BODY>
</HTML>
