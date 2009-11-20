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

require_once('class.CoreConverter.php');

class ConvertMass extends CoreConverter
{
	private $scale = array( ///< unit scale to Gram
	'g'  => 1,           //Gram
	'hg' => 100,         //Hectogram
	'kg' => 1000,        //Kilogram
	't'  => 1000000,     //Tonne
	'oz' => 28.349523125,//Ounce (1/16 lb)
	'lb' => 453.59237,   //Pound
	'st' => 6350.29318   //Stone (14 lb)
	);

	private $lookup = array(
	'gram'      => 'g',
	'hecto'     => 'hg',
	'hectogram' => 'hg',
	'kilogram'  => 'kg',
	'kilo'      => 'kg',
	'tonne'     => 't',
	'ounce'     => 'oz',
	'pound'     => 'lb',
	'stone'     => 'st'
	);

	function getShortcode($name)
	{
		$name = strtolower($name);
		if (substr($name, -1) == 's') $name = substr($name, 0, -1);

		if (!empty($this->lookup[$name])) return $this->lookup[$name];
		if (array_search($name, $this->lookup)) return $name;
		return false;
	}

	function conv($from, $to, $val)
	{
		$from = $this->getShortcode($from);
		$to   = $this->getShortcode($to);

		if (!$from || !$to) return false;

		if ($this->precision)
			return round(($val * $this->scale[$from]) / $this->scale[$to], $this->precision);

		return ($val * $this->scale[$from]) / $this->scale[$to];
	}

}

?>
