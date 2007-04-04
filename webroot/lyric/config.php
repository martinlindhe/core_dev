<?
	$time_start = microtime(true);

	error_reporting(E_ALL);
	mb_internal_encoding('UTF-8');
	date_default_timezone_set('Europe/Stockholm');

	require_once('../functions/class.DB_MySQLi.php');
	require_once('../functions/class.Session.php');

//	require_once('functions_db.php');
//	require_once('functions_session.php');
//	require_once('functions_user.php');

	require_once('functions_bands.php');
	require_once('functions_records.php');
	require_once('functions_lyrics.php');
	require_once('functions_moderation.php');

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbLyrics';
	$config['database']['debug']		= true;

	/* A variable named $db must exist for all future functions to work. */
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = 30*60;
	$config['session']['name'] = 'hcLyrics';
	$config['session']['sha1_key'] = 'kekjhbkjsxfgyuejewjkx276786ddjhnhdzzz9716t6z';
	$config['session']['allow_registration'] = false;

	/* A variable named $session must exist for all future functions to work. */
	$session = new Session($config['session']);
?>