<?php
/**
 * $Id$
 *
 * Gets current weather for major cities around the world
 * http://www.webservicex.net/WCF/ServiceDetails.aspx?SID=48
 */

require_once('input_xml.php');
require_once('class.Cache.php');

define('WEBSERVICEX_WEATHER_API', 'http://www.webservicex.net/globalweather.asmx?wsdl');

function webservicex_weather($city, $country = '')
{
	$client = new SoapClient(WEBSERVICEX_WEATHER_API);

	try {

		$cache = new cache();
		$data = $cache->get('weather_'.$city.'_'.$country);
		if ($data) return unserialize($data);

		$params['CityName']    = $city;
		$params['CountryName'] = $country;
		$val = $client->GetWeather($params);
		$xml = $val->GetWeatherResult;
		if ($xml == 'Data Not Found') {
			echo "webservicex_weather() data fetch failed on city=".$city.", country=".$country."\n";
			return false;
		}

		$x = new xml_input();
		$p = $x->parse($xml);
		if (!$p) return false;

		preg_match('/(?<farenheit>\d+) F \((?<celcius>\d+) C\)/', $p['CurrentWeather|Temperature'], $match);
		if (!empty($match['celcius'])) $p['CurrentWeather|Temperature'] = $match['celcius'].' C';

		$res = array(
		'Location'     =>$p['CurrentWeather|Location'],     //"Stockholm / Bromma, Sweden (ESSB) 59-21N 017-57E 14M"
		'Time'         =>$p['CurrentWeather|Time'],         //
		'Wind'         =>$p['CurrentWeather|Wind'],         //XXX: "from the SSE (150 degrees) at 5 MPH (4 KT):0"
		'SkyConditions'=>$p['CurrentWeather|SkyConditions'],//overcast
		'Temperature'  =>$p['CurrentWeather|Temperature']
		);

		$cache->set('weather_'.$city.'_'.$country, serialize($res), 5*60);
		return $res;

	} catch (Exception $e) {
		echo 'exception: '.$e, "\n";
		echo 'Request header:'.$client->__getLastRequestHeaders()."\n";
		echo 'Request: '.$client->__getLastRequest()."\n";
		echo 'Response: '. $client->__getLastResponse()."\n";
		return false;
	}
}

$x = webservicex_weather('stockholm');

print_r($x);

?>
