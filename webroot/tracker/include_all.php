<?
	$pageload_start = microtime(true);
	
	set_time_limit(60*10);

	error_reporting(E_ALL);
	mb_internal_encoding('UTF-8');
	date_default_timezone_set('Europe/Stockholm');

	/* Special configuration directives: These cannot be placed in config.php because it is read too late in execution flow */
	$config['site_admin'] = 'no@mail.com';	//embedded in RSS feeds in <webMaster> tag and as sender for emails
	$config['site_url']		= 'http://www.default.url/';	//utan avslutande /

	$config['path_functions'] = '../site_functions/';
	include_once('config.php');

	if ($config['debug']) include_once($config['path_functions'].'functions_debug.php');

	ContinueSession($db);
?>