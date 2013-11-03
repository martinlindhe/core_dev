<?php
/**
 * $Id$
 *
 * Functions assumed to always be available
 *
 * @author Martin Lindhe, 2007-2012 <martin@startwars.org>
 */

namespace cd;

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

// required for the string functions to work properly
mb_internal_encoding('UTF-8');

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
 * Debug function. Prints out $s
 */
function d($s)
{
    $cli = is_cli();

    if (is_string($s)) {
        //XXX show name of the variable passed to this function somehow, backtrace or var_name() ?

        if (is_cli()) {
            var_dump($s);
        } else {
            $out = htmlentities($s, ENT_QUOTES, 'UTF-8');
            $out = str_replace("\n", "<br/>", $out);

            if ($out != htmlentities($s, ENT_QUOTES, 'UTF-8'))
                echo '<pre>'."\n".$out.'</pre>';
            else
                var_dump($out);
        }
        echo ln();
        return;
    }

    if (!$s) {
        echo "NULL".ln();
        return;
    }

    //xdebug's var_dump is awesome
    if (extension_loaded('xdebug')) {
        var_dump($s);
        return;
    }

    if (!$cli)
        echo '<pre>'."\n";

    print_r($s);
    echo ln();

    if (!$cli)
        echo '</pre>';
}

/**
 * Debug function. Prints out $s, with timestamp
 */
function dt($s)
{
    echo "[".sql_datetime( time() )."] ";

    echo is_string($s) ? $s.ln() : d($s);
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

/**
 * Displays a debug timestamp
 */
function dts($s = '')
{
    //FIXME: show millisecond precision
    echo 'dts ['.sql_datetime( time() ).']';
    if ($s)
        echo ' '.$s;
    echo "\n";
}

function is_cli()
{
    $page = \cd\XmlDocumentHandler::getInstance();

    return (php_sapi_name() == 'cli' || $page->getMimeType() == 'text/plain');
//    return (php_sapi_name() == 'cli' || $page->getMimeType() == 'text/plain' || $page->getMimeType() == '');
}

function debug_sleep($min_time, $max_time = 0)
{
    if (!$max_time)
        $max_time = $min_time;

    $val = mt_rand($min_time*1000000, $min_time*1000000);
    dt('sleeping for '.$min_time.' to '.$max_time.' = '.$val.' ...');
    usleep( $val );
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
    if (is_array($str) || is_object($str))
        $str = serialize($str);

    error_log($str);

    $f = '/tmp/core_dev.log';

    if (!file_exists($f)) {
        touch($f);
        chmod($f, 0666); // writable by all
    }

    error_log(date('[r] ').$str.PHP_EOL, 3, $f);
}

/**
 * Debug function. Returns memory usage
 */
function dm()
{
    $used_mem = memory_get_peak_usage(false);

    $res = 'Memory: using '.round(ConvertDatasize::convert('byte', 'MiB', $used_mem), 1).' MiB';

    $memory_limit = ini_get('memory_limit');

    if ($memory_limit != '-1') { // "-1" means "no memory limit"

        $limit = datasize_to_bytes($memory_limit);
        $pct = round($used_mem / $limit * 100, 1);
        $limit_s = round(ConvertDatasize::convert('byte', 'MiB', $limit), 1);
        $res .=
        ' ('.$pct.'% of '.$limit_s.' MiB)'.ln();
    } else {
        $res .= ' (no limit)'.ln();
    }

    if (extension_loaded('apc')) {
        $info = apc_cache_info('', true);

        $res .=
        'APC: using '.round(ConvertDatasize::convert('byte', 'MiB', $info['mem_size']), 2).' MiB'.
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
                if (is_array($arg))
                    echo implode(", ", $arg);
                else
                    echo $arg;
            }
            if ($i < count($l['args'])) echo ', ';
        }
        echo ')';
        if (!empty($l['file']))
            echo ' from '.$l['file'].':'.$l['line']."\n";;
        echo ln();
    }

    if (php_sapi_name() != 'cli') echo '</pre>';
}

/**
 * Debug function
 * @return string of hex + ascii values
 */
function dh($m, $row_len = 16, $fill_char = ' ', $html_encode = true)
{
    $j = 0;
    $bytes = '';
    $hex = '';
    $res = '';

    for ($i = 0; $i < strlen($m); $i++)
    {
        $x = substr($m, $i, 1);

        if (ord($x) > 30 && ord($x) < 0x80)
            $bytes .= $html_encode ? htmlspecialchars($x) : $x;
        else
            $bytes .= '.';

        $hex .= bin2hex($x).$fill_char;

        if (++$j == $row_len) {
            $j = 0;
            $res .= $hex.' '.$bytes.ln();
            $bytes = '';
            $hex = '';
        }
    }

    if ($j) {
        $res .=
        $hex.' '.
        str_repeat(' ', ($row_len - strlen($bytes)) * 3).
        $bytes.ln();
    }

    return $res;
}

