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
        $res = json_encode($obj, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);

        return $res;
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
}
