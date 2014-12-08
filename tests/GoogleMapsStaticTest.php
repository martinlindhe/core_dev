<?php

namespace cd;

class GoogleMapsStaticTest extends \PHPUnit_Framework_TestCase
{
    public function testGeocode2()
    {
        $url = GoogleMapsStatic::staticMap(59.2542869, 18.029657);

        $this->assertEquals(
            $url,
            'http://maps.googleapis.com/maps/api/staticmap?center=59.2542869,18.029657&zoom=14&size=512x512&format=png8&maptype=mobile&sensor=false'
        );
    }
}



/*
$path[0]['x'] = gpsToWGS84('62 23 37.00N');
$path[0]['y'] = gpsToWGS84('017 18 28.00E');

$path[1]['x'] = gpsToWGS84('62 23 37.10N');
$path[1]['y'] = gpsToWGS84('017 18 35.50E');

$path[2]['x'] = gpsToWGS84('62 23 34.10N');
$path[2]['y'] = gpsToWGS84('017 18 25.50E');

$path[3]['x'] = gpsToWGS84('62 23 32.10N');
$path[3]['y'] = gpsToWGS84('017 18 38.50E');

$path[4]['x'] = gpsToWGS84('62 23 42.10N');
$path[4]['y'] = gpsToWGS84('017 18 50.50E');


echo GoogleMapsStatic::staticMap($path[0]['x'], $path[0]['y'], $path, $path, 512, 512, 15);
*/
