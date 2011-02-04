<?php
/**
 * $Id$
 *
 * Looks up a set of coordinates and return normalized data such as
 * location name, country and local timezone.
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('Coordinate.php');
require_once('GeonamesClient.php');
require_once('GoogleMapsClient.php');
require_once('TempStore.php');

class GeoLookupResult
{
    var $description;   ///< description of the location
    var $country_code;  ///< 2 letter code, such as "SE"
    var $country_name;
    var $timezone;
    var $sunrise;
    var $sunset;

    var $accuracy; ///< numeric 0-9 where 9 is best accuracy
}

class GeoLookupClient extends Coordinate   //XXXX rename class to something like ReverseLookupClient, CoordinateLookupClient ?
{
    function get()
    {
        $temp = TempStore::getInstance();
        $key = 'GeoLookupClient//'.$this->latitude.'/'.$this->longitude;

        $data = $temp->get($key);
        if ($data)
            return unserialize($data);

        $geonames = GeonamesClient::reverse($this->latitude, $this->longitude);

        $google = GoogleMapsClient::reverse($this->latitude, $this->longitude);

        // return a combination of data from both sources because neither returns all we want alone
        $res = new GeoLookupResult();
        $res->description  = $google->description;
        $res->accuracy     = $google->accuracy;
        $res->country_code = $geonames->country_code;
        $res->country_name = $geonames->country_name;
        $res->timezone     = $geonames->timezone;
        $res->sunrise      = $geonames->sunrise;
        $res->sunset       = $geonames->sunset;

        $temp->set($key, serialize($res));

        return $res;
    }
}

?>
