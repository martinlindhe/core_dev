<?
	$time_start = microtime(true);

	error_reporting(E_ALL);
	date_default_timezone_set('Europe/Stockholm');

	$config['core_root'] = '../';
	require_once($config['core_root'].'core/class.DB_MySQLi.php');
	require_once($config['core_root'].'core/class.Session.php');
	require_once($config['core_root'].'core/class.Files.php');

	require_once('functions_process.php');

	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbProcess';
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = (60*60)*24;		//in seconds
	$config['session']['name'] = 'procId';
	$config['session']['sha1_key'] = 'x8xijemjshjkljhkjhs88t68kioxkijhkjsh';
	$config['session']['allow_registration'] = false;
	$config['session']['home_page'] = 'index.php';
	$session = new Session($config['session']);

	$config['files']['apc_uploads'] = false;
	$config['files']['upload_dir'] = 'D:/devel/webupload/process/';
	$config['files']['thumbs_dir'] = 'D:/devel/webupload/process/thumbs/';
	$files = new Files($config['files']);

	$config['site']['web_root'] = '/process/';	//path on web server, to use to address paths for css & js includes
?>