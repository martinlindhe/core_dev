<?php
/**
 * $Id$
 *
 * Helper functions to convert different coordinate syntaxes
 * to the internal format (WGS84)
 *
 * WGS84 coordinates is also used in
 * service_mlp.php
 * service_googlemaps.php
 *
 * World Geodetic System (WGS84):
 * http://en.wikipedia.org/wiki/WGS84
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * Converts GPS coordinates from degrees, minutes and seconds (D'M'S)
 * to WGS84 Longitude and Latitude
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
