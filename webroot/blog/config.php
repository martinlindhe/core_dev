<?
	/* SITE CONFIGURATION START */
	
	/* Include used function files */
	include_once($config['path_functions'].'functions_db_mysql.php');
	include_once($config['path_functions'].'functions_debug.php');
	include_once($config['path_functions'].'functions_categories.php');
	include_once($config['path_functions'].'functions_blogs.php');	
	include_once($config['path_functions'].'functions_time.php');
	include_once($config['path_functions'].'functions_misc.php');
	
	$config['debug'] = true;
	$config['database_1']['server']   = 'localhost';
	$config['database_1']['port']     = 3306;
	$config['database_1']['username'] = 'root';
	$config['database_1']['password'] = '';
	$config['database_1']['database'] = 'dbBlog';
	$db = dbOpen($config['database_1']);
	
	$config['login_sha1_key'] = 'sitecode_AB';	//used to encode passwords in database, to make brute forcing them more difficult
	$config['session_name'] = 'trackerSessID';	//name of session-id cookie
	$config['session_timeout'] = 3600*4;	//4h session timeout

	$config['start_page'] = '/blog/index.php';
	
	$config['site_admin'] = 'info@agentinteractive.se';
	
	$config['url_rewrite_length'] = 90;		//max length of visible url's after rewrite to hyperlinks

	/* Set language and include used locales files */
	$config['language'] = 'sv';
	include_once($config['path_functions'].'locales_standard.php');
	include_once($config['path_functions'].'locales_time.php');
	
	$config['text'] = $config['text'][ $config['language'] ];

	/* SITE CONFIGURATION END */
?>