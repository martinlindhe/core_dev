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
	require_once($config['core_root'].'core/functions_news.php');
	require_once($config['core_root'].'core/functions_blogs.php');

	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbZine';
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = (60*60)*24;		//in seconds
	$config['session']['name'] = 'zineID';
	$config['session']['sha1_key'] = 'v987zxvc8biusxdhbo4wwnsnxmf098bvi09';
	$config['session']['allow_registration'] = true;
	$config['session']['home_page'] = 'index.php';
	$session = new Session($config['session']);

	$config['files']['apc_uploads'] = false;
	$config['files']['upload_dir'] = 'E:/devel/webupload/zine/';
	$config['files']['thumbs_dir'] = 'E:/devel/webupload/zine/thumbs/';
	$files = new Files($config['files']);

	$config['wiki']['allow_html'] = true;
	$config['wiki']['allow_files'] = true;
	
	$config['site']['web_root'] = '/zine/';	//path on web server, to use to address paths for css & js includes
?>