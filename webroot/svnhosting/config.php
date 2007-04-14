<?
	$time_start = microtime(true);

	error_reporting(E_ALL);
	mb_internal_encoding('UTF-8');
	date_default_timezone_set('Europe/Stockholm');

	$config['core_root'] = '../';
	require_once($config['core_root'].'functions/class.DB_MySQLi.php');
	require_once($config['core_root'].'functions/class.Session.php');
	require_once($config['core_root'].'functions/class.Files.php');

	require_once($config['core_root'].'functions/functions_general.php');
	require_once($config['core_root'].'functions/functions_textformat.php');
	require_once($config['core_root'].'functions/functions_wiki.php');

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbSvnHosting';
	$config['database']['debug']		= true;
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = 30*60;		//in seconds
	$config['session']['name'] = 'svnID';
	$config['session']['sha1_key'] = 'svnHost778zuu2xzuyYYe';
	$config['session']['allow_registration'] = false;
	$config['session']['home_page'] = 'index.php';
	$session = new Session($config['session']);

	$config['wiki']['allow_html'] = true;

?>