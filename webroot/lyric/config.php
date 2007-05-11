<?
	$time_start = microtime(true);

	error_reporting(E_ALL);
	date_default_timezone_set('Europe/Stockholm');

	$config['core_root'] = '../';
	set_include_path($config['core_root'].'core/');
	require_once('class.DB_MySQLi.php');
	require_once('class.Session.php');
	require_once('functions_general.php');
	restore_include_path();

	require_once('functions_bands.php');
	require_once('functions_records.php');
	require_once('functions_lyrics.php');

	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbLyrics';
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = (60*60)*24;
	$config['session']['name'] = 'hcLyrics';
	$config['session']['sha1_key'] = 'kekjhbkjsxfgyuejewjkx276786ddjhnhdzzz9716t6z';
	$config['session']['allow_registration'] = false;
	$config['session']['home_page'] = 'index.php';
	$session = new Session($config['session']);
?>