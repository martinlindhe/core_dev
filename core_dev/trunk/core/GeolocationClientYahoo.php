<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: only returns woeid

//SIMPLIFY: use simplexml

//TODO: cache lookups

//TODO: update to parse all data such as location

require_once('JSON.php');

class GeolocationClientYahooResult
{
    var $woeid; ///< Yahoo woeid for location
}

class GeolocationClientYahoo
{
    private $reader; /// XMLReader
    private $items = array();

    function get($city, $country)
    {
        $q = urlencode('select * from geo.places where text="'.$city.','.$country.'"');
        $url = 'http://query.yahooapis.com/v1/public/yql?q='.$q.'&format=json';

        $x = JSON::decode($url);

        $res = new GeolocationClientYahooResult();
        $res->woeid = $x->query->results->place[0]->woeid;
        return $res;

    }
}

?>
