<?php 
	/*
	 * Module:	bta_jslogin.php
	 * Description: This is the script that logs in to the Administrative interface
	 * 		using Javascript to encrypt the password field.
	 *
	 * Author:	danomac
	 * Written:	14-Febrary-2004
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
	 * Session webserver farm check
	 */
	require_once ("../config.php");

	if (isset($GLOBALS["webserver_farm"]) && isset($GLOBALS["webserver_farm_session_path"])) {
		if ($GLOBALS["webserver_farm"] && strlen($GLOBALS["webserver_farm_session_path"]) > 0) {
			session_save_path($GLOBALS["webserver_farm_session_path"]);
		}
	}
	session_start();
	header("Cache-control: private");

	/*
	 * There are some variables defined in these scripts that are needed.
	 */
	require_once("../version.php");
	require_once("bta_funcs.php");

	/*
	 * Let's try to stay HTML 4.01 compliant.
	 */
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\r\n";

	/*
	 * clean up the IP string and insert it in the session variable... used to try to prevent
	 * session theft later on
	 */
	$ip = str_replace("::ffff:", "", $_SERVER["REMOTE_ADDR"]);
	$_SESSION["clientIP"] = $ip;

	/*
	 * Make sure this page was referred from the correct source
	 */
	if (!isset($_SESSION['refering_page'])) {
		admShowError("You can't access this page directly. Use bta_login.php to login to the administrative interface.",
			     "If you were NOT automatically redirected to this page ensure you have Javascript installed and functional.",
			     $adm_pageerr_title);
		exit;
	} else {
		/*
		 * Let's check to see if the referring page is indeed correct
		 * This is rather redundant. I know.
		 */
		$refererArray = explode("/", $_SESSION['refering_page']);
		$refererCount = count($refererArray);

		if ($refererArray[$refererCount-1] != "index.php") {
			admShowError("You have to use admin/index.php to login to the administrative interface.",
				     "If you are trying to access this file from another page you may get this error. Use bta_login.php to login to the administrative interface.",
				     $adm_pageerr_title);
			exit;
		}

		//reset the refering page
		$_SESSION['refering_page'] = $_SERVER['PHP_SELF'];
	}

	/*
	 * If the admin username and password are not set, terminate
	 */
	if (!isset($admin_user) || !isset($admin_pass) || strlen($admin_user) == 0 || strlen($admin_pass) == 0) {
		admShowError("Administration root username and/or password not set",
			     "The administration system will not function until you set these in the configuration.",
			     $adm_pageerr_title);
		exit;
	}

	/*
	 * Check to see if this session is logged on already, if it is, go to the main page
	 */
	if (isset($_SESSION['authenticated'])) {
		if ($_SESSION['authenticated']) {
			$_SESSION['refering_page'] = "";
			admShowMsg("You are logged in already.",
				       "Redirecting to the main administration panel.",
				       $adm_page_title, true, "bta_main.php", 3);
		}
	}

	/*
	 * Output the HEAD tags needed.
	 */
	echo "<HTML>\r\n<HEAD>\r\n<META NAME=\"Author\" CONTENT=\"danomac\">\r\n";
	echo "<LINK REL=\"stylesheet\" HREF=\"admin.css\" TYPE=\"text/css\" TITLE=\"Default\">\r\n";
	echo "<TITLE>".$phpbttracker_id." ".$phpbttracker_ver." Administration Login</TITLE>\r\n</HEAD>\r\n\r\n<BODY>\r\n";

	/*
	 * Connect to the database
	 */
	if ($GLOBALS["persist"])
		$db = mysql_pconnect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");
	else
		$db = mysql_connect($dbhost, $dbuser, $dbpass) or die("<HTML><BODY><FONT COLOR=\"red\">Couldn't connect to database. Incorrect username/password?</FONT></BODY></HTML>");

	if (!mysql_select_db($database))
		echo mysql_error();
	else {
?>
<SCRIPT SRC="md5.js" TYPE="text/javascript"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript"><!--
function login(f) {
	//compute the hashes with the ID given from mysql
	f['usermd5'].value = hex_hmac_md5(f['id'].value, f['username'].value);
	f['passmd5'].value = hex_hmac_md5(f['id'].value, hex_md5(f['password'].value));

	//let's not pass anything to the server through the http protocol...
	//just the hashes are fine
	f['username'].value = '';
	f['password'].value = '';
	return true;
}
//-->
</SCRIPT>

<NOSCRIPT>
<CENTER><P CLASS="adm_title"><STRONG><FONT SIZE=5>JavaScript HAS to be enabled</P><P>JavaScript needs to be enabled for a (slightly) more secure form of login. 
If you use this script without JavaScript installed <FONT COLOR="red">YOUR DATABASE USERNAME AND PASSWORD WILL BE SENT TO THE SERVER IN CLEARTEXT.</FONT> 
If you are seeing this message, you do NOT have JavaScript and this is your ONLY warning!</FONT></STRONG></P>
<P><STRONG>Note: This Javascript method isn't perfect either. If a hacker really wants to get at the interface, without using SSL
it will not be difficult for them to do. However, this method uses MD5 hashes and sends that to the server instead of using straight cleartext
which is better than nothing. <FONT COLOR="red">The MD5 hashes are NOT encrypted on the way to the server.</FONT></STRONG></CENTER>
</NOSCRIPT>

<FORM ACTION="bta_login_check.php" METHOD="post" onSubmit="return login(this);">

<?php
	mysql_query("INSERT INTO logins SET id=NULL, used=0, ipaddr=\"$ip\"");
?>

<CENTER>
<TABLE CLASS="tblLogin">
<TR>
	<TD COLSPAN="2" ALIGN="center" CLASS="logintbl"><FONT SIZE="5"><STRONG>Tracker Administrative Login</STRONG></FONT></TD>
</TR>
<TR>
	<TD COLSPAN="2" ALIGN="center" CLASS="logintbl"><STRONG>Enter your username and password, and click the <I>Submit</I> button.</STRONG><HR></TD>
</TR>
<TR>
	<TD ALIGN="right" WIDTH="40%" CLASS="logintbl">Username:</TD>
	<TD ALIGN="left" CLASS="logintbl"><INPUT TYPE="text" NAME="username"></TD>
</TR>
<TR>
	<TD ALIGN="right" CLASS="logintbl">Password:</TD>
	<TD ALIGN="left" CLASS="logintbl"><INPUT TYPE="password" NAME="password"></TD>
</TR>
<TR>
	<TD ALIGN="center" COLSPAN="2" CLASS="logintbl"><INPUT TYPE="submit" VALUE="Login" CLASS="button">
	<INPUT TYPE=reset VALUE="Clear" CLASS="button"></TD>
</TR>

<TR>
<?php 
	echo "<TD COLSPAN=\"2\" ALIGN=\"center\"><BR>You are logging in from IP <B>"; 
	echo $ip . "</B><BR>Using <B>" . $_SERVER['HTTP_USER_AGENT'] . "</B> as a web client.";
	echo "<BR><B>This information has been logged.</B></TD>"; 
?>
</TR>
</TABLE>
</CENTER>

<INPUT TYPE="hidden" NAME="id" VALUE="<?php echo mysql_insert_id(); ?>">
<INPUT TYPE="hidden" NAME="passmd5" VALUE="">
<INPUT TYPE="hidden" NAME="usermd5" VALUE="">
</FORM>

<?php
}
?>

</BODY>
</HTML>