/**
 * Debug function
 * @return string of printed human readable hex
 */
function hexstr($s, $fill_char = ' ')
{
    $res = '';

    for ($i=0; $i < strlen($s); $i++)
    {
        $x = substr($s, $i, 1);
        $res .= bin2hex($x).$fill_char;
    }

    return $res;
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
 * @return true if string only contain \w (a-z, A-Z, 0-9, _), ".", or \p{L} (utf8 letters) or -         (FALSE: " ' <space> etc)
 */
function is_alphanumeric($s)
{
    if (!$s)
        return true;

    if (!is_string($s))
        return false;

    $regexp = '/^([\p{L}\w-\.])+$/u';
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
 * Checks if a string contains numbers 0-9 and - .
 */
function numbers_only($s) //XXXX FIXME use a regexp   FIXME rename to is_digits()
{
    if (strlen($s) == 0)
        return false;

    $ok = array('0','1','2','3','4','5','6','7','8','9', '-', '.');
    for ($i=0; $i<strlen($s); $i++) {
        $c = substr($s, $i, 1);
        if (!in_array($c, $ok))
            return false;
    }
    return true;
}
function is_digit($s) { return numbers_only($s); }
function is_digits($s) { return numbers_only($s); }

/**
 * Similar to is_year_period() but accepts any 2 numbers,
 * like "2-0" (sport score), "20-25" etc
 */
function is_number_range($s)
{
    $regexp = '/^(\d+)[-](\d+)$/';
    preg_match_all($regexp, $s, $matches);

    if ($matches && $matches[0] && $matches[0][0] == $s)
        return true;

    return false;
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
        throw new \Exception ('not a number '.$s);

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

function strtoupper_utf8($s)
{
    return mb_convert_case($s, MB_CASE_UPPER, 'UTF-8');
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

/** @return a boolean representation of input value as a string */
function sbool($b)
{
     return $b ? 'true' : 'false';
}

/** @return a boolean value of input string */
function string_to_bool($s)
{
    switch (strtolower($s)) {
    case 'true': return true;
    case 'false': return false;
    }

    return -1;
}

/** @return a integer representation of the bool value */
function bool_to_int($b)
{
    if (is_bool($b) === false)
        throw new \Exception ('not a bool: '.$b);

    return $b ? 1 : 0;
}

/** If string $needle is inside $haystack, then return true */
function instr($haystack, $needle)
{
    if (!is_string($haystack) || !is_string($needle))
        throw new \Exception ('strings only');

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
 * @return string between $needle1 and $needle2 or false if not found
 */
function str_between($s, $needle1, $needle2)
{
    $p1 = strpos($s, $needle1);
    if ($p1 === false)
        return false;

    $p2 = strpos($s, $needle2, $p1 + strlen($needle1));
    if ($p2 === false)
        return false;

    return substr($s, $p1 + strlen($needle1), $p2 - $p1 - strlen($needle1));
}

/**
 * reduce excessive whitespace to a single space
 */
function reduce_whitespace($s)
{
    $s = htmlchars_decode($s);
    $s = str_replace("\t", ' ', $s); //tabs -> spaces
    $s = str_replace("\n", ' ', $s); //linefeed -> spaces
    $s = str_replace("\r", ' ', $s); //linefeed -> spaces
    do {
        $tmp = $s;
        $s = str_replace('  ', ' ', $s);
    } while ($s != $tmp);

    return trim($s);
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

/**
 * Returns input phone number in MSID format
 * Defaults to Sweden (+46) in case of missing country code
 *
 * @param $anr user typed phone number
 * @param $cc country code
 * @return MSID formatted phone number (46707123456)
 */
function formatMSID($anr, $cc = '46')
{
    $anr = str_replace(array("\t", ' ', '-', '+'), '', $anr);

    if (substr($anr, 0, 2) == '00' && strlen($anr) >= 8)
        $anr = substr($anr, 2);

    // number without country code
    if (substr($anr, 0, 1) == '0' && strlen($anr) >= 6 )
        $anr = $cc.substr($anr, 1);

    return $anr;
}

/**
 * Translates strings into other languages
 */
function t($s)
{
    if (!$s)
        throw new \Exception ('huh');

    $locale = \cd\LocaleHandler::getInstance();

    switch ($locale->get()) {
    case 'ger': return $s;             // German (Deutsch)   - XXX not translated
    case 'eng': return $s;             // English (System default)
    case 'swe': $t = \cd\t_swe($s); break; // Swedish (Svenska)
    default: die('Unhandled language: '.$locale->get());
    }

    if (!$t) {
        dp('Untranslated string: '.$s);
        return '__('.$s.')__';
    }

    return $t;
}
