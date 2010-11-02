<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: only returns woeid

//TODO: update to parse all data such as location

class GeolocationClientYahooResult
{
    var $woeid; ///< Yahoo woeid for location
}

class GeolocationClientYahoo
{
    private $reader; /// XMLReader
    private $items = array();

    function get($city, $country)
    {
        $q = urlencode('select * from geo.places where text="'.$city.','.$country.'"');
        $url = 'http://query.yahooapis.com/v1/public/yql?q='.$q.'&format=xml';

        $data = file_get_contents($url);

        $this->reader = new XMLReader();

        $this->reader->xml($data);

        while ($this->reader->read())
        {
            if ($this->reader->nodeType != XMLReader::ELEMENT)
                continue;

            switch ($this->reader->name) {
            case 'query':
                while ($this->reader->read()) {
                    if ($this->reader->nodeType != XMLReader::ELEMENT)
                        continue;

                    switch ($this->reader->name) {
                    case 'results':
                        $this->parseResults();
                        break;

                    default:
                        // echo "GeolocationClientYahoo bad entry " .$reader->name.ln();
                    }
                }
                break;
            default:
                echo "unknown ".$this->reader->name.ln();
                break;
            }
        }

        if (count($this->items) != 1)
            throw new Exception (count($this->items).' location results');

        return $this->items[0];
    }

    private function parseResults()
    {
        $item = new GeolocationClientYahooResult();

        while ($this->reader->read()) {
            if ($this->reader->nodeType == XMLReader::END_ELEMENT && $this->reader->name == 'place') {
                $this->items[] = $item;
                return;
            }

            if ($this->reader->nodeType != XMLReader::ELEMENT)
                continue;

            $key = strtolower($this->reader->name);

            switch ($key) {
            case 'woeid':
                $this->reader->read();
                $item->woeid = $this->reader->value;
                break;

            default:
                //echo 'unknown item entry ' .$this->reader->name.ln();
                break;
            }
        }

    }
}

?>
