<?php
error_reporting(E_ALL);
$time_start = microtime(true);
$config['debug'] = true;

$config['core']['fs_root'] = '/var/www/core_dev/';
$config['core']['web_root'] = '/core_dev/';
$config['core']['full_url'] = 'http://process1.x'.$config['core']['web_root'];

$config['app']['web_root'] = '/';
$config['app']['full_url'] = 'http://process1.x'.$config['app']['web_root'];
$config['default_title'] = 'process server project';					//default title for pages if no title is specified for that page

$config['language'] = 'se';

set_include_path(get_include_path() . PATH_SEPARATOR . $config['core']['fs_root'].'core/');
require_once('core.php');
require_once('handler.php');
require_once('functions_wiki.php');
require_once('functions_process.php');


$config['db']['username']	= 'ml';
$config['db']['password']	= 'nutana';
$config['db']['database']	= 'dbProcess';
$config['db']['host']	= 'process1.x';
$config['db']['port']	= 44000;

$config['session']['timeout'] = (60*60)*24*7;		//keep logged in for 7 days
$config['session']['name'] = 'coreID';
$config['session']['allow_themes'] = true;

$config['auth']['sha1_key'] = 'sdcu7cw897cwhwihwiuh#zaixx7wsxh3hdzsddFDF4ex1g';
$config['auth']['allow_login'] = true;
$config['auth']['allow_registration'] = false;
$config['auth']['userdata'] = false;

$config['files']['apc_uploads'] = false;
$config['files']['upload_dir'] = '/home/ml/dev/process-uploads/';


$h = new handler();
$h->db('mysqli', $config['db']);
$h->user('default');
$h->auth('default', $config['auth']);
$h->session('default', $config['session']);
$h->files('default', $config['files']);

$h->handleEvents();

$config['wiki']['allow_html'] = true;
$config['wiki']['allow_files'] = true;
?>
