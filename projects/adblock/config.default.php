<?
	$time_start = microtime(true);

	error_reporting(E_ALL);

	$config['core']['fs_root'] = 'H:/devel/webroot/core_dev/';
	$config['core']['web_root'] = '/core_dev/';

	$config['app']['web_root'] = '/adblock/';
	$config['default_title'] = 'Adblock Filterset Database';

	set_include_path($config['core']['fs_root'].'core/');
	require_once('class.DB_MySQLi.php');
	require_once('class.Auth_Standard.php');
	require_once('class.Session.php');
	require_once('class.Files.php');
	require_once('functions_general.php');
	require_once('functions_wiki.php');
	require_once('functions_news.php');
	restore_include_path();

	require_once('functions_adblock.php');

	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'marti32_adblock';
	$config['database']['host'] = 'localhost';
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = (60*60)*24*7;	//7 days
	$config['session']['name'] = 'adblockID';
	$session = new Session($config['session']);

	$config['auth']['sha1_key'] = 'sjxkxEadBL0ckjdhyhhHHxnjklsdvyuhu434nzkkz18ju222ha';
	$config['auth']['allow_registration'] = false;
	$auth = new Auth_Standard($config['auth']);


	$config['files']['apc_uploads'] = false;
	$config['files']['upload_dir'] = 'E:/devel/webupload/adblock/';
	$config['files']['thumbs_dir'] = 'E:/devel/webupload/adblock/thumbs/';
	$files = new Files($config['files']);

	$config['wiki']['allow_html'] = true;
	$config['wiki']['allow_files'] = true;

	$config['news']['allow_rating'] = false;
	$config['news']['allow_polls'] = false;



	/*********************************
	* settings specific for the adblock-project
	*********************************/
	$config['adblock']['cachepath'] = 'cache/';
	$config['adblock']['cacheage'] = 	1; //3600/4;		//time before disk cache expires, in seconds

	define('COMMENT_ADBLOCKRULE',	20);
	define('FEEDBACK_ADBLOCK_ADS', 20);
	define('FEEDBACK_ADBLOCK_BROKEN_RULE', 21);
?>
