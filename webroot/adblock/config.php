<?
	/* SITE CONFIGURATION START */

	/* Include used function files */
	include_once($config['path_functions'].'functions_time.php');
	include_once($config['path_functions'].'functions_geoip.php');
	include_once($config['path_functions'].'functions_infofields.php');
	include_once($config['path_functions'].'functions_comments.php');
	include_once($config['path_functions'].'functions_settings.php');
	include_once($config['path_functions'].'functions_adblock.php');
	include_once($config['path_functions'].'functions_files.php');		//for sendTextFile() in download.php
	include_once($config['path_functions'].'functions_misc.php');
	
	$config['debug'] = true;		//if true, alot more events will be logEntry'ed
	$config['database_1']['server']   = 'localhost';
	$config['database_1']['port']     = 3306;
	$config['database_1']['username'] = 'root';
	$config['database_1']['password'] = '';
	$config['database_1']['database'] = 'dbAdblock';
	$db = dbOpen($config['database_1']);

	$config['adblock']['cachepath'] = 'cache/';

	/* User and session configuration */
	$config['session_name'] = 'AIsessID';	//name of session-id cookie
	$config['session_timeout'] = 3600*4;		//4h idle = automatically logged out

	/* The start page of the site. Unauthorized requests etc redirects to here. */
	$config['start_page'] = '/adblock/';
	
	$config['infofield']['allow_html'] = true;


	include_once($config['path_functions'].'locales_standard.php');	
	$config['language'] = 'en';
	$config['text'] = $config['text'][ $config['language'] ];

	/* SITE CONFIGURATION END */
?>