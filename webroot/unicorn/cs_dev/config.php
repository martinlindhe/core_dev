<?
	//cs_dev config
	$time_start = microtime(true);

	error_reporting(E_ALL);
	$time_start = microtime(true);
	$config['debug'] = true;

	$config['core_root'] = '/home/martin/dev/webroot/core_dev/';
	//$config['core_root'] = 'E:/devel/webroot/core_dev/';
	$config['core_web_root'] = '/core_dev/';						//the webpath to root level of core files (css, js, gfx directories)

	$config['web_root'] = '/unicorn/cs_dev/';						//the webpath to the root level of the project
	$config['default_title'] = 'CitySurf.tv - Nu kör vi!';			//default title for pages if no title is specified for that page

	$config['start_page'] = 'start.php';	//logged in start page

	set_include_path($config['core_root'].'core/');
	require_once('class.DB_MySQLi.php');
	require_once('class.Files.php');
	require_once('functions_textformat.php');

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

	//##################
	//cs includes start
	//##################
define('UPLA', '_input/');
define('UPLL', '.'.UPLA);

define('DESIGN', '_design/');

define('PD', '02');
define('UIMG', '150x150');
define('MAXIMUM_USERS', 750);
#standard title of page
define('DEFAULT_USER', '48d40b8b5dee4c06cd8864be1b35456d');
define('NAME_TITLE', 'CitySurf.tv - Nu kör vi!');
$NAME_TITLE = NAME_TITLE;

//define('SMTP_SERVER', 'localhost');
define('P2B', 'http://www.citysurf.tv/');
define('URL', 'citysurf.tv');
define('NAME_URL', 'CitySurf');
define("UO", '30 MINUTES');
define('ADMIN_NAME', 'CitySurf');
define('USER_GALLERY', '_input/usergallery/');
define('USER_IMG', '_input/images/');
define('USER_FIMG', 'user/image/');
define('NEWS', '/_output/news_');
$sex = array('M' => 'm', 'F' => 'k');
$sex_name = array('M' => 'man', 'F' => 'kvinna');

define("STATSTR", "listar <b>%1\$d</b> - <b>%2\$d</b> (totalt: <b>%3\$d</b>)");

	require_once('include/mail.fnc.php');
	require_once('include/gb.fnc.php');
	require_once('include/relations.fnc.php');
	require_once('include/main.fnc.php');
	require_once('include/spy.fnc.php');
	require_once('include/search_users.fnc.php');
	require_once('include/settings.fnc.php');
	require_once('include/secure.fnc.php');
	require_once('include/cut.fnc.php');
	require_once('include/abuse.fnc.php');
	require_once('include/validate.fnc.php');

	require_once('include/user.class.php');	//user() class
	require_once('include/auth.class.php');	//auth() class

	$user = new user();
	//######################
	//end of cs includes
	//######################
	
/*
define('CH', ' SQL_CACHE ');
define('SQL_U', 'cs_user');
define('SQL_P', 'cs8x8x9ozoSSpp');
define('SQL_D', 'cs_platform');
define('SQL_H', 'pc3.icn.se');
*/

	$config['database']['username']	= 'root';
	$config['database']['password']	= 'dravel';
	$config['database']['database']	= 'cs_dev';
	$config['database']['host']	= 'localhost';
	$db = new DB_MySQLi($config['database']);

	$config['files']['upload_dir'] = '/home/martin/dev/webroot/unicorn/cs_dev/uploads/';
	$config['files']['thumbs_dir'] = '/home/martin/dev/webroot/unicorn/cs_dev/uploads/thumbs/';
	$files = new Files($config['files']);
?>
