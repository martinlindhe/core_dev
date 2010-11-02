<?php
/**
 * $Id$
 *
 * Client library to query web services about weather reports
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

require_once('Cache.php');
require_once('WeatherClientWebservicex.php');

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

    function getWeatherReport($city, $country = '')
    {
        $city    = strtolower($city);
        $country = strtolower($country);

        $data = $this->cache->get('weather//'.$city.'/'.$country);
        if ($data)
            return unserialize($data);

        $client = new WeatherClientWebservicex();
        $res = $client->getWeather($city, $country);

        if ($res)
            $this->cache->set('weather//'.$city.'/'.$country, serialize($res));

        return $res;
    }
}

?>
