<?
	$time_start = microtime(true);

	error_reporting(E_ALL);
	date_default_timezone_set('Europe/Stockholm');

	$config['core_root'] = 'E:/devel/webroot/core_dev/';	//use of an absolute path is highly recommended
	$config['core_web_root'] = '/core_dev/';							//the webpath to root level of core files (css, js, gfx directories)

	$config['web_root'] = '/sample/';											//the webpath to the root level of the project
	$config['default_title'] = 'sample project';					//default title for pages if no title is specified for that page

	set_include_path($config['core_root'].'core/');
	//require_once('class.DB_MySQL.php');
	require_once('class.DB_MySQLi.php');
	//require_once('class.DB_PostgreSQL.php');
	require_once('class.Session.php');
	require_once('class.Files.php');
	require_once('functions_faq.php');
	require_once('functions_feedback.php');
	require_once('functions_wiki.php');
	require_once('functions_news.php');
	require_once('functions_blogs.php');
	require_once('functions_guestbook.php');
	require_once('functions_contacts.php');
	require_once('functions_messages.php');
	restore_include_path();

	$config['debug'] = true;
	
	$config['plugins'] = array('wurfl');
	loadPlugins();

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbSample';
	$db = new DB_MySQLi($config['database']);
/*
	$config['database']['username']	= 'postgres';
	$config['database']['password']	= 'test';
	$config['database']['database']	= 'dbSample';
	$db = new DB_PostgreSQL($config['database']);
*/

	$config['session']['timeout'] = (60*60)*24*7;		//keep logged in for 7 days
	$config['session']['name'] = 'coreID';
	$config['session']['sha1_key'] = 'sdcu7cw897cwhwihwiuh#zaixx7wsxh3hdzsddFDF4ex1g';
	$config['session']['allow_login'] = true;
	$config['session']['allow_registration'] = true;
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
		$profile_menu = array(
			'user.php'.$param => 'Overview:'.$username,
			'files.php'.$param => 'Files',
			'user_blogs.php'.$param => 'Blogs',
			'guestbook.php'.$param => 'Guestbook',
			'messages.php'.$param => 'Message',
			'friends.php'.$param => 'Friends'
		);
	} else {
		$profile_menu = array(
			'user.php' => 'Overview:'.$username,
			'files.php' => 'Files',
			'messages.php' => 'Messages',
			'user_blogs.php' => 'Blogs',
			'guestbook.php' => 'Guestbook ('.getGuestbookUnreadCount($session->id).')',	//shows number of unread guestbook messages
			'friends.php' => 'Friends',
			'settings.php' => 'Settings',
			'user_visits.php' => 'Visitors'
		);
	}

	$user_menu = array(
		'users.php' => 'Users:Overview',
		'blogs.php' => 'Blogs',
		'search_users.php' => 'Search users',
		'last_logged_in.php' => 'Last logged in',
		'users_online.php' => 'Users online'
	);

	$session->handleSessionActions();
?>