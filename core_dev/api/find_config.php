<?php

/**
 * $Id$
 */

if (!empty($_GET['pr'])) {
	if (strpbrk($_GET['pr'], '"\'/\\%&?;:.,')) die;				//checks _pr for " ' \ / % & ? ; : . ,
	$project_name = preg_replace("/[^\w\.-]+/", '_', $_GET['pr']); //removes dangerous filesystem letters
	if ($project_name != $_GET['pr']) die;	//invalid chars in path
	$project = '../../'.$project_name.'/';
	if (!is_file($project.'config.php')) $project = '../'.$project_name.'/';
} else {
	$project = '../';	//Defaults to a config.php in the directory below this one
	if (!is_file($project.'config.php')) $project = '../../';
}

if (!is_file($project.'config.php')) {
	if (!is_file($_SERVER['DOCUMENT_ROOT'].'config.php')) {
		die('cant find config path from '.$_SERVER['SCRIPT_FILENAME']);
	}
	$project = $_SERVER['DOCUMENT_ROOT'];
}

$config['no_session'] = true;	//force session handling to be skipped to disallow automatic requests from keeping a user "logged in"
require_once($project.'config.php');
?>
