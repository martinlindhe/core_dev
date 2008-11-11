<?php

error_reporting(E_ALL);
$time_start = microtime(true);
$config['debug'] = true;

$config['core']['fs_root'] = '/home/ml/dev/core_dev/';
$config['core']['web_root'] = '/core_dev/';

$config['app']['web_root'] = '/issues/';
$config['default_title'] = 'issue tracker';

set_include_path($config['core']['fs_root'].'core/');
require_once('class.DB_MySQLi.php');
require_once('class.Session.php');
require_once('class.Auth_Standard.php');
require_once('class.Files.php');
require_once('functions_core.php');
require_once('functions_wiki.php');
require_once('functions_news.php');
require_once('functions_todo.php');
restore_include_path();

$config['database']['username']	= 'root';
$config['database']['password']	= '';
$config['database']['database']	= 'dbIssues';
$db = new DB_MySQLi($config['database']);

$config['session']['timeout'] = (60*60)*24*7;		//keep logged in for 7 days
$config['session']['name'] = 'issueID';
$session = new Session($config['session']);

$config['auth']['sha1_key'] = 'c9787cdghgyY#YTsadffJK(7698JJj933fa!s';
$config['auth']['allow_login'] = true;
$config['auth']['allow_registration'] = true;
$auth = new Auth_Standard($config['auth']);

$config['files']['upload_dir'] = '/home/ml/dev/issues-uploads/';
$config['files']['thumbs_dir'] = $config['files']['upload_dir'].'thumbs/';
$files = new Files($config['files']);

$config['wiki']['allow_html'] = true;
$config['wiki']['allow_files'] = true;

?>
