<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2012 <martin@ubique.se>
 */

//STATUS: good

//TODO: rename IPv4_to_GeoIP() and GeoIP_to_IPv4()
//TODO: is_ipv4() and is_ipv6() regexp matchers
//TODO: IPv6 support

/**
 * Converts a IPv4 address to GeoIP format
 *
 * @param $ipv4 IPv4 address in 123.123.123.123 format
 * @return 32bit unsigned value (GeoIP formatted IPv4 address)
 */
function IPv4_to_GeoIP($ipv4)
{
    if (is_numeric($ipv4)) return $ipv4;

    $arr = explode('.', trim($ipv4));
    if (count($arr) != 4) return 0;

    $num = ($arr[0]*16777216) + ($arr[1]*65536) + ($arr[2]*256) + $arr[3];
    return $num;
}

/**
 * Converts a GeoIP address to human readable format
 *
 * @param $geoip 32bit unsigned value (GeoIP formatted IPv4 address)
 * @return IPv4 address in 123.123.123.123 format
 */
function GeoIP_to_IPv4($geoip)
{
    if (!is_numeric($geoip)) return 0;
    settype($geoip, 'float');

    $w = ($geoip / 16777216) % 256;
    $x = ($geoip / 65536   ) % 256;
    $y = ($geoip / 256     ) % 256;
    $z = ($geoip           ) % 256;
    if ($z < 0) $z += 256;

    return $w.'.'.$x.'.'.$y.'.'.$z;
}

/**
 * Checks if client IP address is in the whitelist
 * Useful to create simple IP access rules
 *
 * @param $whitelist array of IPv4 addresses
 * @return true if client IP address is in the $allowed list
 */
function allowed_ip($whitelist)
{
    if (php_sapi_name() == 'cli') return true;

    $ip = IPv4_to_GeoIP(client_ip());

    return match_ip($ip, $whitelist);
}

/**
 * Checks a IPv4 address against a whitelist
 *
 * @param $ip IPv4 address in GeoIP or human readable format
 * @param $matches array of IPv4 addresses
 * @return true if $ip address is found in the $matches list
 */
function match_ip($ip, $matches)
{
    if (!is_numeric($ip)) $ip = IPv4_to_GeoIP($ip);

    foreach ($matches as $chk) {
        $a = explode('/', $chk);    //check against "80.0.0.0/8" format
        if (count($a) == 2) {
            $lo = IPv4_to_GeoIP($a[0]);
            if ($ip >= $lo) {
                $hi = $lo+bindec('1'.str_repeat('0', 32-$a[1])) - 1;
                //echo "lo: ".GeoIP_to_IPv4($lo)."   (".$lo.")\n";
                //echo "hi: ".GeoIP_to_IPv4($hi)."   (".$hi.")\n";
                if ($ip <= $hi)
                    return true;
            }
        } else if ($ip == IPv4_to_GeoIP($chk))
            return true;
    }

    return false;
}

/**
 * Returns client IP address as a literal string
 */
function client_ip()
{
    if (php_sapi_name() == 'cli') return '127.0.0.1';
    return $ip = $_SERVER['REMOTE_ADDR'];
}

// http://www.blooberry.com/indexdot/html/topics/urlencoding.htm
define('URL_REGEXP',
'('.
    '(https?|ftps?|rtmpe?|mms|rtsp){1}://'. // protocol
    '(?:\w+'.        // optional username
        '(:\w+)?'.   // optional password
        '@'.         // required separator
    ')?'.
    '(\w)+'.         // 1 or more alphanumeric
    '([\w\-\.])+'.   // 1 or more alphanumeric, . or -
    '(:\d+)?'.       // optional port number
    '(/'.            // optional url parameters must begin with /
        '('.
            // 0 or more alphanumeric and/or: _-'!?=&@;+*#%.,:~()
            '[\w/_\-\'\!\?\|\=\&\@\;\+\*\#\%\.\,\:\~\(\)]*'.
        ')?'.
    ')?'.
')i'
);

/**
 * Checks if input string is a valid URL
 *
 * @param $url string
 * @return true if input is a url
 */
function is_url($url)
{
    preg_match(URL_REGEXP, $url, $matches);

    if ($matches && $matches[0] == $url)
        return true;

    return false;
}

