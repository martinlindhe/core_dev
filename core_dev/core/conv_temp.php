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
	var $precision = 2; ///< if set, specifies rounding precision

	function conv($from, $to, $val)
	{
		$from = $this->shortcode($from);
		$to   = $this->shortcode($to);

		//convert to celcius for internal representation
		switch (strtolower($from)) {
		case 'c': $cel =  $val; break;
		case 'f': $cel = ($val - 32) * (5/9); break;
		case 'r': $cel = ($val - 491.67) * (5/9); break;
		case 'k': $cel =  $val - 273.15; break;
		default: return false;
		}

		switch (strtolower($to)) {
		case 'c': $res =  $cel; break;
		case 'f': $res = ($cel * (9/5)) + 32; break;
		case 'r': $res = ($cel + 273.15) * (9/5); break;
		case 'k': $res =  $cel + 273.15; break;
		default: return false;
		}

		if ($this->precision)
			return round($res, $this->precision);

		return $res;
	}

	function shortcode($name)
	{
		$lookup = array(
		'celcius'   => 'c',
		'farenheit' => 'f',
		'rakine'    => 'r',
		'kelvin'    => 'k',
		);

		$name = strtolower($name);
		if (!empty($lookup[$name])) return $lookup[$name];
		if (array_search($name, $lookup)) return $name;
		return false;
	}

}

?>
