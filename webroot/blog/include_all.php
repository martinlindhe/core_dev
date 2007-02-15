<?
	$pageload_start = microtime(true);

	error_reporting(E_ALL);
	mb_internal_encoding('UTF-8');
	ini_set('magic_quotes_gpc', '0');		//turn off GPC magic quotes, turned on on surftown :-( safe mode är på så det går inte...

	/* Special configuration directives: These cannot be placed in config.php because it is read too late in execution flow */
	$config['site_admin'] = 'info@agentinteractive.se';	//embedded in RSS feeds in <webMaster> tag and as sender for emails
	$config['site_url']		= 'http://www.default.url/';	//utan avslutande /

	$config['path_functions'] = '../site_functions/';
	
	//php 5 funktion, surftown kör php4 :-/
	//date_default_timezone_set('Europe/Stockholm');

	/* Include the essential function files functions files */
	include_once($config['path_functions'].'functions_log.php');
	include_once($config['path_functions'].'functions_user.php');
	include_once($config['path_functions'].'functions_geoip.php');
	include_once($config['path_functions'].'functions_session.php');
	
	include_once('config.php');

	if ($config['debug']) include_once($config['path_functions'].'functions_debug.php');

	ContinueSession($db);
?>