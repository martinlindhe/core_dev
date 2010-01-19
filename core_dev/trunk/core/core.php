<?php
/**
 * $Id$
 *
 * Functions assumed to always be available
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//FIXME: get rid of getProjectPath()

require_once('locale.php');	//for translations
require_once('output_xhtml.php');	//for XHTML output helper functions
require_once('functions_general.php');	//FIXME: anything in there worth keeping?
require_once('functions_textformat.php');	//for decodeDataSize()
require_once('prop_Timestamp.php');
require_once('network.php');
require_once('files.php');
require_once('class.CoreBase.php'); //for CoreBase class

/**
 * Debug function. Prints out variable $v
 *
 * @param $v variable of any type to display
 * @return nothing
 */
function d($v)
{
	if (is_string($v)) {
		//XXX show name of the variable passed to this function somehow, backtrace or var_name() ?

		if (php_sapi_name() == 'cli') {
			var_dump($v);
		} else {
			$out = htmlentities($v, ENT_QUOTES, 'UTF-8');
			$out = str_replace("\n", "<br/>", $out);

			if ($out != htmlentities($v, ENT_QUOTES, 'UTF-8'))
				echo '<pre>'.$out.'</pre>';

			else
				var_dump($out);
		}
		echo dln();
		return;
	}

	//xdebug's var_dump is awesome
	if (extension_loaded('xdebug')) {
		var_dump($v);
		return;
	}

	if (php_sapi_name() != 'cli') echo '<pre>';
	print_r($v);
	if (php_sapi_name() != 'cli') echo '</pre>';
}

/**
 * Displays string as intended to be read. > is rendered in the browser and terminal
 */
function ds($s)
{
	if (php_sapi_name() == 'cli') return $s;
	else return htmlentities($s);
}

/**
 * Returns appropriate line feed character
 */
function ln()
{
	return php_sapi_name() == 'cli' ? "\n" : '<br/>';
}

function dln() //TODO DEPRECATE this function
{
	return ln();
}

/**
 * Debug function. Prints $str to Apache log file
 */
function dp($str)
{
	global $config;

	if (is_array($str))
		$str = serialize($str);

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
		formatDataSize($limit).")".dln();
}

/**
 * Debug function. Prints backtrace
 */
function dtrace()
{
	$bt = debug_backtrace();
	if (php_sapi_name() != 'cli') echo '<pre>';

	foreach ($bt as $idx => $l)
	{
		echo $l['line'].': '.$l['function'].'(';

		//echo count($l['args']).' args'.dln();
		$i = 0;
		foreach ($l['args'] as $arg) {
			$i++;
			echo $arg;
			if ($i < count($l['args'])) echo ', ';
		}
		echo ') from '.$l['file'].dln();

		if (!empty($l['class'])) echo 'XXX class '.$l['class'].dln();
		if (!empty($l['object'])) echo 'XXX object '.d($l['object']).dln();
		if (!empty($l['type'])) echo 'XXX type '.$l['type'].dln();
	}

	if (php_sapi_name() != 'cli') echo '</pre>';
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
 * Returns the literal name of a variable
 *
 * @param $var variable
 */
function var_name(&$var, $scope = false)
{
	$scope = $scope ? $scope : $GLOBALS;

	$old = $var;
	$var = '__random__'.rand().'temp';

	$key = array_search($var, $scope);
	$var = $old;

	return $key;
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
 * Returns web root to core_dev from current project.
 * Every project using core_dev must place a symlink named "core_dev" to core_dev
 * base directory in the same directory as project config.php resides
 *
 * @example ln -s /devel/web/core_dev/trunk/ core_dev
 *
 * @return for example "/sample/core_dev/"
 */
function coredev_webroot()
{
	if (substr($_SERVER['REQUEST_URI'], -1) == '/')
		$path = $_SERVER['REQUEST_URI'];
	else {
		$dir = dirname($_SERVER['REQUEST_URI']);

		if (substr($dir, -1) == '/')
			$path = $dir;
		else
			$path = $dir.'/';
	}

	return $path.'core_dev/';
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
 * Rounds a number to exactly $precision number of decimals, padding with zeros if nessecary
 */
function round_decimals($val, $precision = 0)
{
	$ex = explode('.', round($val, $precision));

	if (empty($ex[1]) || strlen($ex[1]) < $precision) {
		$ex[1] = str_pad(@$ex[1], $precision, '0');
	}

	if (!$precision)
		return $ex[0];

	return implode('.', $ex);
}

/**
 * Returns line ending used in the input text
 *
 * @return "\n" (unix), "\r\n" (windows) or "\r" (mac)
 */
function str_get_ending($s)
{
	if (strpos($s, "\r\n") !== false)
		return "\r\n";

	if (strpos($s, "\r") !== false)
		return "\r";

	return "\n";
}

?>
