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
	restore_include_path();
	
	require_once('functions_chat.php');

	$config['debug'] = true;

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbAJAXChat';
	$db = new DB_MySQLi($config['database']);

	$config['session']['timeout'] = (60*60)*24*7;	//7 days
	$config['session']['name'] = 'ajaxChatID';
	$config['session']['sha1_key'] = 'ajaxchat8x8jdszfoklxcvuykFFaadvdfvzw434fg3f3';
	$config['session']['allow_registration'] = true;
	$config['session']['default_title'] = 'ajax chat project';
	$config['session']['web_root'] = '/ajax_chat/';
	$session = new Session($config['session']);

	//chat config options
	$config['chat']['max_text_length']	= 100;	//max number of characters allowed to input in a chat line
	$config['chat']['buffer_lines']			= 15;		//number of lines to read into the chat buffer for a chat channel
	$config['chat']['idle_timeout']			=	3;		//idle timeout, in seconds, too low value will cause lots of join/left spam in channel (10 or more recommended)

	$meta_js = array('js/formatDate.js', 'js/functions.js', 'js/ajax.js');

	$session->handleSessionActions();
?>