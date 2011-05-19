<?php
/**
 * $Id$
 *
 * Functions assumed to always be available
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

require_once('CoreBase.php');
require_once('LocaleHandler.php');        //for translations
require_once('output_xhtml.php');         //for XHTML output helper functions
require_once('functions_textformat.php'); //for decodeDataSize()
require_once('Timestamp.php');
require_once('network.php');
require_once('files.php');
require_once('time.php');
require_once('html.php');
require_once('ConvertDatasize.php');
require_once('XmlDocumentHandler.php');

// PHP_VERSION_ID is available as of PHP 5.2.7, if our
// version is lower than that, then emulate it
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

/**
 * PHP version check
 *
 * @param $ver minimum version number
 * @return true if we are running at least PHP $ver
 */
function php_min_ver($ver)
{
    if (strnatcmp(phpversion(), $ver) < 0)
        return false;

    return true;
}

/**
 * Debug function. Prints out variable $v
 *
 * @param $v variable of any type to display
 * @return nothing
 */
function d($v)
{
    $cli = is_cli();

    if (is_string($v)) {
        //XXX show name of the variable passed to this function somehow, backtrace or var_name() ?

        if (is_cli()) {
            var_dump($v);
        } else {
            $out = htmlentities($v, ENT_QUOTES, 'UTF-8');
            $out = str_replace("\n", "<br/>", $out);

            if ($out != htmlentities($v, ENT_QUOTES, 'UTF-8'))
                echo '<pre>'."\n".$out.'</pre>';
            else
                var_dump($out);
        }
        echo ln();
        return;
    }

    if (!$v) {
        echo "NULL".ln();
        return;
    }

    //xdebug's var_dump is awesome
    if (extension_loaded('xdebug')) {
        var_dump($v);
        return;
    }

    if (!$cli) echo '<pre>'."\n";
    print_r($v);
    echo ln();
    if (!$cli) echo '</pre>';
}

/**
 * Displays string as intended to be read. > is rendered in the browser and terminal
 */
function ds($s)
{
    if (php_sapi_name() == 'cli')
        return $s;
    else
        return htmlentities($s);
}

function is_cli()
{
    $page = XmlDocumentHandler::getInstance();

    return (php_sapi_name() == 'cli' || $page->getMimeType() == 'text/plain');
//    return (php_sapi_name() == 'cli' || $page->getMimeType() == 'text/plain' || $page->getMimeType() == '');
}

/**
 * Returns appropriate line feed character
 */
function ln()
{
    return is_cli() ? PHP_EOL : '<br/>';
}

/**
 * Debug function. Prints $str to Apache log file
 */
function dp($str)
{
    global $config;

    if (is_array($str) || is_object($str))
        $str = serialize($str);

    error_log($str);

    if (!empty($config['debug']))
        error_log(date('[r] ').$str.PHP_EOL, 3, '/tmp/core_dev.log');
}

/**
 * Debug function. Returns memory usage
 */
function dm()
{
    $conv = new ConvertDatasize();

    $res = 'Memory: using '.round($conv->convLiteral(memory_get_peak_usage(false), 'MiB', 'byte'), 1).' MiB';

    // "-1" means "no memory limit"
    if (ini_get('memory_limit') != '-1') {
        //XXX simplify datasize conversion
        $limit = $conv->convLiteral(ini_get('memory_limit'), 'byte'); //convert from "128M", or "4G" to bytes
        $res .= ' ('.round(memory_get_peak_usage(false) / $limit * 100, 1).'% of '.$conv->convLiteral($limit, 'MiB').' MiB)'.ln();
    } else {
        $res .= ' (no limit)'.ln();
    }

    if (function_exists('apc_cache_info')) {
        $conv = new ConvertDatasize();

        $info = apc_cache_info('', true);
//d($info);
        $res .=
        'APC: using '.round($conv->convLiteral($info['mem_size'], 'MiB'), 2).' MiB'.
        ', '.$info['num_hits'].' hits, '.$info['num_misses'].' misses, '.$info['num_entries'].' entries (max '.$info['num_slots'].')'.ln();
    }

    return $res;
}

/** Debug function. Prints backtrace */
function bt()
{
    $bt = debug_backtrace();
    if (php_sapi_name() != 'cli') echo '<pre>';

    foreach ($bt as $idx => $l)
    {
//        if (!empty($l['class'])) echo '(class '.$l['class'].') ';
        if (!empty($l['object'])) echo get_class($l['object']).$l['type'];
        echo $l['function'].'(';

        $i = 0;
        foreach ($l['args'] as $arg) {
            $i++;
            if (is_object($arg)) {
                echo gettype($arg).' '.get_class($arg);
            } else {
                echo $arg;
            }
            if ($i < count($l['args'])) echo ', ';
        }
        echo ')';
        if (!empty($l['file']))
            echo ' from '.$l['file'].':'.$l['line'];
        echo ln();
    }

    if (php_sapi_name() != 'cli') echo '</pre>';
}

/**
 * Debug function. Prints $m as hex + ascii values
 */
