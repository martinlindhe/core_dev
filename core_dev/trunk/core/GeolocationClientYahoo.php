<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: cache lookups

//TODO: update to parse all data such as location

//TODO LATER: when/if yahoo adds timezone to result, stop using GeonamesClient for that

require_once('JSON.php');
require_once('Coordinate.php');

require_once('GeonamesClient.php'); // to lookup timezone for location

class GeolocationClientYahooResult
{
    var $name;     ///< name of location
    var $country;  ///< 2-letter country code (SE=Sweden)
    var $woeid;    ///< Yahoo woeid for location
    var $area;     ///< holds multiple coordinates
    var $timezone;
}

class GeolocationClientYahoo
{
    function get($place, $country = '')
    {
        $text = $country ? ($place.','.$country) : $place;

        $q = urlencode('select * from geo.places where text="'.$text.'"');
        $url = 'http://query.yahooapis.com/v1/public/yql?q='.$q.'&format=json';

        $x = JSON::decode($url);

        //XXX: instead return all results as array of GeolocationClientYahooResult objects?
        if ($x->query->count > 1)
            $item = $x->query->results->place[0];
        else
            $item = $x->query->results->place;

        $res = new GeolocationClientYahooResult();
        $res->name = $item->name;
        $res->country = $item->country->code;

/* XXX TODO: parse admin1, admin2:

admin1: {
    * code: ""
    * type: "County"
    * content: "Jamtland"
}
admin2: {
    * code: ""
    * type: "Municipality"
    * content: "Härjedalen"
}
*/
        $res->woeid = $item->woeid;
        $res->area = new StdClass();
        $res->area->center = new Coordinate($item->centroid->latitude, $item->centroid->longitude);
        $res->area->sw = new Coordinate($item->boundingBox->southWest->latitude, $item->boundingBox->southWest->longitude);
        $res->area->ne = new Coordinate($item->boundingBox->northEast->latitude, $item->boundingBox->northEast->longitude);


        //XXX this is a ugly hack until yahoo returns timezone with their response
        $c = new GeonamesClient($item->centroid->latitude, $item->centroid->longitude);
        $geoname = $c->get();
        $res->timezone = $geoname->timezone;

        return $res;
    }
}

?>
