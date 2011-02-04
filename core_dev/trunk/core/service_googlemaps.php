<?php
/**
 * $Id$
 *
 * Functions to interact with the Google Maps API
 *
 * Official documentation:
 * http://code.google.com/apis/maps/
 *
 * Google Maps API wiki:
 * http://mapki.com/
 *
 * @author Martin Lindhe, 2008-2011 <martin@startwars.org>
 */

//STATUS: unused, rewrite

/**
 * Displays a static map
 *
 * Google Static Maps HTTP API documentation:
 * http://code.google.com/apis/maps/documentation/staticmaps/
 *
 * @param $lat latitude (-90.0 to 90.0) horizontal
 * @param $long longitude (-180.0 to 180.0) vertical
 * @param $width up to 640 pixels
 * @param $height up to 640 pixels
 * @param $zoom 0 (whole world) to 19 (very detailed view) or "auto" to autozoom
 * @param $maptype mobile, satellite, terrain, hybrid
 * @param $format png8, png32, jpg, jpg-baseline or gif
 * @return URL to static map or false
 */
function googleMapsStaticMap($lat, $long, $markers = array(), $path = array(), $width = 512, $height = 512, $zoom = 14, $maptype = 'mobile', $format = 'png8')
{
    global $config;
    if (!is_numeric($lat) || !is_numeric($long) || !is_numeric($width) || !is_numeric($height)) return false;
    if ($lat < -90.0 || $lat > 90.0 || $long < -180.0 || $long > 180.0) return false;
    if ($width < 0 || $width > 640 || $height < 0 || $height > 640) return false;
    if ((is_numeric($zoom) && ($zoom < 0 || $zoom > 19)) || is_string($zoom) && $zoom != 'auto') return false;
    if (empty($config['google_maps']['api_key'])) die('google maps api_key not set!');

    $url = 'http://maps.google.com/staticmap'.
        '?center='.$lat.','.$long.
        ($zoom == 'auto' ? '' : '&zoom='.$zoom).
        '&size='.$width.'x'.$height.
        '&format='.urlencode($format).
        '&maptype='.urlencode($maptype).
        '&key='.$config['google_maps']['api_key'];

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

?>
