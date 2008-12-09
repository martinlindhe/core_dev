<?php
/**
 * $Id$
 *
 * Helper functions to convert different coordinate systems
 * and syntaxes to the internal format: WGS84 in degrees
 *
 * WGS84 coordinates is also used in
 * service_mlp.php
 * service_googlemaps.php
 *
 * World Geodetic System (WGS84):
 * http://en.wikipedia.org/wiki/WGS84
 *
 * Converters:
 * http://latlong.mellifica.se/ (SWEREF 99, RT 90 to WGS84)
 * http://franson.com/coordtrans/ (commerical product)
 *
 * @author Martin Lindhe, 2008 <martin@startwars.org>
 */

/**
 * Converts WGS84 degrees, minutes and seconds (D'M'S)
 * to WGS84 Longitude and Latitude
 *
 * Lat:   62 23 37.00N  ->  62.393611
 * Long: 017 18 28.00E  ->  17.307778
 *
 * @param $coord Geographic coordinate from GPS device or MLP service
 * @return WGS84 coordinate in degrees
 */
//FIXME handle more syntaxes:
//  N62 23 37.00
//  N 62 23 37.00
//	N 58° 41.3862'  (degree, minute)
//	N 58° 41' 23.17" (degree, minute, second)
function gpsToWGS84($coord)	//XXX rename function
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

/**
 * Converts RT 90 coordinates to WGS84
 * http://sv.wikipedia.org/wiki/RT_90
 *
 * @note RT 90 is used in Sweden, but is superseded by SWEREF 99
 */
function RT90toWGS84($x, $y)
{
	global $coords;

	//GRS 80 ellipsoid, projection "rt90_2.5_gon_v"
	$coords['axis'] = 6378137.0; // GRS 80
	$coords['flattening'] = 1.0 / 298.257222101; // GRS 80
	$coords['lat_of_origin'] = 0.0;

	$coords['central_meridian'] = 15.0 + 48.0/60.0 + 22.624306/3600.0;
	$coords['scale'] = 1.00000561024;
	$coords['false_northing'] = -667.711;
	$coords['false_easting'] = 1500064.274;

	return grid_to_geodetic($x, $y);
}

/**
 * Converts SWEREF 99 coordinates to WGS84
 * http://sv.wikipedia.org/wiki/Sweref
 *
 * @note SWEREF 99 is used in Sweden and is the current standard
 */
function SWEREF99toWGS84($n, $e)
{
	global $coords;

	//projection "sweref_99_tm"
	$coords['axis'] = 6378137.0; // GRS 80
	$coords['flattening'] = 1.0 / 298.257222101; // GRS 80
	$coords['lat_of_origin'] = 0.0;

	$coords['central_meridian'] = 15.00;
	$coords['scale'] = 0.9996;
	$coords['false_northing'] = 0.0;
	$coords['false_easting'] = 500000.0;

	return grid_to_geodetic($n, $e);
}

/**
 * Conversion from grid coordinates to geodetic coordinates.
 *
 * http://www.lantmateriet.se/templates/LMV_Page.aspx?id=5197
 * based on: http://mellifica.se/geodesi/gausskruger.js
 * "Gauss Conformal Projection (Transverse Mercator), Krügers Formulas"
 */
function grid_to_geodetic($x, $y)
{
	global $coords;

	//Prepare ellipsoid-based stuff
	$e2 = $coords['flattening'] * (2.0 - $coords['flattening']);
	$n  = $coords['flattening'] / (2.0 - $coords['flattening']);
	$a_roof = $coords['axis'] / (1.0 + $n) * (1.0 + $n*$n/4.0 + $n*$n*$n*$n/64.0);

	$delta1 =    $n/2.0      - 2.0*$n*$n/3.0        +  37.0*$n*$n*$n/96.0   - $n*$n*$n*$n/360.0;
	$delta2 = $n*$n/48.0     + $n*$n*$n/15.0        - 437.0*$n*$n*$n*$n/1440.0;
	$delta3 = 17.0*$n*$n*$n/480.0 - 37*$n*$n*$n*$n/840.0;
	$delta4 = 4397.0*$n*$n*$n*$n/161280.0;

	$Astar =   $e2 +       $e2*$e2 +        $e2*$e2*$e2 +       $e2*$e2*$e2*$e2;
	$Bstar =         -(7.0*$e2*$e2 +   17.0*$e2*$e2*$e2 +  30.0*$e2*$e2*$e2*$e2) / 6.0;
	$Cstar =                         (224.0*$e2*$e2*$e2 + 889.0*$e2*$e2*$e2*$e2) / 120.0;
	$Dstar =                                           -(4279.0*$e2*$e2*$e2*$e2) / 1260.0;

	//Convert
	$deg_to_rad = M_PI / 180;
	$lambda_zero = $coords['central_meridian'] * $deg_to_rad;
	$xi  = ($x - $coords['false_northing']) / ($coords['scale'] * $a_roof);
	$eta = ($y - $coords['false_easting'])  / ($coords['scale'] * $a_roof);
	$xi_prim = $xi -
		$delta1*sin(2.0*$xi) * cosh(2.0*$eta) -
		$delta2*sin(4.0*$xi) * cosh(4.0*$eta) -
		$delta3*sin(6.0*$xi) * cosh(6.0*$eta) -
		$delta4*sin(8.0*$xi) * cosh(8.0*$eta);
	$eta_prim = $eta -
		$delta1*cos(2.0*$xi) * sinh(2.0*$eta) -
		$delta2*cos(4.0*$xi) * sinh(4.0*$eta) -
		$delta3*cos(6.0*$xi) * sinh(6.0*$eta) -
		$delta4*cos(8.0*$xi) * sinh(8.0*$eta);

	$phi_star     = asin(sin($xi_prim)   / cosh($eta_prim));
	$delta_lambda = atan(sinh($eta_prim) / cos($xi_prim));

	$lon_radian = $lambda_zero + $delta_lambda;
	$lat_radian =
		$phi_star + sin($phi_star) * cos($phi_star) *
		($Astar +
		 $Bstar*pow(sin($phi_star), 2) +
		 $Cstar*pow(sin($phi_star), 4) +
		 $Dstar*pow(sin($phi_star), 6));

	$lat = $lat_radian * 180.0 / M_PI;
	$lon = $lon_radian * 180.0 / M_PI;
	return array($lat, $lon);
}

?>
