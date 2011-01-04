<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: ok

require_once('HttpClient.php');

class JSON
{
    public static function encode($obj)
    {
        return json_encode($obj);
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
