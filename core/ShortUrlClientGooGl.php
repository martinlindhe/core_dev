<?php
/**
 * API for http://goo.gl/ URL shortening service
 *
 * https://developers.google.com/url-shortener/
 *
 * Get API key here: http://code.google.com/apis/console/
 *
 * @author Martin Lindhe, 2011-2014 <martin@ubique.se>
 */

//STATUS: works 2014-04-25

// TODO: add "expand url" method, use https://www.googleapis.com/urlshortener/v1/url?shortUrl=http://goo.gl/fbsS

// TODO: add ability to specify api key! dont be static?

namespace cd;

require_once('IShortUrlClient.php');
require_once('HttpClient.php');
require_once('Json.php');
require_once('TempStore.php');

class ShortUrlClientGooGl implements IShortUrlClient
{
    static function shorten($input_url)
    {
        $temp = TempStore::getInstance();
        $res = $temp->get('goo.gl/'.$input_url);
        if ($res)
            return $res;

        $api_key = '';

        $http = new HttpClient('https://www.googleapis.com/urlshortener/v1/url');

        $http->setContentType('application/json');

        $res = $http->post( Json::encode( array('longUrl' => $input_url, 'key' => $api_key) ) );

        $res = Json::decode($res);

        if (isset($res->error)) {
            d($res->error->errors);
            throw new \Exception ('Error code '.$res->error->code.': '.$res->error->message);
        }

        $temp->set('goo.gl/'.$input_url, $res->id);

        return $res->id;
    }

}
