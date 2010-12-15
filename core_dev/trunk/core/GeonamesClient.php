<?php
/**
 * $Id$
 *
 * Allows simple searching of data for a coordinate,
 * such as local time, name of current timezone, country.
 *
 * http://www.geonames.org/
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

//XXX TODO 1: refactor into a "GeoLookup" class that uses "GeonamesClient"
//XXX TODO 2: refactor parts from service_googlemaps to do a google geocoding api lookup class

require_once('Cache.php');

class GeonamesResult
{
    var $country_code;
    var $country_name;
    var $timezone;
    var $sunrise;
    var $sunset;
}

class GeonamesClient ///< XXX extend from Coordinate ?
{
    private $lat, $long;

    function __construct($lat, $long)
    {
        $this->lat  = $lat;
        $this->long = $long;
    }

    function get()
    {
        if (!$this->lat || !$this->long)
            throw new Exception ('no coords set');

        $cache = new Cache();
        $cache->setCacheTime(60*60*24); //24h

        $key = 'geonames.org//'.$this->lat.'/'.$this->long;

        $data = $cache->get($key);
        if ($data)
            return unserialize($data);

        $url = 'http://ws.geonames.org/timezone?lat='.$this->lat.'&lng='.$this->long;
        $data = file_get_contents($url);
        $xml = simplexml_load_string($data);

        $res = new GeonamesResult();
        $res->country_code = strval($xml->timezone->countryCode);
        $res->country_name = strval($xml->timezone->countryName);
        $res->timezone     = strval($xml->timezone->timezoneId);
        $res->sunrise      = strval($xml->timezone->sunrise);
        $res->sunset       = strval($xml->timezone->sunset);

        $cache->set($key, serialize($res));

        return $res;
    }

}

?>
