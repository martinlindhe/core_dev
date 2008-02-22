<?
	error_reporting(E_ALL);
	$time_start = microtime(true);
	$config['debug'] = true;

	$config['core_root'] = '/home/martin/dev/cs/www.phonecafe.se/core_dev/';	//use of an absolute path is highly recommended
	$config['core_web_root'] = '/core_dev/';						//the webpath to root level of core files (css, js, gfx directories)

	$config['web_root'] = '/core_dev/process/';						//the webpath to the root level of the project
	$config['default_title'] = 'process server project';					//default title for pages if no title is specified for that page

	set_include_path($config['core_root'].'core/');
	require_once('class.DB_MySQLi.php');
	require_once('class.Auth_Standard.php');
	require_once('class.Session.php');
	require_once('class.Files.php');
	require_once('functions_general.php');
	require_once('functions_wiki.php');
	require_once('functions_process.php');
	restore_include_path();

	//use same sample db
	$config['database']['username']	= 'root';
	$config['database']['password']	= 'dravel';
	$config['database']['database']	= 'dbSample';
	$config['database']['host']	= '10.10.10.240';
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = (60*60)*24*7;		//keep logged in for 7 days
	$config['session']['name'] = 'coreID';
	$config['session']['allow_themes'] = true;
	$session = new Session($config['session']);

	$config['auth']['sha1_key'] = 'sdcu7cw897cwhwihwiuh#zaixx7wsxh3hdzsddFDF4ex1g';
	$config['auth']['allow_login'] = true;
	$config['auth']['allow_registration'] = true;
	$auth = new Auth_Standard($config['auth']);

	$config['files']['apc_uploads'] = false;
	$config['files']['upload_dir'] = 'E:/devel/webupload/sample/';
	$config['files']['thumbs_dir'] = 'E:/devel/webupload/sample/thumbs/';
	$files = new Files($config['files']);

	$config['wiki']['allow_html'] = true;
	$config['wiki']['allow_files'] = true;
?>