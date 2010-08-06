<?php
/**
 * $Id$
 */

class JSON
{
    public static function Encode($obj)
    {
        return json_encode($obj);
    }

    public static function Decode($json, $assoc = false)
    {
        $res = json_decode($json, $assoc);

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
            throw new Exception('JSON::Decode: '.$e);

        return $res;
    }
}

?>
