<?php
/**
 * $Id$
 *
 * Gets current weather for major cities around the world
 * Use client_weather.php for a general API
 *
 * http://www.webservicex.net/WCF/ServiceDetails.aspx?SID=48
 */

//STATUS: wip, need standard api hiding multiple weather services (like with tinyurl services)

//STATUS: broken & need custom xml parser:

/*
<?xml version="1.0" encoding="utf-16"?>
<CurrentWeather>
  <Location>Stockholm / Bromma, Sweden (ESSB) 59-21N 017-57E 14M</Location>
  <Time>Jan 07, 2010 - 10:50 AM EST / 2010.01.07 1550 UTC</Time>
  <Wind> from the NW (320 degrees) at 9 MPH (8 KT):0</Wind>
  <Visibility> 5 mile(s):0</Visibility>
  <SkyConditions> mostly cloudy</SkyConditions>
  <Temperature> 14 F (-10 C)</Temperature>
  <Wind>Windchill: 1 F (-17 C):1</Wind>
  <DewPoint> 12 F (-11 C)</DewPoint>
  <RelativeHumidity> 92%</RelativeHumidity>
  <Pressure> 29.94 in. Hg (1014 hPa)</Pressure>
  <Status>Success</Status>
</CurrentWeather>
*/


require_once('conv_temperature.php');
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
die('XXX FIXME: xml_input() dont parse weather dataproperly! replace with custom parser');
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
