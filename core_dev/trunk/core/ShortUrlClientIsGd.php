<?php
/**
 * $Id$
 *
 * API for http://is.gd/ URL shortening service
 *
 * API documentation: http://is.gd/api_info.php
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

require_once('IShortUrlClient.php');

require_once('HttpClient.php');

class ShortUrlClientIsGd implements IShortUrlClient
{
    /**
     * Creates a short URL from input URL
     *
     * @param $input_url input URL
     * @return short URL or false on error
     */
    static function getShortUrl($input_url)
    {
        $url = 'http://is.gd/api.php?longurl='.urlencode($input_url);
        $http = new HttpClient($url);
        $http->setCacheTime(86400); //24 hours

        $res = $http->getBody();

        if (substr($res, 0, 4) == 'http') return trim($res);
        echo 'Error: '.$res;
        return false;
    }

    function getUrl($url)
    {

    }

}

?>
