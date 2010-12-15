<?php
/**
 * $Id$
 *
 * Client library to query web services about weather reports
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

require_once('Cache.php');

require_once('WeatherClientYahoo.php');
// require_once('WeatherClientWebservicex.php'); // currently not working, 2010-11-02

class WeatherResult
{
    var $city, $region, $country;

    var $wind_chill, $wind_direction, $wind_speed;

    var $coord_lat, $coord_long;

    var $time;

    var $visibility;
    var $skycond;
    var $celcius;
}

class WeatherClient extends CoreBase
{
    private $cache;

    function __construct()
    {
        $this->cache = new Cache();
        //$this->cache->debug = true;
        $this->cache->setCacheTime(60*60); //default to 60 minute cache
    }

    function setCacheTime($s) { $this->cache->setCacheTime($s); }

    /**
     * @return a WeatherResult object
     */
    function getWeatherReport($city, $country = '')
    {
        $city    = strtolower($city);
        $country = strtolower($country);

        $data = $this->cache->get('weather//'.$city.'/'.$country);
        if ($data)
            return unserialize($data);

//        $client = new WeatherClientWebservicex();
        $client = new WeatherClientYahoo();
        $res = $client->getWeather($city, $country);

        if ($res)
            $this->cache->set('weather//'.$city.'/'.$country, serialize($res));

        return $res;
    }
}

?>
