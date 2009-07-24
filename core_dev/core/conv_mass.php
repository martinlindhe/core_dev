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
	function conv($from, $to, $val)
	{
		//convert to gram for internal representation
		switch (strtolower($from)) {
		case 'gram':     case 'g':  $gram = $val; break;
		case 'kilogram': case 'kg': $gram = $val * 1000; break;
		case 'tonne':    case 't':  $gram = $val * 1000000; break;
		case 'pound':    case 'lb': $gram = $val * 453.59237; break;
		default: return false;
		}

		switch (strtolower($to)) {
		case 'gram':     case 'g':  $res = $gram; break;
		case 'kilogram': case 'kg': $res = $gram / 1000; break;
		case 'tonne':    case 't':  $res = $gram / 1000000; break;
		case 'pound':    case 'lb': $res = $gram / 453.59237; break;
		default: return false;
		}

		//XXX: the rounding is neccesary to work around PHP's handling of floats,
		//     or some will return .0000000000001 precision which make testcase fail
		return round($res, 8);
	}
}

?>
