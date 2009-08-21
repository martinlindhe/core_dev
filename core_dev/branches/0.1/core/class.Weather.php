<?php
/**
 * $Id$
 *
 * Returns a weather report from the selected place
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('service_weather_webservicex.php');
require_once('class.Cache.php');

class Weather
{
	var $cache_expire = 300; ///< expire time in seconds for local cache

	function report($city, $country = '')
	{
		$city    = strtolower($city);
		$country = strtolower($country);
		$cache = new cache();
		//$cache->debug = true;
		$data = $cache->get('weather_'.$city.'_'.$country);
		if ($data) return unserialize($data);

		$res = webservicex_weather($city, $country);
		$cache->set('weather_'.$city.'_'.$country, serialize($res), $this->cache_expire);
		return $res;
	}
}

?>
