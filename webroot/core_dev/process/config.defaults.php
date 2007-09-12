<?
	//xdebug_enable();
	error_reporting(E_ALL);
	$time_start = microtime(true);
	$config['debug'] = true;

	//$config['core_root'] = '/home/martin/dev/webroot/core_dev/';	//use of an absolute path is highly recommended
	$config['core_root'] = 'E:/devel/webroot/core_dev/';	//use of an absolute path is highly recommended
	$config['core_web_root'] = '/core_dev/';						//the webpath to root level of core files (css, js, gfx directories)

	$config['web_root'] = '/core_dev/process/';						//the webpath to the root level of the project
	$config['default_title'] = 'process server project';					//default title for pages if no title is specified for that page

	set_include_path($config['core_root'].'core/');
	require_once('class.DB_MySQLi.php');
	require_once('class.Session.php');
	require_once('class.Files.php');
	require_once('functions_wiki.php');
	restore_include_path();

	require_once('functions_process.php');

	//use same sample db
	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbSample';
	$db = new DB_MySQLi($config['database']);
/*
	$config['database']['username']	= 'postgres';
	$config['database']['password']	= 'test';
	$config['database']['database']	= 'dbSample';
	$db = new DB_PostgreSQL($config['database']);
*/

	$config['session']['timeout'] = (60*60)*24*7;		//keep logged in for 7 days
	$config['session']['name'] = 'coreID';
	$config['session']['sha1_key'] = 'sdcu7cw897cwhwihwiuh#zaixx7wsxh3hdzsddFDF4ex1g';
	$config['session']['allow_login'] = true;
	$config['session']['allow_registration'] = true;
	$config['session']['allow_themes'] = true;
	$session = new Session($config['session']);

	$config['files']['apc_uploads'] = false;
	$config['files']['upload_dir'] = 'E:/devel/webupload/sample/';
	$config['files']['thumbs_dir'] = 'E:/devel/webupload/sample/thumbs/';
	$files = new Files($config['files']);

	$config['wiki']['allow_html'] = true;
	$config['wiki']['allow_files'] = true;

	$session->handleSessionActions();
?>