<?php
/**
 * $Id$
 *
 * API for http://is.gd/ URL shortening service
 *
 * API documentation:
 * http://is.gd/api_info.php
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: works 2011-01-13

require_once('IShortUrlClient.php');

require_once('HttpClient.php');

class ShortUrlClientIsGd implements IShortUrlClient
{
    static function shorten($input_url)
    {
        $url = 'http://is.gd/api.php?longurl='.urlencode($input_url);
        $http = new HttpClient($url);
        $http->setCacheTime(86400); //24 hours

        $res = $http->getBody();

        if (substr($res, 0, 4) == 'http')
            return trim($res);

        throw new Exception ('Error: '.$res);
    }

}

?>
