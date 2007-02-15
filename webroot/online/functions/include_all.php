<?
	include("functions_db.php");
	include("functions_user.php");
	include("functions_countries.php");
	include("functions_news.php");
	include("functions_cc.php");
	include("functions_todo.php");
	include("functions_serverinfo.php");
	include("functions_time.php");
	include("functions_newsletter.php");
	include("functions_contentcodes.php");
	include("functions_characters.php");
	include("functions_forums.php");

	/* Configuration - START */
	$config['database_1']['server']   = 'localhost';
	$config['database_1']['port']     = 14084;
	$config['database_1']['username'] = 'root';
	$config['database_1']['password'] = 'nutana88';
	$config['database_1']['database'] = 'online_site';
	
	define("SUPPORT_MAIL", "support@inthc.net");
	define("SUPPORT_MAIL_HTML", "<a href=\"mailto:".SUPPORT_MAIL."\">".SUPPORT_MAIL."</a>");
	define("LOGFILE_CONTENTCODES",	"g:/weblogs/contentcodes.log" );
	/* Configuration - END */


	$db = dbOpen($config['database_1']);
	if ($db === false) {
		header("Location: server_down.php"); die;
	}


	session_start();

	if (!isset($_SESSION["loggedIn"]) || !$_SESSION["loggedIn"]) {
		/* Initialize session variables */
		$_SESSION["loggedIn"]  = false;
		$_SESSION["superUser"] = false;
	}
	
	include("constants.php");
?>