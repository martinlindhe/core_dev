<?php
/**
 * $Id$
 *
 * API for http://goo.gl/ URL shortening service
 *
 * To get started, you'll need a free bit.ly user account and apiKey. Signup at: http://bit.ly/a/sign_up
 *
 * API documentation:
 * http://code.google.com/apis/urlshortener/overview.html
 *
 * @author Martin Lindhe, 2011 <martin@startwars.org>
 */

//STATUS: not finished. their api return "500 internal error" at 2011-01-13

//TODO: implement cache of result, because httpclient dont cache a POST request

require_once('IShortUrlClient.php');

require_once('HttpClient.php');
require_once('JSON.php');

class ShortUrlClientGooGl implements IShortUrlClient
{
    static function shorten($input_url)
    {
        $http = new HttpClient('https://www.googleapis.com/urlshortener/v1/url');
        $http->setContentType('application/json');
        $res = $http->post( array('longUrl' => $input_url) );

        $res = JSON::decode($res);

        if ($res->error->code != 200)
            throw new Exception ('Error code '.$res->error->code.': '.$res->error->message);

d($res);
die;

        return $res->data->url;
    }

}

?>
