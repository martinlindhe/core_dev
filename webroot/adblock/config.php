<?
	$time_start = microtime(true);

	error_reporting(E_ALL);
	mb_internal_encoding('UTF-8');
	date_default_timezone_set('Europe/Stockholm');

	require_once('../functions/class.DB_MySQLi.php');
	require_once('../functions/class.Session.php');
	require_once('../functions/class.Files.php');

	include_once('../functions/functions_general.php');
	include_once('../functions/functions_textformat.php');
	include_once('../functions/functions_wiki.php');

	include_once('functions_adblock.php');
	include_once('functions_comments.php');	//todo: gör en klass av detta

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbAdblock';
	$config['database']['debug']		= true;
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = 3600*4;		//4h idle = automatically logged out
	$config['session']['name'] = 'adblockID';
	$config['session']['sha1_key'] = 'sjxkxEadBL0ckjdhyhhHHxnjklsdvyuhu434nzkkz18ju222ha';
	$config['session']['allow_registration'] = false;
	$config['session']['home_page'] = 'index.php';
	$session = new Session($config['session']);

	$config['wiki']['allow_html'] = true;


	//todo: används dessa: ?
	$config['start_page'] = 'index.php';	//todo: använd ['session']['home_page'] istället

	$config['files'] = array();
	$files = new Files($config['files']);

	$config['adblock']['cachepath'] = 'cache/';
?>