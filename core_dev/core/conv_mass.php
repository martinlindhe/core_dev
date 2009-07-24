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
	var $scale = array( ///< unit scale to Gram
	'g'  => 1,        //Gram
	'kg' => 1000,     //Kilogram
	't'  => 1000000,  //Tonne
	'lb' => 453.59237 //Pound
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
