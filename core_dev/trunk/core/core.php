<?php
/**
 * $Id$
 *
 * Functions assumed to always be available
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

//FIXME: get rid of getProjectPath()

require_once('locale.php');	//for translations
require_once('output_xhtml.php');	//for XHTML output helper functions
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
 * Returns appropriate line feed character
 */
function dln()
{
	return php_sapi_name() == 'cli' ? "\n" : "<br/>";
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
 * Debug function. Returns memory usage
 */
function dm()
{
	$limit = decodeDataSize(ini_get('memory_limit'));

	return
		"Memory usage: ".
		formatDataSize(memory_get_peak_usage(false)).
		" (".round(memory_get_peak_usage(false) / $limit * 100, 1)."% of ".
		formatDataSize($limit).")".dln().dln();
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
function getProjectPath($_amp = 1)
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
	echo
		'<script type="text/javascript">'.
		'document.location.href="'.$url.'";'.
		'</script>';
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

/**
 * Checks if a string contains only numbers 0-9
 */
function numbers_only($s)
{
	$ok = array('0','1','2','3','4','5','6','7','8','9');
	for ($i=0; $i<strlen($s); $i++) {
		$c = substr($s, $i, 1);
		if (!in_array($c, $ok)) return false;
	}
	return true;
}

/**
 * Default time format display
 *
 * @param $ts unix timestamp or SQL DATETIME format
 */
function formatTime($ts = 0)
{
	if (!$ts) $ts = time();

	if (function_exists('formatTimeOverride'))
		return formatTimeOverride($ts);

	if (!is_numeric($ts)) $ts = strtotime($ts);

	$datestamp = mktime (0,0,0,date('m',$ts), date('d',$ts), date('Y',$ts));
	$yesterday = mktime (0,0,0,date('m') ,date('d')-1,  date('Y'));
	$tomorrow  = mktime (0,0,0,date('m') ,date('d')+1,  date('Y'));

	$timediff = time() - $ts;

	if (date('Y-m-d', $ts) == date('Y-m-d')) {
		//Today 18:13
		$res = date('H:i',$ts);
	} else if ($datestamp == $yesterday) {
		//Yesterday 18:13
		$res = t('Yesterday').' '.date('H:i',$ts);
	} else if ($datestamp == $tomorrow) {
		//Tomorrow 18:13
		$res = t('Tomorrow').' '.date('H:i',$ts);
	} else {
		//2007-04-14 15:22
		$res = date('Y-m-d H:i', $ts);
	}

	return $res;
}

?>
