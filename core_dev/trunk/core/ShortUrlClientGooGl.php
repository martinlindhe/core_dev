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

//STATUS: works 2011-01-13

namespace cd;

require_once('IShortUrlClient.php');
require_once('HttpClient.php');
require_once('JSON.php');
require_once('TempStore.php');

class ShortUrlClientGooGl implements IShortUrlClient
{
    static function shorten($input_url)
    {
        $temp = TempStore::getInstance();
        $res = $temp->get('goo.gl/'.$input_url);
        if ($res)
            return $res;

        $http = new HttpClient('https://www.googleapis.com/urlshortener/v1/url');

        $http->setContentType('application/json');
        $res = $http->post( JSON::encode( array('longUrl' => $input_url)) );

        $res = JSON::decode($res);

        if (isset($res->error))
            throw new Exception ('Error code '.$res->error->code.': '.$res->error->message);

        $temp->set('goo.gl/'.$input_url, $res->id);

        return $res->id;
    }

}

?>
