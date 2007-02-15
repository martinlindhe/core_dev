<?
	/* SITE CONFIGURATION START */
	
	/* Include used function files */
	include_once($config['path_functions'].'functions_categories.php');
	include_once($config['path_functions'].'functions_settings.php');
	//include_once($config['path_functions'].'functions_comments.php');
	include_once($config['path_functions'].'functions_infofields.php');
	include_once($config['path_functions'].'functions_dictionary.php');
	include_once($config['path_functions'].'functions_misc.php');
	
	$config['debug'] = true;
	$config['database_1']['server']   = 'localhost';
	$config['database_1']['port']     = 3306;
	$config['database_1']['username'] = 'root';
	$config['database_1']['password'] = '';
	$config['database_1']['database'] = 'dbSvnHosting';
	$db = dbOpen($config['database_1']);
	
	$config['login_sha1_key'] = 'svnHost778zuu2xzuyYYe';	//used to encode passwords in database, to make brute forcing them more difficult
	$config['session_name'] = 'svnID';				//name of session-id cookie
	$config['session_timeout'] = 3600*4;			//4h session timeout

	$config['start_page'] = '/svnhosting/index.php';
	


	/* Set language and include used locales files */
	$config['language'] = 'en';
	include_once($config['path_functions'].'locales_standard.php');
	include_once($config['path_functions'].'locales_time.php');
	
		$config['text'] = $config['text'][ $config['language'] ];

	/* SITE CONFIGURATION END */
?>