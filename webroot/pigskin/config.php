<?
	$time_start = microtime(true);

	error_reporting(E_ALL);

	$config['core_root'] = '../core_dev/';
	$config['core_web_root'] = '/core_dev/';

	$config['web_root'] = '/pigskin/';
	$config['default_title'] = 'pigskin';

	set_include_path($config['core_root'].'core/');
	require_once('class.DB_MySQLi.php');
	require_once('class.Session.php');
	require_once('class.Files.php');
	restore_include_path();

	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbPigskin';
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = (60*60)*24*7;	//7 days
	$config['session']['name'] = 'Piggy';
	$config['session']['sha1_key'] = 'x89xoedfjlzkjsfgviuasvnavklnke5avnaoeviaeovoiav';
	$config['session']['allow_registration'] = true;
	$session = new Session($config['session']);

	$config['files']['apc_uploads'] = false;
	$config['files']['count_file_views'] = true;
	$config['files']['image_max_width'] = 800;
	$config['files']['image_max_height'] = 570;
	$config['files']['thumb_default_width'] = 70;
	$config['files']['thumb_default_height'] = 60;
	$config['files']['upload_dir'] = 'E:/Devel/webupload/pigskin/';
	$config['files']['thumbs_dir'] = 'E:/Devel/webupload/pigskin/thumbs/';
	$files = new Files($config['files']);

	$session->handleSessionActions();
?>