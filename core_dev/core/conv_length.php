<?php
/**
 * $Id$
 *
 * Conversion functions for different units of length
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Conversion_of_units#Length
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

class length
{
	var $precision = 0; ///< if set, specifies rounding precision

	var $scale = array( ///< unit scale to Meter
	'pm'     => 0.000000000001, //Picometer
	'nm'     => 0.000000001,    //Nanometer
	'mm'     => 0.001,          //Millimeter
	'cm'     => 0.01,           //Centimeter
	'dm'     => 0.1,            //Decimeter
	'm'      => 1,              //Meter
	'km'     => 1000,           //Kilometer
	'in'     => 0.0254,         //Inch
	'ft'     => 0.304800610,    //Feet
	'yd'     => 0.9144,         //Yard
	'ukmile' => 1852,           //UK nautical mile
	'usmile' => 1609.344,       //US statute mile
	'au'     => 149597871464    //Astronomical Unit
	);

	function conv($from, $to, $val)
	{
		$from = $this->shortcode($from);
		$to   = $this->shortcode($to);

		if (empty($this->scale[$from]) || empty($this->scale[$to])) return false;

		if ($this->precision)
			return round(($val * $this->scale[$from]) / $this->scale[$to], $this->precision);

		return ($val * $this->scale[$from]) / $this->scale[$to];
	}

	function shortcode($name)
	{
		$lookup = array(
		'picometer'  => 'pm',
		'nanometer'  => 'nm',
		'millimeter' => 'mm',
		'centimeter' => 'cm',
		'decimeter'  => 'dm',
		'meter'      => 'm',
		'kilometer'  => 'km',
		'inch'       => 'in',
		'feet'       => 'ft',
		'yard'       => 'yd'
		);

		$name = strtolower_utf8($name);
		if (!empty($lookup[$name])) return $lookup[$name];
		return $name;
	}
}

?>
