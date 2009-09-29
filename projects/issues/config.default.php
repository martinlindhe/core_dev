<?php

error_reporting(E_ALL);
$time_start = microtime(true);

$config['debug'] = true;

$config['core']['fs_root'] = '/var/www/core_dev/';
$config['core']['web_root'] = '/issues/core_dev/';

set_include_path(get_include_path() . PATH_SEPARATOR . $config['core']['fs_root'].'core/');
require_once('handler.php');
require_once('class.Files.php');
require_once('functions_core.php');
require_once('functions_wiki.php');
require_once('functions_news.php');
require_once('functions_todo.php');

$config['app']['web_root'] = '/issues/';

$config['default_title'] = 'GIR - Issue Tracker';

$config['db']['driver']     = 'mysqli';
$config['db']['username']	= 'root';
$config['db']['password']	= '';
$config['db']['database']	= 'dbIssues';

$config['user']['driver'] = 'default';

$config['session']['driver']  = 'default';
$config['session']['timeout'] = (60*60)*24*7;		//keep logged in for 7 days
$config['session']['name']    = 'issueID';

$config['auth']['driver'] = 'default';
$config['auth']['sha1_key'] = 'c9787cdghgyY#YTsadffJK(7698JJj933fa!s';
$config['auth']['allow_login'] = true;
$config['auth']['allow_registration'] = true;

$h = new Handler($config);
$h->handleEvents();


$config['files']['upload_dir'] = '/home/ml/dev/issues-uploads/';
$config['files']['thumbs_dir'] = $config['files']['upload_dir'].'thumbs/';
$files = new Files($config['files']);

$config['wiki']['allow_html'] = true;
$config['wiki']['allow_files'] = true;

?>
