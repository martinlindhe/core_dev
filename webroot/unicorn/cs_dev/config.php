<?
	//cs_dev config

	error_reporting(E_ALL);
	$time_start = microtime(true);
	$config['debug'] = true;

	//$config['core_root'] = '/home/martin/dev/webroot/core_dev/';
	$config['core_root'] = 'E:/devel/webroot/core_dev/';
	$config['core_web_root'] = '/core_dev/';						//the webpath to root level of core files (css, js, gfx directories)

	$config['web_root'] = '/unicorn/cs_dev/';						//the webpath to the root level of the project
	$config['default_title'] = 'CitySurf.tv - Nu kör vi!';			//default title for pages if no title is specified for that page

	$config['start_page'] = 'start.php';	//logged in start page

	set_include_path($config['core_root'].'core/');
	require_once('class.DB_MySQLi.php');
	require_once('class.Files.php');

/*
	//require_once('class.Session.php');

	require_once('functions_faq.php');
	require_once('functions_wiki.php');
	require_once('functions_news.php');
	require_once('functions_blogs.php');
	require_once('functions_guestbook.php');
	require_once('functions_contacts.php');
	require_once('functions_messages.php');
	require_once('functions_forum.php');
*/
	restore_include_path();

	//cs includes
	require_once('include/config.include.php');	//todo: ta bort denna fil

	require_once('include/mail.fnc.php');
	require_once('include/gb.fnc.php');
	require_once('include/relations.fnc.php');
	require_once('include/main.fnc.php');
	require_once('include/spy.fnc.php');
	require_once('include/user.class.php');	//user() class
	require_once('include/auth.class.php');	//auth() class

	$user = new user();
	//end of cs includes

/*
define('CH', ' SQL_CACHE ');
define('SQL_U', 'cs_user');
define('SQL_P', 'cs8x8x9ozoSSpp');
define('SQL_D', 'cs_platform');
define('SQL_H', 'pc3.icn.se');
*/

	$config['database']['username']	= 'root';
	//$config['database']['password']	= 'dravel';
	$config['database']['database']	= 'cs_dev';
	$config['database']['host']	= 'localhost';
	$db = new DB_MySQLi($config['database']);

	$config['files']['upload_dir'] = '/home/martin/dev/webroot/unicorn/cs_dev/uploads/';
	$config['files']['thumbs_dir'] = '/home/martin/dev/webroot/unicorn/cs_dev/uploads/thumbs/';
	$files = new Files($config['files']);
?>
