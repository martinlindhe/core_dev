<?
	$time_start = microtime(true);

	error_reporting(E_ALL);
	mb_internal_encoding('UTF-8');
	date_default_timezone_set('Europe/Stockholm');

	$config['core_root'] = '../';
	require_once($config['core_root'].'core/class.DB_MySQLi.php');
	require_once($config['core_root'].'core/class.Session.php');

	require_once('functions_bands.php');
	require_once('functions_records.php');
	require_once('functions_lyrics.php');
	require_once('functions_moderation.php');

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