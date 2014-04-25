<?php
/**
 * API for http://bit.ly/ URL shortening service
 *
 * http://dev.bitly.com/
 *
 * @author Martin Lindhe, 2011-2014 <martin@ubique.se>
 */

//STATUS: works 2011-01-13

//TODO remove api key!

namespace cd;

require_once('IShortUrlClient.php');
require_once('HttpClient.php');
require_once('Json.php');

class ShortUrlClientBitLy implements IShortUrlClient
{
    static function shorten($input_url)
    {
/*
        $login = 'bitlyapidemo';
        $key = 'R_0da49e0a9118ff35f52f629d2d71bf07';
*/
        $login = 'martinunicorn';
        $key = 'R_f37747e06a18173096b714827c76b567';

        $url = 'http://api.bit.ly/v3/shorten?format=json&login='.$login.'&apiKey='.$key.'&longUrl='.urlencode($input_url);
        $http = new HttpClient($url);
        $http->setCacheTime(86400); //24 hours

        $res = Json::decode( $http->getBody() );

        if ($res->status_code != 200)
            throw new \Exception ('Error code '.$res->status_code.': '.$res->status_txt);

        return $res->data->url;
    }

}
