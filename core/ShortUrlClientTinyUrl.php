<?php
/**
 * API for http://tinyurl.com/ URL shortening service
 *
 * API documentation:
 * http://fyneworks.blogspot.com/2008/08/tiny-url-api.html
 *
 * @author Martin Lindhe, 2009-2014 <martin@ubique.se>
 */

//STATUS: works 2014-04-25

namespace cd;

require_once('IShortUrlClient.php');
require_once('HttpClient.php');

class ShortUrlClientTinyUrl implements IShortUrlClient
{
    static function shorten($url)
    {
        $url = 'http://tinyurl.com/api-create.php?url='.urlencode($url);
        $http = new HttpClient($url);
        $http->setCacheTime(86400); //24 hours

        $res = $http->getBody();

        if (substr($res, 0, 4) == 'http')
            return trim($res);

        list($error_code, $error_message) = explode('|', $res);

        throw new \Exception ('Error: '.$error_message.' ('.$error_code.')');
    }

}
