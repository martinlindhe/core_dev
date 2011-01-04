<?php
/**
 * $Id$
 *
 * Allows simple searching of data for a coordinate,
 * such as local time, name of current timezone, country.
 *
 * http://www.geonames.org/
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('Coordinate.php');
require_once('Cache.php');

require_once('GeoLookupClient.php'); // for GeoLookupResult

class GeoLookupClientGeonames extends Coordinate
{
    function get()
    {
        if (!$this->latitude || !$this->longitude)
            throw new Exception ('no coords set');

        $cache = new Cache();
        $cache->setCacheTime(60*60*24); //24h

        $key = 'geonames.org//'.$this->latitude.'/'.$this->longitude;

        $data = $cache->get($key);
        if ($data)
            return unserialize($data);

        $url = 'http://ws.geonames.org/timezone?lat='.$this->latitude.'&lng='.$this->longitude;
        $data = file_get_contents($url);
        $xml = simplexml_load_string($data);

        $res = new GeoLookupResult();
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
