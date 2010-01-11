<?php
/**
 * $Id$
 *
 * Gets current weather for major cities around the world
 * Use client_weather.php for a general API
 *
 * http://www.webservicex.net/WCF/ServiceDetails.aspx?SID=48
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

//STATUS: ok, need testing

require_once('conv_temperature.php');
require_once('input_xml.php');

class weather_webservicex extends CoreBase
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
			$data = $val->GetWeatherResult;

			if ($data == 'Data Not Found') {
				echo "webservicex_weather::getWeather() not found on city=".$city.", country=".$country.dln();
				return false;
			}


			$reader = new XMLReader();
			if ($this->debug) echo 'Parsing ASX: '.$data.ln();

			//XXX ugly hack: data is returned marked as utf-16 but is utf-8 (???)
			$data = str_replace('utf-16', 'utf-8', $data);

			$reader->xml($data);

			$celcius = false;

			while ($reader->read())
			{
				if ($reader->nodeType != XMLReader::ELEMENT)
					continue;

				switch ($reader->name) {
				case 'CurrentWeather':
					while ($reader->read()) {
						if ($reader->nodeType != XMLReader::ELEMENT)
							continue;

						switch ($reader->name) {
						case 'CurrentWeather': break;
						case 'Temperature': //<Temperature> 14 F (-10 C)</Temperature>
							$reader->read();
							list($farenheit) = explode(' ', trim($reader->value), 2);
							$temp = new ConvertTemperature();
							$celcius = round($temp->conv('F','C', $farenheit), 1);
							break;

						case 'Location': //<Location>Stockholm / Bromma, Sweden (ESSB) 59-21N 017-57E 14M</Location>
							$reader->read();
							list($location, $rest) = explode('(', $reader->value);
							list($code, $coords) = explode(')', $rest); //XXX: convert from "59-21N 017-57E 14M"
							//echo 'CODE: '.$code."\n";   //ESNQ, ESSB, ESNN.... what is this?
							break;

						case 'Time': //<Time>Jan 07, 2010 - 10:50 AM EST / 2010.01.07 1550 UTC</Time>
							$reader->read();
							list($time1, $time2) = explode(' / ', $reader->value);
							//echo "t1: ".$time1."<br>"; echo "t2: ".$time2."<br>";
							$time = strtotime($time1); //FIXME strtotime() dont handle either format
							break;

						case 'Wind': //<Wind> from the NW (320 degrees) at 9 MPH (8 KT):0</Wind>
							$reader->read();
							$wind = $reader->value; //XXX: parse string
							break;

						case 'Visibility': //<Visibility> 5 mile(s):0</Visibility>
							$reader->read();
							$visibility = $reader->value; //XXX: parse string
							break;

						case 'SkyConditions': //<SkyConditions> mostly cloudy</SkyConditions>
							$reader->read();
							$skycond = trim($reader->value);
							break;

						case 'DewPoint': break; //<DewPoint> 12 F (-11 C)</DewPoint>
						case 'RelativeHumidity': break; //<RelativeHumidity> 92%</RelativeHumidity>
						case 'Pressure': break; //<Pressure> 29.94 in. Hg (1014 hPa)</Pressure>
						case 'Status': break; //<Status>Success</Status>

						default:
							echo "bad entry " .$reader->name.ln();
						}
					}
					break;
				default:
					echo "unknown ".$reader->name.ln();
					break;
				}
			}

			$res = array(
			'Location'     => trim($location),
			'Coordinates'  => trim($coords),
			'Time'         => $time,
			'Wind'         => $wind,
			'Visibility'   => $visibility,
			'SkyConditions'=> $skycond,
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
