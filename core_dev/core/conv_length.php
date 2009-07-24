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
	function conv($from, $to, $val)
	{
		//convert to meter for internal representation
		switch (strtolower($from)) {
		case 'picometer':    case 'pm': $meter = $val * 0.000000000001; break; //1 trillionth meter
		case 'nanometer':    case 'nm': $meter = $val * 0.000000001; break; //1 millionth meter
		case 'millimeter':   case 'mm': $meter = $val * 0.001; break;
		case 'centimeter':   case 'cm': $meter = $val * 0.01; break;
		case 'decimeter':    case 'dm': $meter = $val * 0.1; break;
		case 'meter':        case 'm':  $meter = $val * 1; break;
		case 'kilometer':    case 'km': $meter = $val * 1000; break;

		case 'feet':         case 'ft': $meter = $val * 0.304800610; break;
		case 'inch':         case 'in': $meter = $val * 0.0254; break;
		case 'yard':         case 'yd': $meter = $val * 0.9144; break;
		case 'ukmile':                  $meter = $val * 1852; break; //UK nautical mile
		case 'usmile':                  $meter = $val * 1609.344; break; //US statute mile

		case 'astronomical': case 'au': $meter = $val * 149597871464; break;
		default: return false;
		}

		switch (strtolower($to)) {
		case 'picometer':    case 'pm': $res = $meter / 0.000000000001; break;
		case 'nanometer':    case 'nm': $res = $meter / 0.000000001; break;
		case 'millimeter':   case 'mm': $res = $meter / 0.001; break;
		case 'centimeter':   case 'cm': $res = $meter / 0.01; break;
		case 'decimeter':    case 'dm': $res = $meter / 0.1; break;
		case 'meter':        case 'm':  $res = $meter / 1; break;
		case 'kilometer':    case 'km': $res = $meter / 1000; break;

		case 'feet':         case 'ft': $res = $meter / 0.304800610; break;
		case 'inch':         case 'in': $res = $meter / 0.0254; break;
		case 'yard':         case 'yd': $res = $meter / 0.9144; break;
		case 'ukmile':                  $res = $meter / 1852; break;
		case 'usmile':                  $res = $meter / 1609.344; break;

		case 'astronomical': case 'au': $res = $meter / 149597871464; break;
		default: return false;
		}

//		echo $val. " ".$from." to ".$to.": ".$meter." meter -> ".$res." ".$to."\n";

		return round($res, 8);
	}
}

?>
