<?php
/**
 * $Id$
 *
 * Functions assumed to always be available
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//FIXME: get rid of getProjectPath()

require_once('LocaleHandler.php'); //for translations
require_once('output_xhtml.php');   //for XHTML output helper functions
require_once('functions_general.php');  //FIXME: anything in there worth keeping?
require_once('functions_textformat.php');   //for decodeDataSize()
require_once('prop_Timestamp.php');
require_once('network.php');
require_once('files.php');
require_once('class.CoreBase.php'); //for CoreBase class
require_once('conv_datasize.php');

// PHP_VERSION_ID is available as of PHP 5.2.7, if our
// version is lower than that, then emulate it
if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

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
        echo ln();
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
    return php_sapi_name() == 'cli' ? PHP_EOL : '<br/>';
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
        error_log(date('[r] ').$str.PHP_EOL, 3, '/tmp/core_dev.log');
    }
}

/**
 * Debug function. Returns memory usage
 */
function dm()
{
    $conv = new ConvertDatasize();

    $limit   = $conv->convLiteral(ini_get('memory_limit'), 'byte');
    $current = $conv->convLiteral(memory_get_peak_usage(false), 'byte');

    $res =
    "Memory usage: ".
    round($conv->convLiteral($current, 'MiB'), 1)." MiB";

    if ($limit)
        $res .=
        " (".round(memory_get_peak_usage(false) / $limit * 100, 1)."% of ".
        $conv->convLiteral($limit, 'MiB')." MiB)".ln();

    return $res;
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

        //echo count($l['args']).' args'.ln();
        $i = 0;
        foreach ($l['args'] as $arg) {
            $i++;
            echo $arg;
            if ($i < count($l['args'])) echo ', ';
        }
        echo ') from '.$l['file'].ln();

        if (!empty($l['class'])) echo 'XXX class '.$l['class'].ln();
        if (!empty($l['object'])) echo 'XXX object '.d($l['object']).ln();
        if (!empty($l['type'])) echo 'XXX type '.$l['type'].ln();
    }

    if (php_sapi_name() != 'cli') echo '</pre>';
}

/**
 * Debug function. Prints $m as hex + ascii values
 */
function dh($m)
{
    echo '[[dumping '.strlen($m).' bytes]]'.ln();
    $j = 0;
    $bytes = '';
    $hex = '';

    for ($i=0; $i<strlen($m); $i++) {
        $x = substr($m, $i, 1);
        if (ord($x) > 30 && ord($x) < 0x80)
            $bytes .= $x;
        else
            $bytes .= '.';
        $hex .= bin2hex($x).' ';

        $j++;
        if ($j == 15) {
            $j = 0;
            echo $hex.' '.$bytes.PHP_EOL;
            $bytes = '';
            $hex = '';
        }
    }

    if ($j) {
        echo $hex.' ';
        echo str_repeat(' ', (15-strlen($bytes))*3 );
        echo $bytes.PHP_EOL;
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
 * @return true if string only contain \w (a-z, A-Z, 0-9, _) or \p{L} (utf8 letters) or -         (FALSE: " ' <space> etc)
 */
function is_alphanumeric($s)
{
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
function numbers_only($s)
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
function round_decimals($val, $precision = 0, $separator = '.', $combinator = '.')
{
    $ex = explode($separator, round($val, $precision));

    if (empty($ex[1]) || strlen($ex[1]) < $precision)
        $ex[1] = str_pad(@$ex[1], $precision, '0');

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

?>