function dh($m, $row_len = 16)
{
    $len = strlen($m);

    if ($len >= 10)
        $len = $len.'/0x'.dechex($len);

    echo '[['.$len.' bytes:]]'.ln();
    $j = 0;
    $bytes = '';
    $hex = '';

    for ($i=0; $i < $len; $i++)
    {
        $x = substr($m, $i, 1);

        if (ord($x) > 30 && ord($x) < 0x80)
            $bytes .= $x;
        else
            $bytes .= '.';

        $hex .= bin2hex($x).' ';

        if (++$j == $row_len) {
            $j = 0;
            echo $hex.' '.$bytes.PHP_EOL;
            $bytes = '';
            $hex = '';
        }
    }

    if ($j) {
        echo $hex.' ';
        echo str_repeat(' ', ($row_len - strlen($bytes)) * 3);
        echo $bytes.PHP_EOL;
    }

    if ($m)
        echo 'md5: '.md5($m).PHP_EOL;
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
 * @return true if string only contain \w (a-z, A-Z, 0-9, _) or \p{L} (utf8 letters) or -         (FALSE: " ' <space> etc)
 */
function is_alphanumeric($s)
{
    if (!$s)
        return true;

    if (!is_string($s))
        return false;

    $regexp = '/^([\p{L}\w-])+$/u';
    preg_match_all($regexp, $s, $matches);

    if ($matches && $matches[0] && $matches[0][0] == $s)
        return true;

    return false;
}

/**
 * Generate a random string of a-z, A-Z, 0-9 (62 combinations)
 */
function randstr($len)
{
    $res = '';
    for ($i=0; $i<$len; $i++) {
        $rnd = mt_rand(0, 61);
        if ($rnd < 10)
            $res .= chr($rnd+48);
        else if ($rnd < 36)
            $res .= chr($rnd+55);
        else
            $res .= chr($rnd+61);
    }
    return $res;
}

/**
 * Checks if a string contains only numbers 0-9
 */
function numbers_only($s) //XXXX FIXME use a regexp
{
    $ok = array('0','1','2','3','4','5','6','7','8','9');
    for ($i=0; $i<strlen($s); $i++) {
        $c = substr($s, $i, 1);
        if (!in_array($c, $ok))
            return false;
    }
    return true;
}

/**
 * Rounds a number to exactly $precision number of decimals, padding with zeros if nessecary
 */
function round_decimals($val, $precision = 0, $separator = '.', $combinator = '.')  // XXX FIXME move to math.php
{
    $ex = explode($separator, round($val, $precision));

    if (empty($ex[1]) || strlen($ex[1]) < $precision)
        $ex[1] = str_pad( !empty($ex[1]) ? $ex[1] : 0, $precision, '0');

    if (!$precision)
        return $ex[0];

    return implode($combinator, $ex);
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

/**
 * Pads input string $s to exactly $len chars with $pad_s and cuts to exactly $len chars, from the left
 */
function strpad_exact($s, $len, $pad_s = ' ')
{
    return substr( str_pad($s, $len, $pad_s, STR_PAD_RIGHT), 0, $len);
}

/**
 * Prefixes input string $s with $pad_s and cuts to exactly $len chars, from the right
 */
function strpre_exact($s, $len, $pad_s = ' ')
{
    return substr( str_pad($s, $len, $pad_s, STR_PAD_LEFT), -$len);
}

function array_last_entry($arr)
{
    if (!is_array($arr))
        return;

    if (empty($arr))
        return;

    return end($arr);
}

function array_first_entry($arr)
{
    if (!is_array($arr))
        return;

    if (empty($arr))
        return;

    reset($arr);
    return current($arr);
}

/**
 * Renders number of bytes in a easily readable format, such as "2.5 KiB" or "3 MiB"
 */
function byte_count($s)
{
    if (!is_numeric($s))
        throw new Exception ('not a number '.$s);

    if ($s < 1024)
        return $s.' bytes';

    if ($s < (1024 * 1024))
        return round($s / 1024, 1).' KiB';

    if ($s < (1024 * 1024 * 1024))
        return round($s / 1024 / 1024, 1).' MiB';

    if ($s < (1024 * 1024 * 1024 * 1024))
        return round($s / 1024 / 1024 / 1024, 1).' GiB';

    return round($s / 1024 / 1024 / 1024 / 1024).' TiB';
}

/**
 * Needed (in PHP 5.3) to correctly lowercase swedish words, like "Östen"->"östen"
 */
function strtolower_utf8($s)
{
    return mb_convert_case($s, MB_CASE_LOWER, 'UTF-8');
}

function ucfirst_utf8($s)
{
    return mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');
}

/** @return system uptime, in seconds **/
function uptime()
{
    // XXX Linux only, windows code available in phpshsinfo, http://phpsysinfo.sourceforge.net/ in class.WINNT.inc.php
    // XXX2 make simple OS-specific classes wrapping these kind of features
    $raw = explode(' ', file_get_contents('/proc/uptime') );
    return $raw[0];
}

/** returns a boolean representation of input value as a string */
function sbool($b)
{
     return $b ? 'true' : 'false';
}

/** If string $needle is inside $haystack, then return true */
function instr($haystack, $needle)
{
    if (!is_string($haystack) || !is_string($needle))
        throw new Exception ('strings only');

    if (strpos($haystack, $needle) === false)
        return false;

    return true;
}

/**
 * Removes all spaces from input string
 */
function strip_spaces($s)
{
    return str_replace(' ', '', $s);
}

/**
 * reduce excessive whitespace to a single space
 */
function reduce_whitespace($s)
{
    $s = htmlchars_decode($s);
    $s = str_replace("\t",     ' ', $s); //tabs -> spaces
    do {
        $tmp = $s;
        $s = str_replace('  ', ' ', $s);
    } while ($s != $tmp);
    return $s;
}

/**
 * Strips BOM marker from UTF-8 text
 */
function utf8_strip_bom($s)
{
    if (substr($s, 0, 3) == "\xEF\xBB\xBF")
        return substr($s, 3);

    return $s;
}

?>
