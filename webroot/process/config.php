<?
	$time_start = microtime(true);

	error_reporting(E_ALL);
	date_default_timezone_set('Europe/Stockholm');

	$config['core_root'] = '';
	$config['core_web_root'] = '/_process/';

	$config['web_root'] = '/_process/';
	$config['default_title'] = 'process server';

	set_include_path($config['core_root'].'core/');
	require_once('class.DB_MySQLi.php');
	require_once('class.Session.php');
	require_once('class.Files.php');
	restore_include_path();

	require_once('functions_process.php');

	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= 'dravelsql';
	$config['database']['database']	= 'dbProcess';
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = (60*60)*24*7;	//7 days
	$config['session']['name'] = 'procId';
	$config['session']['sha1_key'] = 'x8xijemjshjkljhkjhs88t68kioxkijhkjsh';
	$config['session']['allow_registration'] = false;
	$session = new Session($config['session']);

	$config['files']['apc_uploads'] = false;
	$config['files']['upload_dir'] = 'D:/devel/webupload/process/';
	$config['files']['thumbs_dir'] = 'D:/devel/webupload/process/thumbs/';
	$files = new Files($config['files']);

	$session->handleSessionActions();
?>