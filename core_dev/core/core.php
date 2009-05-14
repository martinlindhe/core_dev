<?php
/**
 * $Id$
 *
 * Functions assumed to always be available
 *
 * @disclaimer This file is a required component of core_dev
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

//Include required core_dev files:
require_once('locale.php');	//for translations
require_once('output_xhtml.php');	//for XHTML output helper functions
require_once('functions_defaults.php');	//default appearance such as time display
require_once('functions_general.php');	//FIXME: anything in there worth keeping?
require_once('functions_textformat.php');	//for decodeDataSize()

/**
 * Debug function. Prints out variable $v
 *
 * @param $v variable of any type to display
 * @return nothing
 */
function d($v)
{
	if (is_string($v)) {
		if (php_sapi_name() == 'cli') echo $v;
		else echo htmlentities($v);
	}
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
 * Debug function. Prints $str to Apache log file
 */
function dp($str)
{
	global $config;

	error_log($str);
	if (!empty($config['debug'])) {
		error_log(date('[r] ').$str."\n", 3, '/tmp/core_dev.log');
	}
}

/**
 * Debug function. Prints $m as hex + ascii values
 */
function dh($m)
{
	echo "[[dumping ".strlen($m)." bytes]]\n";
	$j = 0;
	$bytes = '';
	$hex = '';

	for ($i=0; $i<strlen($m); $i++) {
		$x = substr($m, $i, 1);
		if (ord($x) > 30) {
			$bytes .= $x;
		} else {
			$bytes .= '.';
		}
		$hex .= bin2hex($x).' ';

		$j++;
		if ($j == 15) {
			$j = 0;
			echo "$hex $bytes\n";
			$bytes = '';
			$hex = '';
		}
	}

	if ($j) {
		echo $hex." ";
		echo str_repeat(' ', (15-strlen($bytes))*3 );
		echo "$bytes\n";
	}
}

/**
 * Debug function. Dumps memory usage
 */
function dm($db = '')
{
	$limit = decodeDataSize(ini_get('memory_limit'));

	require_once('functions_textformat.php');	//for formatDataSize()
	echo "[Memory usage]\n";
	echo "Limit  : ".formatDataSize($limit)."\n";
	echo "Current: ".formatDataSize(memory_get_usage(true))." (".round(memory_get_usage(true) / $limit * 100, 2)." %)\n";
	echo "Peak   : ".formatDataSize(memory_get_peak_usage(true))." (".round(memory_get_peak_usage(true) / $limit * 100, 2)." %)\n\n";

	echo "[emalloc memory report]\n";
	echo "Current: ".formatDataSize(memory_get_usage(false))." (".round(memory_get_usage(false) / $limit * 100, 2)." %)\n";
	echo "Peak   : ".formatDataSize(memory_get_peak_usage(false))." (".round(memory_get_peak_usage(false) / $limit * 100, 2)." %)\n\n";

	if (extension_loaded('xdebug')) {
		echo "[Xdebug Memory report]\n";
		echo "Current: ".formatDataSize(xdebug_memory_usage())." (".round(xdebug_memory_usage() / $limit * 100, 2)." %)\n";
		echo "Peak   : ".formatDataSize(xdebug_peak_memory_usage())." (".round(xdebug_peak_memory_usage() / $limit * 100, 2)." %)\n\n";
	}

	if ($db) {
		echo "[DB driver memory usage]\n";
		echo "Query history: ".sizeof($db->queries)."\n";	//XXX how to get actual size of the array?
	}

	echo "---\n";
}

/**
 * Includes a core function file
 *
 * @param $file filename to include
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
 *
 * @param $c command to execute
 * @param $retval return value of command executed
 */
function exectime($c, &$retval = 0)
{
	//XXX: Use 2>&1 in $c to redirect stderr to $output buffer
	$output = array();
	$exec_start = microtime(true);
	exec($c, $output, &$retval);

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

/**
 * Returns the current time in the same format as the MySQL "NOW()" command
 * @return time in MySQL datetime format
 */
function now()
{
	return strftime('%Y-%m-%d %H:%M:%S');
}

/**
 * Returns given UNIX timestamp in MySQL datetime format
 * @param $timestamp is a UNIX timestamp
 * @return given UNIX timestamp in MySQL datetime format
 */
function sql_datetime($timestamp)
{
	return date('Y-m-d H:i:s', $timestamp);
}

/**
 * Returns MySQL datetime in UNIX timestamp format
 * @param $datetime is a MySQL datetime
 * @return given MySQL datetime in UNIX timestamp format
 */
function datetime_to_timestamp($datetime)
{
	return strtotime($datetime);
}

/**
 * Compares two MySQL datetime timestamps
 * @param $d1 is a MySQL datetime
 * @param $d2 is a MySQL datetime
 * @return true if $d1 is older date than $d2
 */
function datetime_less($d1, $d2)
{
	if (strtotime($d1) < strtotime($d2)) return true;
	return false;
}

/**
 * Injects Javascript snippet to redirect the user to required destination
 * This is used mainly to avoid overcomplicated code paths when rendering
 * webpages & the need to redirect the user after html output already started.
 *
 * FIXME: implement one of the following solutions instead
 * a) implement a proper template engine which could also handle this
 * b) turn output buffering on in php.ini (not always an option)
 */
function goLoc($url)
{
	echo '<script type="text/javascript">';
	echo 'document.location.href = "'.$url.'";';
	echo '</script>';
}

/**
 * Generate a random string of a-z, A-Z, 0-9 (62 combinations)
 */
function randstr($len)
{
	$res = '';
	for ($i=0; $i<$len; $i++) {
		$rnd = mt_rand(0, 61);
		if ($rnd < 10) {
			$res .= chr($rnd+48);
		} else if ($rnd < 36) {
			$res .= chr($rnd+55);
		} else {
			$res .= chr($rnd+61);
		}
	}
	return $res;
}

?>
