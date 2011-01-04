<?php
/**
 * $Id$
 *
 * Looks up a set of coordinates and return normalized data such as
 * location name and local timezone.
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('Coordinate.php');
require_once('GeoLookupClientGeonames.php');
require_once('GeoLookupClientGoogle.php');

class GeoLookupResult
{
    var $country_code;  ///< 2 letter code, such as "SE"
    var $country_name;
    var $timezone;
    var $sunrise;
    var $sunset;
}

class GeoLookupClient extends Coordinate
{
    //XXX: the called classes returns the first result rather than an array of all results..
    function get()
    {
        $cli = new GeoLookupClientGeonames($this->latitude, $this->longitude);

//        $cli = new GeoLookupClientGoogle($this->latitude, $this->longitude);  //XXX not finished
        return $cli->get();
    }
}

?>
