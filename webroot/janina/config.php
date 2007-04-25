<?
	$time_start = microtime(true);

	error_reporting(E_ALL);
	mb_internal_encoding('UTF-8');
	date_default_timezone_set('Europe/Stockholm');

	$config['core_root'] = '../';
	require_once($config['core_root'].'core/class.DB_MySQLi.php');
	require_once($config['core_root'].'core/class.Session.php');
	require_once($config['core_root'].'core/class.Files.php');

	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbJanina';
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = (60*60)*24;
	$config['session']['name'] = 'Janina';
	$config['session']['sha1_key'] = 'janinaSHAxyxtybhge3bbexudud81cujnm11wbvwcvvw';
	$config['session']['allow_registration'] = false;
	$session = new Session($config['session']);
	
	$config['files']['apc_uploads'] = false;
	$config['files']['count_file_views'] = true;
	$config['files']['image_max_width'] = 800;
	$config['files']['image_max_height'] = 570;
	$config['files']['thumb_default_width'] = 70;
	$config['files']['thumb_default_height'] = 60;
	$config['files']['upload_dir'] = 'E:/Devel/webupload_janina/';
	$config['files']['thumbs_dir'] = 'E:/Devel/webupload_janina/thumbs/';
	$files = new Files($config['files']);
?>