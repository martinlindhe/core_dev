<?php
/**
 * $Id$
 *
 * Methods for querying the query.yahooapis.com service
 *
 * @author Martin Lindhe, 2010-2012 <martin@startwars.org>
 */

//STATUS: wip

namespace cd;

require_once('JSON.php');
require_once('TempStore.php');
require_once('GeonamesClient.php'); // to lookup timezone for location

class YahooGeocodeResult
{
    var $name;     ///< name of location
    var $country;  ///< 2-letter country code (SE=Sweden)
    var $woeid;    ///< Yahoo woeid for location
    var $area;     ///< holds multiple coordinates
    var $timezone;
}

class YahooQueryCoordinate
{
    var $latitude;  // 59.332169 = stockholm,sweden
    var $longitude; // 18.062429 = stockholm,sweden

    function __construct($lat, $long)
    {
        $this->latitude  = $lat;
        $this->longitude = $long;
    }

}

class YahooQueryClient
{
    static function geocode($place, $country = '')
    {
        $text = $country ? ($place.','.$country) : $place;

        $temp = TempStore::getInstance();
        $key = 'YahooQueryClient/geocode//'.$text;

        $data = $temp->get($key);
        if ($data)
            return unserialize($data);

        $q = urlencode('select * from geo.places where text="'.$text.'"');
        $url = 'http://query.yahooapis.com/v1/public/yql?q='.$q.'&format=json';

        $x = JSON::decode($url);

        //XXX: instead return all results as array of YahooGeocodeResult objects?
        if ($x->query->count > 1)
            $item = $x->query->results->place[0];
        else
            $item = $x->query->results->place;

        $res = new YahooGeocodeResult();
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
        $res->area->center = new YahooQueryCoordinate($item->centroid->latitude, $item->centroid->longitude);
        $res->area->sw = new YahooQueryCoordinate($item->boundingBox->southWest->latitude, $item->boundingBox->southWest->longitude);
        $res->area->ne = new YahooQueryCoordinate($item->boundingBox->northEast->latitude, $item->boundingBox->northEast->longitude);


        //XXX this is a ugly hack until yahoo returns timezone with their response
        $geoname = GeonamesClient::reverse($item->centroid->latitude, $item->centroid->longitude);
        $res->timezone = $geoname->timezone;

        $temp->set($key, serialize($res));

        return $res;
    }
}

?>
