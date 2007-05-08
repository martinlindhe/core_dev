<?
	$time_start = microtime(true);

	error_reporting(E_ALL);
	mb_internal_encoding('UTF-8');
	date_default_timezone_set('Europe/Stockholm');

	$config['core_root'] = '../';
	require_once($config['core_root'].'core/class.DB_MySQLi.php');
	require_once($config['core_root'].'core/class.Session.php');
	require_once($config['core_root'].'core/class.Files.php');

	require_once($config['core_root'].'core/functions_wiki.php');

	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbSvnHosting';
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = 30*60;		//in seconds
	$config['session']['name'] = 'svnID';
	$config['session']['sha1_key'] = 'svnHost778zuu2xzuyYYe';
	$config['session']['allow_registration'] = false;
	$config['session']['home_page'] = 'index.php';
	$session = new Session($config['session']);

	$config['wiki']['allow_html'] = true;

?>