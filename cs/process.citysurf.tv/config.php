<?
	$time_start = microtime(true);

	error_reporting(E_ALL);

	$config['core_root'] = '/home/martin/process.citysurf.tv/core_dev/';	//use of absolute path is required for admin pages to function
	$config['core_web_root'] = '/core_dev/';

	$config['web_root'] = '/';
	$config['default_title'] = 'process server';
	
	set_include_path($config['core_root'].'core/');
	require_once('class.DB_MySQLi.php');
	require_once('class.Session.php');
	require_once('class.Files.php');
	restore_include_path();

	require_once('functions_process.php');

	$config['debug'] = true;

	$config['plugins'] = array('ipx');
	loadPlugins();

	$config['database']['username']	= 'root';
	$config['database']['password']	= 'dravelsql';
	$config['database']['database']	= 'dbProcess';
	$db = new DB_MySQLi($config['database']);

	$config['user_db']['host']	= 'pc3.icn.se';
	$config['user_db']['username']	= 'cs_user';
	$config['user_db']['password']	= 'cs8x8x9ozoSSpp';
	$config['user_db']['database']	= 'cs_platform';

	$config['session']['timeout'] = (60*60)*24*7;	//7 days
	$config['session']['name'] = 'procId';
	$config['session']['sha1_key'] = 'x8xijemjshjkljhkjhs88t68kioxkijhkjsh';
	$config['session']['allow_registration'] = false;
	$session = new Session($config['session']);

	$session->handleSessionActions();

	$allowed_ip = array(
		'127.0.0.1',
		'213.80.11.162',	//unicorn kontoret
		'212.37.28.102',	//NVOX gamla ip
		'213.115.96.248',	//NVOX nya ip
		'217.151.193.79',	//Ericsson IPX (ipx-pat.ipx.com)
		'217.151.193.80'	//Ericsson IPX (ipx-pat.ipx.com)
	);

	$config['sms']['originating_number'] = '72777';
	$config['sms']['auth_username'] = 'lwcg';
	$config['sms']['auth_password'] = '3koA4enpE';
?>
