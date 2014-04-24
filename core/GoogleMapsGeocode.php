<?php
/**
 * Google Geocoding HTTP API documentation:
 * http://code.google.com/apis/maps/documentation/services.html#Geocoding
 * http://code.google.com/apis/maps/documentation/geocoding/index.html#GeocodingResponses
 *
 * Google Reverse Geocoding API documentation:
 * http://code.google.com/apis/maps/documentation/services.html#ReverseGeocoding
 *
 * @author Martin Lindhe, 2008-2014 <martin@ubique.se>
 */

// NOTE: uses Geocoding API v3, recommended as of 2014-04-24

// TODO add api key

//XXX: google dont return timezone of geolocation queries

namespace cd;

require_once('Json.php');
require_once('TempStore.php');
require_once('GeoLookupClient.php');

class GeoCodeResult  //XXXX MERGE WITH GeoLookupResult into GeoResult !
{
    var $country;     ///< 2 letter country code
    var $name;        ///< eg. "Motala", "Sweden"
    var $latitude;
    var $longitude;
    var $accuracy;
}

class GoogleMapsGeocode
{
    /**
     * Performs a Geocoding lookup from street address to coordinates
     *
     * @param $address address to get coordinates for
     * @return coordinates & accuracy of specified location or false
     */
    static function geocode($address, $api_key = '')
    {
        $temp = TempStore::getInstance();
        $key = 'googlemaps/geocode//'.$address;

        $data = $temp->get($key);
        if ($data)
            return unserialize($data);

        $url =
        'http://maps.googleapis.com/maps/api/geocode/json'.
        '?sensor=false'.
        ($api_key ? '&key='.$api_key : '').
        '&address='.urlencode(trim($address));

        $json = Json::decode($url);
//d($json);

        if ($json->status != "OK")
            return false;

        $item = $json->results[0];

        $res = new GeoCodeResult();
//        $res->accuracy  = $item->AddressDetails->Accuracy; // 0 (worst) to 9 (best)

        $res->name = $item->formatted_address;

        $res->latitude  = $item->geometry->location->lat;
        $res->longitude = $item->geometry->location->lng;

        $temp->set($key, serialize($res), '1h');
        return $res;
    }


 // TODO add tests, update to v3 of API
    static function reverse($latitude, $longitude, $api_key = '')
    {
        $temp = TempStore::getInstance();
        $key = 'googlemaps/reverse//'.$latitude.'/'.$longitude;

        $data = $temp->get($key);
        if ($data)
            return unserialize($data);

        $url =
        'http://maps.googleapis.com/maps/api/geocode/json'.
        '?sensor=false'.
        ($api_key ? '&key='.$api_key : '').
        '&latlng='.$latitude.','.$longitude;


        $json = Json::decode($url);

        if ($json->status != "OK")
            return false;

        $item = $json->results[0];
//d($item);
        $res = new GeoLookupResult();
        //$res->accuracy     = $item->AddressDetails->Accuracy;
        $res->description  = $item->formatted_address;
        //$res->country_code = $item->AddressDetails->Country->CountryNameCode;
        //$res->country_name = $item->AddressDetails->Country->CountryName;

        $temp->set($key, serialize($res));
        return $res;
    }

}
