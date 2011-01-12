<?php
/**
 * $Id$
 *
 * API for http://tinyurl.com/ URL shortening service
 *
 * API documentation: http://fyneworks.blogspot.com/2008/08/tiny-url-api.html
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

require_once('IShortUrlClient.php');

require_once('HttpClient.php');

class ShortUrlClientTinyUrl implements IShortUrlClient
{
    /**
     * Creates a short URL from input URL
     *
     * @param $url input URL
     * @return short URL or false on error
     */
    static function getShortUrl($url)
    {
        $url = 'http://tinyurl.com/api-create.php?url='.urlencode($url);
        $http = new HttpClient($url);
        $http->setCacheTime(86400); //24 hours

        $res = $http->getBody();

        if (substr($res, 0, 4) == 'http') return trim($res);

        list($error_code, $error_message) = explode('|', $res);
        echo 'Error: '.$error_message.' ('.$error_code.')';
        return false;
    }
}

?>
