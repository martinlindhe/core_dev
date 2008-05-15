<?php
/**
 * $Id$
 *
 * Functions assumed to always be available
 *
 * \disclaimer This file is a required component of core_dev
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

//Include required core_dev files:
require_once('functions_locale.php');	//for translations
require_once('functions_xhtml.php');	//for XHTML output helper functions
require_once('functions_defaults.php');	//default appearance such as time display
require_once('functions_general.php');	//FIXME: anything in there worth keeping?

/**
 * Debug function. Prints out variable $v
 *
 * \param $v variable of any type to display
 * \return nothing
  */
function d($v)
{
	if (is_string($v)) echo htmlentities($v);
	else {
		if (extension_loaded('xdebug')) var_dump($v);	//xdebug's var_dump is awesome
		else {
			echo '<pre>';
			print_r($v);
			echo '</pre>';
		}
	}
}

/**
 * Debug function. Prints $m to Apache log file
 */
function dp($m)
{
	global $config;

	error_log($m);
	if (!empty($config['debug'])) {
		error_log($m, 3, '/var/tmp/core_dev.log');
	}
}

/**
 * Helper function to include core function files
 *
 * \param $file filename to include
 */
function require_core($file)
{
	global $config;
	require_once($config['core']['fs_root'].'core/'.$file);
}

/**
 * Loads all active plugins
 */
function loadPlugins()
{
	global $config;

	if (empty($config['plugins'])) return;

	foreach ($config['plugins'] as $plugin) {
		require_once($config['core']['fs_root'].'plugins/'.$plugin.'/plugin.php');
	}
}

/**
 * Executes $c and returns the time it took
 */
function exectime($c)
{
	$exec_start = microtime(true);
	exec($c);
	return microtime(true) - $exec_start;
}

/**
 * Returns the project's path as a "project name" identifier. in a webroot hierarchy if scripts are
 * run from the / path it will return nothing, else the directory name of the directory script are run from
 */
function getProjectPath($_amp = 1)	//FIXME: get rid of this function
{
	global $config;

	if ($_amp == 3) return $config['app']['web_root'];

	if (!empty($_GET['pr'])) {
		$proj_name = basename(strip_tags($_GET['pr']));
	} else {
		$project_path = dirname($_SERVER['SCRIPT_NAME']);
		$pos = strrpos($project_path, '/');
		$proj_name = substr($project_path, $pos+1);
	}
		
	if ($proj_name == 'admin') $proj_name = '';

	if ($proj_name) {
		switch ($_amp) {
			case 0: return '?pr='.$proj_name;
			case 1: return '&amp;pr='.$proj_name;
			case 2: return '&pr='.$proj_name;
		}
	}
	return '';
}

?>
