<?php
/**
 * $Id$
 *
 * !!! For simpler usage, see GeoLookupClient
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
require_once('HttpClient.php');
require_once('TempStore.php');

require_once('GeoLookupClient.php'); // for GeoLookupResult

class GeonamesClient
{
    static function reverse($latitude, $longitude)
    {
        if (!$latitude || !$longitude)
            throw new Exception ('no coords set');

        $temp = TempStore::getInstance();

        $key = 'GeonamesClient//'.$latitude.'/'.$longitude;

        $data = $temp->get($key);
        if ($data)
            return unserialize($data);

        $url = 'http://ws.geonames.org/timezone?lat='.$latitude.'&lng='.$longitude;
        $http = new HttpClient($url);
        $data = $http->getBody();

        $xml = simplexml_load_string($data);
//d($xml);

        $res = new GeoLookupResult();
        $res->country_code = strval($xml->timezone->countryCode);
        $res->country_name = strval($xml->timezone->countryName);
        $res->timezone     = strval($xml->timezone->timezoneId);
        $res->sunrise      = strval($xml->timezone->sunrise);
        $res->sunset       = strval($xml->timezone->sunset);

        $temp->set($key, serialize($res), '30d');

        return $res;
    }

}

?>
