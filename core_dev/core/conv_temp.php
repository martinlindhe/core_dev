<?php
/**
 * $Id$
 *
 * Conversion functions for different units of temperature
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Temperature_conversion_formulas
 * http://en.wikipedia.org/wiki/Conversion_of_units#Temperature
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

class temp
{
	function conv($from, $to, $val)
	{
		//convert to celcius for internal representation
		switch (strtolower($from)) {
		case 'celcius':   case 'c': $cel =  $val; break;
		case 'farenheit': case 'f': $cel = ($val - 32) * (5/9); break;
		case 'rakine':    case 'r': $cel = ($val - 491.67) * (5/9); break;
		case 'kelvin':    case 'k': $cel =  $val - 273.15; break;
		default: return false;
		}

		switch (strtolower($to)) {
		case 'celcius':   case 'c': $res =  $cel; break;
		case 'farenheit': case 'f': $res = ($cel * (9/5)) + 32; break;
		case 'rakine':    case 'r': $res = ($cel + 273.15) * (9/5); break;
		case 'kelvin':    case 'k': $res =  $cel + 273.15; break;
		default: return false;
		}

		//XXX: rounding is neccesary to work around PHP's handling of floats,
		//     or some will return .0000000000001 precision which make tests fail
		return round($res, 8);
	}
}

?>
