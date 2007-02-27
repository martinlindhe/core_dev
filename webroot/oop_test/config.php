<?
	$time_start = microtime(true);
	
/*
	//autoload seems to slow down page load ALOT (PHP 5.2.1, Apache 2.2.4 WinXP)
	function __autoload($class_name) {
  	require_once('../functions/class.'.$class_name.'.php');
	}
*/
//	require_once('../functions/class.DB_MySQL.php');
	require_once('../functions/class.DB_MySQLi.php');
	require_once('../functions/class.Session.php');

	$config['database']['username']	= 'root';
	$config['database']['password']	= '';
	$config['database']['database']	= 'dbOOPHP';
	$config['database']['debug']		= true;

	/* A variable named $db must exist for all future functions to work. */
	$db = new DB_MySQLi($config['database']);

	$config['session']['name'] = 'OOPtest';
	$config['session']['sha1_key'] = 'sitecode_uReply';		//todo: byt ut sitecode

	/* A variable named $session must exist for all future functions to work. */
	$session = new Session($config['session']);
?>