<?php
/**
 * Official documentation:
 * http://code.google.com/apis/maps/
 *
 * Google Maps API wiki:
 * http://mapki.com/
 *
 * Google Static Maps HTTP API documentation:
 * http://code.google.com/apis/maps/documentation/staticmaps/
 *
 * @author Martin Lindhe, 2008-2014 <martin@ubique.se>
 */

//STATUS: wip

namespace cd;

require_once('Json.php');
require_once('TempStore.php');

class GoogleMapsStatic
{
    /**
     * Creates a link to a static map as a image resource
     *
     * @param $lat latitude (-90.0 to 90.0) horizontal
     * @param $long longitude (-180.0 to 180.0) vertical
     * @param $width up to 640 pixels
     * @param $height up to 640 pixels
     * @param $zoom 0 (whole world) to 21 (showing buildings)
     * @param $maptype mobile, satellite, terrain, hybrid
     * @param $format png8, png32, jpg, jpg-baseline or gif
     * @return URL to static map or false
     */
    static function staticMap($lat, $long, $markers = array(), $path = array(), $width = 512, $height = 512, $zoom = 14, $maptype = 'mobile', $format = 'png8')
    {
        if (!is_numeric($lat) || !is_numeric($long) || !is_numeric($width) || !is_numeric($height))
            throw new \Exception ('bad input');

        if ($lat < -90.0 || $lat > 90.0 || $long < -180.0 || $long > 180.0)
            throw new \Exception ('odd coords');

        if ($width < 0 || $width > 640 || $height < 0 || $height > 640)
            throw new \Exception ('odd sizes');

        if ($zoom < 0 || $zoom > 21)
            throw new \Exception ('odd zoom');

        $url =
        'http://maps.googleapis.com/maps/api/staticmap'.
        '?center='.$lat.','.$long.
        ($zoom == 'auto' ? '' : '&zoom='.$zoom).
        '&size='.$width.'x'.$height.
        '&format='.urlencode($format).
        '&maptype='.urlencode($maptype).
        '&sensor=false';

        $cols = array('red', 'green', 'blue', 'orange', 'purple', 'brown', 'yellow', 'gray', 'black', 'white');

        if (!empty($markers)) {
            $url .= '&markers=';
            for ($i = 0; $i<count($markers); $i++) {
                if ($i == 0) $desc = $cols[$i];
                else $desc = 'mid'.$cols[$i];
                $url .= $markers[$i]['x'].','.$markers[$i]['y'].','.$desc.($i+1);
                if ($i < count($markers)-1) $url .= '|';
            }
        }

        $width = array(6,4,2,2,1,1,1,1,1,1,1,1);

        if (!empty($path)) {
            $alpha = 0xA0;
            for ($i = 0; $i<count($path)-1; $i++) {
                $url .= '&path=rgba:0x0000ff'.dechex($alpha).',weight:'.$width[$i].
                    '|'.$path[$i]['x'].','.$path[$i]['y'].
                    '|'.$path[$i+1]['x'].','.$path[$i+1]['y'];
                if ($alpha > 0x40) $alpha -= 0x20;
            }
        }

        return $url;
    }

}
