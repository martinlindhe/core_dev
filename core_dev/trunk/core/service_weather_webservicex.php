<?php
/**
 * $Id$
 *
 * Gets current weather for major cities around the world
 * Use client_weather.php for a general API
 *
 * http://www.webservicex.net/WCF/ServiceDetails.aspx?SID=48
 */

require_once('conv_temp.php');
require_once('input_xml.php');

class weather_webservicex
{
	private $client;

	function __construct()
	{
		$this->client = new SoapClient('http://www.webservicex.net/globalweather.asmx?wsdl');
	}

	function getCitiesByCountry($country)
	{
		$params = array(
		'CountryName' => $country
		);

		try {
			$val = $this->client->GetCitiesByCountry($params);
			$xml = $val->GetCitiesByCountryResult;
			if ($xml == 'Data Not Found') {
				echo "webservicex_weather::getCitiesByCountry() not found on country=".$country.dln();
				return false;
			}

			//XXX parse it properly
			d($xml);
			return $xml;

		} catch (Exception $e) {
			echo 'exception: '.$e, "\n";
			echo 'Request header:'.$this->client->__getLastRequestHeaders()."\n";
			echo 'Request: '.$this->client->__getLastRequest()."\n";
			echo 'Response: '. $this->client->__getLastResponse()."\n";
			return false;
		}
	}

	function getWeather($city, $country = '')
	{
		$params = array(
		'CountryName' => $country,
		'CityName'    => $city
		);

		try {
			$val = $this->client->GetWeather($params);
			$xml = $val->GetWeatherResult;

			if ($xml == 'Data Not Found') {
				echo "webservicex_weather::getWeather() not found on city=".$city.", country=".$country.dln();
				return false;
			}

			$x = new xml_input();
			$p = $x->parse($xml);
			if (!$p) return false;

			$celcius = false;
			if (!empty($p['CurrentWeather|Temperature'])) {
				list($farenheit) = explode(' ', $p['CurrentWeather|Temperature']);
				$temp = new temperature();
				$celcius = round($temp->conv('F','C', $farenheit), 1);
			}

			//parse "Sundsvall-Harnosand Flygplats, Sweden (ESNN) 62-32N 017-27E 10M"
			list($location, $rest) = explode('(', $p['CurrentWeather|Location']);
			list($code, $coords) = explode(')', $rest);
			//echo 'CODE: '.$code."\n";   //ESNQ, ESSB, ESNN.... what is this?

			//parse "Jul 28, 2009 - 02:20 PM EDT / 2009.07.28 1820 UTC"
			list($time1, $time2) = explode(' / ', $p['CurrentWeather|Time']);

			//echo "t1: ".$time1."<br>"; echo "t2: ".$time2."<br>";
			$time = strtotime($time1); //FIXME strtotime() dont handle either format

			$res = array(
			'Location'     => trim($location),
			'Coordinates'  => trim($coords), //XXX: convert from "59-21N 017-57E 14M"
			'Time'         => $time,
			'Wind'         =>@$p['CurrentWeather|Wind'],         //XXX: "from the SSE (150 degrees) at 5 MPH (4 KT):0"
			'Visibility'   =>@$p['CurrentWeather|Visibility'],   //XXX: "greater than 7 mile(s):0"
			'SkyConditions'=>@$p['CurrentWeather|SkyConditions'],//see $skyconditions_swe (can be empty)
			'Temperature'  => $celcius
			);

			return $res;

		} catch (Exception $e) {
			echo 'exception: '.$e, "\n";
			echo 'Request header:'.$this->client->__getLastRequestHeaders()."\n";
			echo 'Request: '.$this->client->__getLastRequest()."\n";
			echo 'Response: '. $this->client->__getLastResponse()."\n";
			return false;
		}

	}
}



?>
