<?php
$time_start = microtime(true);

error_reporting(E_ALL);

$config['core']['fs_root'] = '/home/martin/dev/core_dev/';
$config['core']['web_root'] = '/core_dev/';
$config['core']['full_url'] = 'http://projects.localhost'.$config['core']['web_root'];

$config['app']['web_root'] = '/lyrics/';
$config['app']['full_url'] = 'http://projects.localhost'.$config['app']['web_root'];
$config['default_title'] = 'lyric database';

$config['language'] = 'en';


set_include_path($config['core']['fs_root'].'core/');
require_once('class.DB_MySQLi.php');
require_once('class.Auth_Standard.php');
require_once('class.Session.php');
require_once('functions_core.php');
restore_include_path();

require_once('functions_bands.php');
require_once('functions_records.php');
require_once('functions_lyrics.php');

$config['debug'] = true;

$config['database']['username']	= 'root';
$config['database']['password']	= '';
$config['database']['database']	= 'marti32_lyric';
$db = new DB_MySQLi($config['database']);

$config['session']['timeout'] = (60*30)-1;		//stay logged in for 30 minutes - FIXME om detta ändras så måste det stämme med js-timeouten (som är 30 min nu med iofs..)
$config['session']['name'] = 'hcLyrics';
$config['session']['error_page'] = 'index.php';
$session = new Session($config['session']);

$config['auth']['sha1_key'] = 'kekjhbkjsxfgyuejewjkx276786ddjhnhdzzz9716t6z';
$config['auth']['allow_login'] = true;
$config['auth']['allow_registration'] = false;
$config['auth']['userdata'] = false;
$auth = new Auth_Standard($config['auth']);
?>
