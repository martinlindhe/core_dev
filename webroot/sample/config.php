<?
	$time_start = microtime(true);

	error_reporting(E_ALL);
	date_default_timezone_set('Europe/Stockholm');

	$config['core_root'] = '../';
	set_include_path($config['core_root'].'core/');
	require_once('class.DB_MySQLi.php');
	require_once('class.Session.php');
	require_once('class.Files.php');
	require_once('functions_wiki.php');
	require_once('functions_news.php');
	require_once('functions_blogs.php');
	require_once('functions_guestbook.php');
	require_once('functions_contacts.php');
	restore_include_path();

	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbSample';
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = (60*60)*24*7;		//keep logged in for 7 days
	$config['session']['name'] = 'coreID';
	$config['session']['sha1_key'] = 'sdcu7cw897cwhwihwiuh#zaixx7wsxh3hdzsddFDF4ex1g';
	$config['session']['allow_registration'] = true;
	$config['session']['web_root'] = '/sample/';
	$config['session']['default_title'] = 'sample project';
	$config['session']['allow_themes'] = true;
	$session = new Session($config['session']);

	$config['files']['apc_uploads'] = false;
	$config['files']['upload_dir'] = 'E:/devel/webupload/sample/';
	$config['files']['thumbs_dir'] = 'E:/devel/webupload/sample/thumbs/';
	$files = new Files($config['files']);

	$config['wiki']['allow_html'] = true;
	$config['wiki']['allow_files'] = true;

	/* Visas på alla olika sidor som hör till ens egen användarprofil */
	$param = '';
	$username = $session->username;
	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$param = '?id='.$_GET['id'];
		$username = getUserName($_GET['id']);
	}
	$profile_menu = array(
	'user.php'.$param => 'Overview:'.$username,
	'files.php'.$param => 'Files',
	'blog.php'.$param => 'Blogs',
	'guestbook.php'.$param => 'Guestbook ('.getGuestbookUnreadCount($session->id).')',	//shows number of unread guestbook messages
	'friends.php'.$param => 'Friends',
	'settings.php'.$param => 'Settings');

	$session->handleSessionActions();
?>