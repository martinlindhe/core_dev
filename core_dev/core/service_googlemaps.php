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
 * \author Martin Lindhe, 2008 <martin@startwars.org>
 */

require_once('input_csv.php');

$config['google_maps']['api_key'] = '';

/**
 * Displays a static map
 *
 * Google Static Maps HTTP API documentation:
 * http://code.google.com/apis/maps/documentation/staticmaps/
 *
 * @param $lat latitude (-90.0 to 90.0)
 * @param $long longitude (-180.0 to 180.0)
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
	if (empty($config['google_maps']['api_key'])) return false;

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


/**
 * Performs a Geocoding lookup from street address
 *
 * Google Geocoding HTTP API documentation:
 * http://code.google.com/apis/maps/documentation/services.html#Geocoding
 *
 * @param $address address to get coordinates for
 * @return coordinates of specified location or false
 */
function googleMapsGeocode($address)
{
	global $config;

	$url = 'http://maps.google.com/maps/geo'.
		'?q='.urlencode(trim($address)).
		'&output=csv'.	//XXX "xml" output format returns prettified street address & more info if needed
		'&key='.$config['google_maps']['api_key'];

	$res = csvParseRow(file_get_contents($url));
	if ($res[0] != 200 || $res[1] == 0) return false;

	$out['x'] = $res[2];
	$out['y'] = $res[3];
	$out['accuracy'] = $res[1];	//0 (worst) to 9 (best)
	return $out;
}

/**
 * Performs a reverse geocoding lookup from coordinates
 *
 * Google Reverse Geocoding API documentation:
 * http://code.google.com/apis/maps/documentation/services.html#ReverseGeocoding
 */
function googleMapsReverseGeocode($lat, $long)
{
	global $config;

	$url = 'http://maps.google.com/maps/geo'.
		'?ll='.$lat.','.$long.
		'&output=csv'.	//XXX "xml" output format returns prettified street address & more info if needed
		'&key='.$config['google_maps']['api_key'];

	$res = csvParseRow(file_get_contents($url));
	if ($res[0] != 200 || $res[1] == 0) return false;

	$out['name'] = $res[2];
	$out['accuracy'] = $res[1];	//0 (worst) to 9 (best)
	return $out;
}

/**
 * Converts GPS coordinates from degrees, minutes and seconds (D'M'S)
 * to WGS84 Longitude and Latitude (Google Maps API format)
 *
 * Lat:   62 23 37.00N  ->  62.393611
 * Long: 017 18 28.00E  ->  17.307778
 *
 * @param $coord Geographic coordinate from GPS device or MLP service
 */
function gpsToWGS84($coord)
{
	$s_coord = explode(' ', trim($coord));
	if (count($s_coord) != 3) return false;

	switch (substr($s_coord[2], -1)) {
		case 'N': case 'E': $sign = 1; break;
		case 'S': case 'W': $sign = -1; break;
		default: return false;
	}

	$s_coord[2] = substr($s_coord[2], 0, -1);

	return round($sign * ($s_coord[0] + ($s_coord[1]/60) + ($s_coord[2]/3600)), 6);
}

?>
