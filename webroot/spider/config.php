<?
	$time_start = microtime(true);

	error_reporting(E_ALL);
	date_default_timezone_set('Europe/Stockholm');

	$config['core_root'] = '../';
	set_include_path($config['core_root'].'core/');
	require_once('class.DB_MySQLi.php');
	require_once('class.Session.php');
	require_once('class.Files.php');
	require_once('functions_wiki.php');
	require_once('functions_news.php');
	restore_include_path();

	require_once('functions_spider.php');

	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbSpider';
	$db = new DB_MySQLi($config['database']);


	$config['session']['timeout'] = (60*60)*24;		//in seconds
	$config['session']['name'] = 'spiderID';
	$config['session']['sha1_key'] = 'CAXadshq4jJAJRsjrzXFTszdfsJRzrj66rua43y';
	$config['session']['allow_registration'] = false;
	$config['session']['web_root'] = '/spider/';
	$config['session']['default_title'] = 'spider project';
	$session = new Session($config['session']);

	$config['files']['apc_uploads'] = false;
	$config['files']['upload_dir'] = 'E:/devel/webupload/spider/';
	$config['files']['thumbs_dir'] = 'E:/devel/webupload/spider/thumbs/';
	$files = new Files($config['files']);

	$session->handleSessionActions();
?>