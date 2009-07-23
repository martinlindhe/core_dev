<?php
/**
 * $Id$
 *
 * Conversion functions between different systems for measurement of mass
 *
 * Supported systems:
 * - Kilogram (kg)
 * - Pound (lb)
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Temperature_conversion_formulas
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

class mass
{
	function conv($from, $to, $val)
	{
		//XXX: the rounding is neccesary to work around PHP's handling of floats,
		//     or some will return .0000000000001 precision which make testcase fail
		switch ($to) {
		case 'kg': return round($this->toKilo($from, $val), 8);
		case 'lb': return round($this->toPound($from, $val), 8);
		}
		return false;
	}

	function toKilo($from, $val)
	{
		switch ($from) {
		case 'kg': return $val;
		case 'lb': return $val * 0.45359237;
		}
	}

	function toPound($from, $val)
	{
		switch ($from) {
		case 'kg': return $val / 0.45359237;
		case 'lb': return $val;
		}
	}

}

?>
