<?php
/**
 * $Id$
 *
 * Conversion functions for different temperature systems
 *
 * Celcius (C)
 * -----------
 * 0°C and 100°C are arbitrarily placed at the melting and boiling
 * points of water and standard to the metric system.
 *
 * Farenheit (F)
 * -------------
 * The freezing point of water is 32 °F and the boiling point 212 °F,
 * placing the boiling and freezing points of water exactly 180 degrees apart.
 *
 * Kelvin (K)
 * ----------
 * An absolute temperature scale where absolute zero, the theoretical
 * absence of all thermal energy, is zero (0 K).
 *
 * Rankine (R)
 * -----------
 * As with the Kelvin scale, zero on the Rankine scale is absolute zero,
 * but the Rankine degree is defined as equal to one degree Fahrenheit,
 * rather than the one degree Celsius used by the Kelvin scale.
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Temperature_conversion_formulas
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

class temp
{
	function conv($from, $to, $val)
	{
		$val = floatval($val);
		switch ($to) {
			case 'C': return $this->toCelcius($from, $val);
			case 'F': return $this->toFarenheit($from, $val);
			case 'K': return $this->toKelvin($from, $val);
			case 'R': return $this->toRankine($from, $val);
		}
	}

	function toCelcius($s, $val)
	{
		switch ($s) {
			case 'C': return  $val;
			case 'F': return ($val - 32) * (5/9);
			case 'K': return  $val - 273.15;
			case 'R': return ($val - 491.67) * (5/9);
		}
	}

	function toFarenheit($s, $val)
	{
		switch ($s) {
			case 'C': return ($val * (9/5)) + 32;
			case 'F': return  $val;
			case 'K': return ($val * (9/5)) - 459.67;	//XXX verify!
			case 'R': return  $val - 459.67;	//XXX verify!
		}
	}

	function toKelvin($s, $val)
	{
		switch ($s) {
			case 'C': return  $val + 273.15; //XXX verify!
			case 'F': return ($val + 459.67) * (5/9); //XXX verify
			case 'K': return  $val;
			case 'R': return  $val * (5/9); //XXX verifgy
		}
	}

	function toRankine($s, $val)
	{
		switch ($s) {
			case 'C': return ($val + 273.15) * (9/5); //XXX verify
			case 'F': return  $val + 459.67; //XXX verify
			case 'K': return  $val * (9/5); //XXX verify
			case 'R': return  $val;
		}
	}
}

$t = new temp();

if ($t->conv('C', 'F', 300) != 572)       echo "FAIL 1\n";
if ($t->conv('C', 'K', 300) != 573.15)    echo "FAIL 2\n";
if ($t->conv('C', 'R', 300) != 1031.67)   echo "FAIL 3\n";

if ($t->conv('F', 'C', 500) != 260)       echo "FAIL 4\n";
if ($t->conv('F', 'K', 500) != 533.15)    echo "FAIL 5\n";
if ($t->conv('F', 'R', 500) != 959.67)    echo "FAIL 6\n";

if ($t->conv('K', 'C', 0) != -273.15)     echo "FAIL 7\n";
if ($t->conv('K', 'F', 0) != -459.67)     echo "FAIL 8\n";
if ($t->conv('K', 'R', 0) != 0)           echo "FAIL 9\n";

if ($t->conv('R', 'C', 509.67) != 10)     echo "FAIL 10\n";
if ($t->conv('R', 'F', 509.67) != 50)     echo "FAIL 11\n";
if ($t->conv('R', 'K', 509.67) != 283.15) echo "FAIL 12\n";

//FIXME: some tests fail (5,6,12) however they return correct data. perhaps a datatype issue??

?>
