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

	require_once('functions_adblock.php');
	require_once('functions_comments.php');	//todo: gör en klass av detta

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbAdblock';
	$config['database']['debug']		= true;
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = 30*60;		//in seconds
	$config['session']['name'] = 'adblockID';
	$config['session']['sha1_key'] = 'sjxkxEadBL0ckjdhyhhHHxnjklsdvyuhu434nzkkz18ju222ha';
	$config['session']['allow_registration'] = false;
	$config['session']['home_page'] = 'index.php';
	$session = new Session($config['session']);

	$config['files']['anon_uploads'] = false;
	$config['files']['upload_dir'] = 'E:/devel/webroot/adblock/webupload/';
	$config['files']['thumbs_dir'] = 'E:/devel/webroot/adblock/webupload/thumbs/';
	$files = new Files($config['files']);

	$config['wiki']['allow_html'] = true;
	$config['wiki']['allow_files'] = true;


	//todo: används dessa: ?
	$config['start_page'] = 'index.php';	//todo: använd ['session']['home_page'] istället
	$config['adblock']['cachepath'] = 'cache/';
?>