/**
 * Extracts all url:s from input string
 */
function match_urls($str, $keep_dupes = false)
{
    preg_match_all(URL_REGEXP, $str, $matches);

    if ($keep_dupes)
        return $matches[0];

    return array_merge(array_unique($matches[0]));
}

/**
 * Checks if input string is a valid email address
 *
 * @param $adr string
 * @return true if input is a email address
 */
function is_email($adr)
{
    $pattern = '/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/';
    if (preg_match($pattern, $adr))
        return true;

    return false;
}

/**
 * @return protocol part of url, e.g. "http"
 */
function get_protocol($url)
{
    $p = strpos($url, '://');
    if (!$p) return false;

    return substr($url, 0, $p);
}

/**
 * @return default port for protocol $scheme
 */
function scheme_default_port($scheme)
{
    $schemes = array('http'=>80, 'https'=>443, 'rtsp'=>554, 'rtmp'=>1935, 'rtmpe'=>1935, 'mms'=>1755);

    if (empty($schemes[$scheme]))
        return false;

    return $schemes[$scheme];
}

/**
 * Decodes a string of raw POST params (key1=val+1&key2=val%202) into an array
 */
function decode_raw_http_params($raw)
{
    $out = array();

    $pairs = explode('&', $raw);
    foreach ($pairs as $key => $val) {
        $x = explode('=', $val);
        $out[ $x[0] ] = urldecode($x[1]);
    }
    return $out;
}

/**
 * Returns true if client ip is localhost
 */
function is_client_localhost()
{
    //XXX resolve ip's to common format
    if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' ||  $_SERVER['REMOTE_ADDR'] == '::ffff:127.0.0.1')
        return true;

    // if client ip == server ip, return true
    if ($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'])
        return true;

    return false;
}

/**
 * Embeds a array for use in a URL
 *
 * @return string, e.g. name[]=val1&name[]=val2
 */
function url_array($name, $arr)
{
    $res = array();
    foreach ($arr as $s)
        $res[] = $name.'[]='.$s;

    return implode('&', $res);
}

/**
 * Builds a url query
 * basically the same as PHP's http_build_query except it doesnt auto-encode values and thus dont break urls
 * with other separators than &, such as ; (commonly used by eg. git web interface)
 *
 * @param $arr key=>val pairs
 * @param $safe url encodes "unsafe" characters in key values
 * @return string, e.g. name1=val&name2=val3&name3
 */
function url_query($arr, $separator = '&', $safe = true)
{
    $res = array();
    foreach ($arr as $key => $val)
        if ($val)
            $res[] = $key.'='.($safe ? coredev_urlencode($val) : $val);
        else
            $res [] = $key;

    return implode($separator, $res);
}

/**
 * Similar to urlencode(), rawurlencode() except it does not escape ;
 * Attempts to follow RFC 1738 better than PHP 5.3 does:
 *
 * The characters ";", "/", "?", ":", "@", "=" and "&" are the characters which may be
 * reserved for special meaning within a scheme. No other characters may
 * be reserved within a scheme.
 *
 * Usually a URL has the same interpretation when an octet is
 * represented by a character and when it encoded. However, this is not
 * true for reserved characters: encoding a character reserved for a
 * particular scheme may change the semantics of a URL.
 *
 * Thus, only alphanumerics, the special characters $-_.+!*'(), and
 * reserved characters used for their reserved purposes may be used
 * unencoded within a URL.
 */
function coredev_urlencode($s)
{
    $res = '';
    for ($i=0; $i<strlen($s); $i++) {
        $c = substr($s, $i, 1);
        switch ($c) {
        case '+': $c = '%2B'; break;
        case ':': $c = '%3A'; break;
        case '"': $c = '%22'; break;
        case ' ': $c = '+'; break;
        }
        if (ord($c) >= 128 || ord($c) <= 15)
            $c = '%'.strtoupper(dechex(ord($c)));

        $res .= $c;
    }

    return $res;
}

/**
 * Determines if we have a reliable connection to
 * the specified host, by attempting to successfully open
 * a tcp connection to the host within specified timeout.
 * @param float $timeout in seconds
 */
function can_connect($host, $port = 80, $timeout = 30)
{
    $sock = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if (!$sock)
        return false;

    fclose($sock);
    return true;
}
