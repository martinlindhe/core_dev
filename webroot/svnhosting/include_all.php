<?
	$pageload_start = microtime(true);

	error_reporting(E_ALL);
	mb_internal_encoding('UTF-8');

	/* Special configuration directives: These cannot be placed in config.php because it is read too late in execution flow */
	$config['site_admin'] = 'no@mail.com';	//embedded in RSS feeds in <webMaster> tag and as sender for emails
	$config['site_url']		= 'http://www.default.url/';	//utan avslutande /

	$config['path_functions'] = '../site_functions/';
	date_default_timezone_set('Europe/Stockholm');

	/* Include the essential function files functions files */
	include_once($config['path_functions'].'functions_db.php');
	include_once($config['path_functions'].'functions_geoip.php');		//for IPv4_to_GeoIP() used in log functions
	include_once($config['path_functions'].'functions_log.php');
	include_once($config['path_functions'].'functions_user.php');
	include_once($config['path_functions'].'functions_session.php');

	include_once('config.php');

	if ($config['debug']) include_once($config['path_functions'].'functions_debug.php');

	ContinueSession($db);
?>