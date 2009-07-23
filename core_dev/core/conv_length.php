<?php
/**
 * $Id$
 *
 * Conversion functions for different distance systems
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Conversion_of_units#Length
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

class length
{
	//XXX implement
	function conv($from, $to, $val)
	{
		switch ($to) {
		case 'astronomical': case 'au': return $this->toAstronomical($from, $val);

		case 'feet': case 'ft':       return $this->toFeet($from, $val);
		case 'inch': case 'in':       return $this->toInch($from, $val);
		case 'yard': case 'yd':       return $this->toYard($from, $val);
		case 'usmile': case 'mi':     return $this->toMileUS($from, $val); //US statute mile of  1,609.344 meters (5,280 feet)
		case 'ukmile':                return $this->toMileUK($from, $val); //UK nautical mile of 1,852 meters (about 6,076.1 ft)

		case 'picometer':  case 'pm': return $this->toMeter($from, $val) * 1000000000000; //1 trillionth meter
		case 'nanometer':  case 'nm': return $this->toMeter($from, $val) * 1000000; //1 millionth meter
		case 'millimeter': case 'mm': return $this->toMeter($from, $val) * 1000;
		case 'centimeter': case 'cm': return $this->toMeter($from, $val) * 100;
		case 'decimeter':  case 'dm': return $this->toMeter($from, $val) * 10;
		case 'meter':      case 'm':  return $this->toMeter($from, $val);
		case 'kilometer':  case 'km': return $this->toMeter($from, $val) / 1000;

		}
		return false;
	}

	/* a unit of length roughly equal to the mean distance between the Earth and the Sun */
	function toAstronomical($from, $val) //1 AU = 149 597 871 464 m
	{
		switch ($from) {
		case 'millimeter': case 'mm': return $val / 149597871464 / 1000;
		case 'centimeter': case 'cm': return $val / 149597871464 / 100;
		case 'decimeter':  case 'dm': return $val / 149597871464 / 10;
		case 'meter':      case 'm':  return $val / 149597871464;
		case 'kilometer':  case 'km': return $val / 149597871464 * 1000;
		}
	}

	function toFeet($from, $val) //1 US feet = 1 200/3 937 m
	{
		switch ($from) {
		case 'meter': case 'm': return $val / (1200/3937);
		}
	}

	function toInch($from, $val) //1 inch = 1/36 yd = 1/12 ft = 0.0254 m
	{
		switch ($from) {
		case 'meter': case 'm': return $val / 0.0254;
		case 'feet': case 'ft': return $val / (1/12);
		case 'yard': case 'yd': return $val / (1/36);
		}
	}

	function toMeter($from, $val)
	{//XXX verify
		switch ($from) {
		case 'astronomical': case 'au': return $val * 149597871464;
		case 'feet':         case 'ft': return $val * (1200/3937);
		case 'usmile':       case 'mi': return $val * 1609.344;
		case 'ukmile':                  return $val * 1852;
		}
	}

	function toMileUK($from, $val) // 1 UK mile = 1,852 meters (about 6,076.1 ft)
	{//XXX verify
		switch ($from) {
		case 'meter': case 'm': return $val / 1852;
		}
	}

	function toMileUS($from, $val) // 1 US mile (mi) = 1 760 yd = 5 280 ft = 80 chains = 1 609.344 m
	{//XXX verify
		switch ($from) {
		case 'meter': case 'm': return $val / 1609.344;
		case 'yard': case 'yd': return $val / 1760;
		case 'feet': case 'ft': return $val / 5280;
		case 'chains':          return $val / 80;
		}
	}

	function toYard($from, $val) // 1 yd = 0.9144 m = 3 ft = 36 in
	{//XXX verify
		switch ($from) {
		case 'meter': case 'm': return $val * 0.9144;
		case 'feet': case 'ft': return $val / 3;
		case 'inch': case 'in': return $val / 36;
		}
	}



}

$l = new length();

if ($l->conv('m', 'au', 100000000000) != 0.668458708813) echo "FAIL1\n";
if ($l->conv('km', 'au', 100000000000) != 668.458708813) echo "FAIL2\n";
if ($l->conv('m', 'ft', 100) != 328.083333333) echo "FAIL3\n";
if ($l->conv('m', 'in', 100) != 3937.00787402) echo "FAIL4\n";
if ($l->conv('ft', 'in', 100) != 1200) echo "FAIL5\n";
if ($l->conv('yd', 'in', 100) != 3600) echo "FAIL6\n";


echo $l->conv('yd', 'in', 100)."\n";

//if ($l->conv('usmile','meter', 1.5) != 2414.016) echo "FAIL1\n";

?>
