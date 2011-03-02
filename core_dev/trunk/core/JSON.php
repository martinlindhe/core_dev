<?php
/**
 * $Id$
 *
 * JSON's basic types are:
 * Number (double precision floating-point format)
 * String (double-quoted Unicode with backslash escaping)
 * Boolean (true or false)
 * Array (an ordered sequence of values, comma-separated and enclosed in square brackets)
 * Object (a collection of key:value pairs, comma-separated and enclosed in curly braces; the key must be a string)
 * null
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: ok

require_once('HttpClient.php');

class JSON
{
    public static function encode($obj, $with_keys = true)
    {
        if (is_array($obj))
            return '['.self::encodeObject($obj, false).']';

        if (is_object($obj))
            return '{'.self::encodeObject($obj, true).'}';

        throw new Exception ('ewwp');

        return json_encode($obj);
    }

    /** encodes an objects and arrays */
    public static function encodeObject($list, $with_keys = false)
    {
        $all = array();

        foreach ($list as $key => $val)
        {
            $res = '';
            if ($with_keys)
                if (is_numeric($key) && (strlen($key) == 1 || substr($key, 0, 1) != '0'))
                    $res .= $key.':';
                else
                    $res .= '"'.$key.'":';
            if (is_bool($val))
                $res .= ($val ? '1' : '0');
            else if (is_numeric($val) && (strlen($val) == 1 || substr($val, 0, 1) != '0' || strpos($val, '.') !== false))
                $res .= $val;
            else {
                $val = str_replace('"', '&quot;', $val); // "
                $val = str_replace("\r", '&#13;', $val); // carriage return
                $val = str_replace("\n", '&#10;', $val); // line feed
                $res .= '"'.$val.'"';
            }
            $all[] = $res;
        }

        return implode(',', $all);
    }

    public static function decode($data, $assoc = false)
    {
        if (is_url($data)) {
            $http = new HttpClient($data);
            $http->setCacheTime(60 * 60); //1h
            $data = $http->getBody();
        }

        $res = json_decode($data, $assoc);

        $e = '';
        switch (json_last_error()) {
        case JSON_ERROR_DEPTH:
            $e = 'Maximum stack depth exceeded';
            break;

        case JSON_ERROR_CTRL_CHAR:
            $e = 'Control character error, possibly incorrectly encoded';
            break;

        case JSON_ERROR_STATE_MISMATCH:
            $e = 'State mismatch';
            break;

        case JSON_ERROR_SYNTAX:
            $e = 'Syntax error';
            break;
/* XXX PHP 5.3.3
        case JSON_ERROR_UTF8:
            $e = 'Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
*/
        }

        if ($e && !$res)
            throw new Exception('JSON::decode: '.$e);

        return $res;
    }
}

?>
