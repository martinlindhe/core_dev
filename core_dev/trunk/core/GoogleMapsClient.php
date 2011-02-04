<?php
/**
 * $Id$
 *
 * Google Reverse Geocoding API documentation:
 * http://code.google.com/apis/maps/documentation/services.html#ReverseGeocoding
 *
 * Google Geocoding HTTP API documentation:
 * http://code.google.com/apis/maps/documentation/services.html#Geocoding
 * http://code.google.com/intl/sv-SE/apis/maps/documentation/geocoding/index.html#GeocodingResponses
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

//XXX: google dont return timezone of geolocation queries

require_once('JSON.php');
require_once('TempStore.php');

class GeoCodeResult
{
    var $latitude;
    var $longitude;
    var $accuracy;
}

class GoogleMapsClient
{
    static $api_key = 'ABQIAAAAZb_xLTALhJppDDNbAvv61RTTk3jw-XtFtPS4v2-kipB51_4ySRQsE9iSridKaiJXVTQ5msdyWyuhRw'; //XXXXX

    static function reverse($latitude, $longitude)
    {
        $temp = TempStore::getInstance();
        $key = 'googlemaps/reverse//'.$latitude.'/'.$longitude;

        $data = $temp->get($key);
        if ($data)
            return unserialize($data);

        $url =
        'http://maps.google.com/maps/geo?ll='.$latitude.','.$longitude.
        '&key='.self::$api_key.'&output=json'; //XXX "output=xml" returns prettified street address & more info if needed

        $json = JSON::decode($url);
//d($json);
        if ($json->Status->code != 200) return false;

        $item = $json->Placemark[0];

        $res = new GeoLookupResult();
        $res->accuracy     = $item->AddressDetails->Accuracy;
        $res->description  = $item->address;
        $res->country_code = $item->AddressDetails->Country->CountryNameCode;
        $res->country_name = $item->AddressDetails->Country->CountryName;     ///XXX google returns local name (Sverige, instead of Sweden)

//XXX not returned from google lookup:
//        $res->timezone     = strval($xml->timezone->timezoneId);
//        $res->sunrise      = strval($xml->timezone->sunrise);
//        $res->sunset       = strval($xml->timezone->sunset);

        $temp->set($key, serialize($res));
        return $res;
    }

    /**
     * Performs a Geocoding lookup from street address to coordinates
     *
     * @param $address address to get coordinates for
     * @return coordinates & accuracy of specified location or false
     */
    static function geocode($address)
    {
        $url = 'http://maps.google.com/maps/geo'.
            '?q='.urlencode(trim($address)).
            '&output=json'.    //XXX "output=xml" returns prettified street address & more info if needed
            '&key='.self::$api_key;
/*
        $res = csvParseRow(file_get_contents($url));
        if ($res[0] != 200 || $res[1] == 0) return false;
*/

        $json = JSON::decode($url);
//d($json);
        if ($json->Status->code != 200) return false;

        $item = $json->Placemark[0];

        $res = new GeoCodeResult();
        $res->latitude  = $item->Point->coordinates[1];
        $res->longitude = $item->Point->coordinates[0];
        $res->accuracy  = $item->AddressDetails->Accuracy; // 0 (worst) to 9 (best)

        return $res;
    }

}

?>
