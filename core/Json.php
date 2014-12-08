<?php
/**
 * @author Martin Lindhe, 2010-2014 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

require_once('HttpClient.php');
require_once('html.php');

class Json
{
    /**
     * Encodes input as objects
     */
    public static function encode($obj, $with_keys = true)
    {
///        return json_encode($obj, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);


        if (is_array($obj))
            return self::encodeArray($obj, $with_keys);


        if (is_object($obj))
            return self::encodeObject($obj, $with_keys);

        throw new \Exception ('ewwp');

    }

    static function encodeArray($obj, $with_keys = true)
    {
        return '['.self::encodeInternal($obj, $with_keys).']';
    }

    static function encodeObject($obj, $with_keys = true)
    {
        return '{'.self::encodeInternal($obj, $with_keys).'}';
    }


    public static function decode($data, $assoc = false)
    {
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

        case JSON_ERROR_UTF8:
            $e = 'Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
        }

        if ($e && !$res)
            throw new \Exception('JSON::decode: '.$e.' on input data: '.$data);

        return $res;
    }

    private static function encodeInternal($list, $with_keys = false)
    {
        $all = array();

        foreach ($list as $key => $val)
        {
            if (is_object($val) || is_array($val)) {
                $all[] = json_encode($val);
                continue;
            }
            //throw new \Exception ('eh '.$key.' = '.$val);

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
}
