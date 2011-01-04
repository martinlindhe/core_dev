<?php
/**
 * $Id$
 *
 * Google Reverse Geocoding API documentation:
 * http://code.google.com/apis/maps/documentation/services.html#ReverseGeocoding
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: NOT FINISHED!

require_once('JSON.php');

class GeoLookupClientGoogle extends Coordinate
{
    var $api_key = 'ABQIAAAAZb_xLTALhJppDDNbAvv61RTTk3jw-XtFtPS4v2-kipB51_4ySRQsE9iSridKaiJXVTQ5msdyWyuhRw'; //XXXXX

    function get()
    {
        $url =
        'http://maps.google.com/maps/geo?ll='.$this->latitude.','.$this->longitude.
        '&key='.$this->api_key.'&output=json';

        $json = JSON::decode($url);

        if ($json->Status->code != 200) return false;

        $item = $json->Placemark[0];

        $res = new GeoLookupResult();
        $res->country_code = $item->AddressDetails->Country->CountryNameCode;
        $res->country_name = $item->AddressDetails->Country->CountryName;     ///XXX google returns local name (Sverige, instead of Sweden)

//XXX not returned from google lookup:
//        $res->timezone     = strval($xml->timezone->timezoneId);
//        $res->sunrise      = strval($xml->timezone->sunrise);
//        $res->sunset       = strval($xml->timezone->sunset);
        return $res;
    }

}

?>
