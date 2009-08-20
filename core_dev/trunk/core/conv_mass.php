<?php
/**
 * $Id$
 *
 * Conversion functions between different units of mass
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Conversion_of_units#Mass
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

class mass
{
	var $precision = 0; ///< if set, specifies rounding precision

	var $scale = array( ///< unit scale to Gram
	'g'  => 1,           //Gram
	'kg' => 1000,        //Kilogram
	't'  => 1000000,     //Tonne
	'lb' => 453.59237,   //Pound
	'oz' => 28.349523125 //Ounce (/16 lb)
	);

	function conv($from, $to, $val)
	{
		$from = $this->shortcode($from);
		$to   = $this->shortcode($to);

		if (!$from || !$to) return false;

		if ($this->precision)
			return round(($val * $this->scale[$from]) / $this->scale[$to], $this->precision);

		return ($val * $this->scale[$from]) / $this->scale[$to];
	}

	function shortcode($name)
	{
		$name = strtolower($name);
		if (substr($name, -1) == 's') $name = substr($name, 0, -1);

		$lookup = array(
		'gram'     => 'g',
		'kilogram' => 'kg',
		'kilo'     => 'kg',
		'tonne'    => 't',
		'pound'    => 'lb',
		'ounce'    => 'oz'
		);

		if (!empty($lookup[$name])) return $lookup[$name];
		if (array_search($name, $lookup)) return $name;
		return false;
	}
}

?>
