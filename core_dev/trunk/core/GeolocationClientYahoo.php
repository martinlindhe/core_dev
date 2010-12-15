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
require_once('Coordinate.php');

class GeolocationClientYahooResult
{
    var $woeid; ///< Yahoo woeid for location
    var $area;  ///< holds multiple coordinates
}

class GeolocationClientYahoo
{
    function get($city, $country)
    {
        $q = urlencode('select * from geo.places where text="'.$city.','.$country.'"');
        $url = 'http://query.yahooapis.com/v1/public/yql?q='.$q.'&format=json';

        $x = JSON::decode($url);
        $item = $x->query->results->place[0];

        $res = new GeolocationClientYahooResult();
        $res->woeid = $item->woeid;
        $res->area = new StdClass();
        $res->area->center = new Coordinate($item->centroid->latitude, $item->centroid->longitude);
        $res->area->sw = new Coordinate($item->boundingBox->southWest->latitude, $item->boundingBox->southWest->longitude);
        $res->area->ne = new Coordinate($item->boundingBox->northEast->latitude, $item->boundingBox->northEast->longitude);

        return $res;
    }
}

?>
