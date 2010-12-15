<?php
/**
 * $Id$
 *
 * References
 * ----------
 * http://developer.yahoo.com/weather/
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

require_once('RssReader.php');

require_once('GeolocationClientYahoo.php');

class WeatherClientYahoo extends RssReader
{
    var $city, $region, $country;
    var $wind_chill, $wind_direction, $wind_speed;
    var $skycond, $celcius, $time;
    var $visibility;
    var $coord_lat, $coord_long;

    function __construct($data = '')
    {
        $this->ext_tags = array(
        'yweather:location', 'yweather:units', 'yweather:wind', 'yweather:atmosphere', 'yweather:astronomy',
        'yweather:condition', 'yweather:forecast',
        'geo:lat', 'geo:long'
        );

        parent::__construct($data);
    }

    function pluginParseTag($key)
    {
        switch ($key) {
        // <yweather:location city="Jonkoping" region=""   country="Sweden"/>
        case 'yweather:location':
            $this->city    = $this->reader->getAttribute('city');
            $this->region  = $this->reader->getAttribute('region');
            $this->country = $this->reader->getAttribute('country');
            break;

        // <yweather:units temperature="C" distance="km" pressure="mb" speed="km/h"/>
        case 'yweather:units':
            break; //XXXXX PARSE!

        // <yweather:wind chill="4"   direction="190"   speed="27.36" />
        case 'yweather:wind':
            //XXX what is the unit types?
            $this->wind_chill     = $this->reader->getAttribute('chill');
            $this->wind_direction = $this->reader->getAttribute('direction');
            $this->wind_speed     = $this->reader->getAttribute('speed');
            break;

        // <yweather:atmosphere humidity="93"  visibility="9.99"  pressure="982.05"  rising="0" />
        case 'yweather:atmosphere':
            $this->visibility = $this->reader->getAttribute('visibility');
            break; //XXXXX PARSE!

        // <yweather:astronomy sunrise="7:18 am"   sunset="4:15 pm"/>
        case 'yweather:astronomy':
            break; //XXXXX PARSE!

        // <yweather:condition  text="Light Rain"  code="11"  temp="8"  date="Tue, 02 Nov 2010 8:50 pm CET" />
        case 'yweather:condition':
            // XXX: code=11 ????
            $this->skycond = $this->reader->getAttribute('text');
            $this->celcius = $this->reader->getAttribute('temp');
            $this->time    = strtotime($this->reader->getAttribute('date'));
            break;

        // <yweather:forecast day="Tue" date="2 Nov 2010" low="6" high="7" text="Rain" code="12" />
        // <yweather:forecast day="Wed" date="3 Nov 2010" low="4" high="8" text="Rain/Wind" code="12" />
        case 'yweather:forecast':
            break;

        // <geo:lat>57.78</geo:lat>
        case 'geo:lat':
            $this->coord_lat = $this->reader->readValue();
            break;

        // <geo:long>14.18</geo:long>
        case 'geo:long':
            $this->coord_long = $this->reader->readValue();
            break;

        default:
            echo 'xxxxx '.$key."\n";
            break;
        }

    }

    function getWeather($city, $country = '')
    {
        $loc_lookup = new GeolocationClientYahoo();
        $x = $loc_lookup->get($city, $country);

        if (!$x->woeid)
            throw new Exception ('location not found');

        $url = 'http://weather.yahooapis.com/forecastrss?w='.$x->woeid.'&u=c';

        $this->parse($url);
        $items = $this->getItems();

        if (count($items) != 1)
            throw new Exception ('unexpected number of results');

        $res = new WeatherResult();
        $res->city            = $this->city;
        $res->region          = $this->region;
        $res->country         = $this->country;

        //XXXX what is the unit types?
        $res->wind_chill      = $this->wind_chill;
        $res->wind_direction  = $this->wind_direction;
        $res->wind_speed      = $this->wind_speed;

        $res->coord_lat       = $this->coord_lat;
        $res->coord_long      = $this->coord_long;
        $res->time            = $this->time;
        $res->visibility      = $this->visibility;
        $res->skycond         = $this->skycond;
        $res->celcius         = $this->celcius;

        return $res;
    }
}

?>
