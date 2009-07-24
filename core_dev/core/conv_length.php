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
		if (empty($this->scale[$from]) || empty($this->scale[$to])) return false;

		$res = ($val * $this->scale[$from]) / $this->scale[$to];

		//XXX: rounding is neccesary to work around PHP's handling of floats,
		//     or some will return .0000000000001 precision which make tests fail
		return round($res, 8);
	}
}

?>
