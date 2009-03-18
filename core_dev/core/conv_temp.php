<?php
/**
 * $Id$
 *
 * Conversion functions for different temperature systems
 *
 * Supported temperature systems:
 * - Celcius (C)
 * - Farenheit (F)
 * - Kelvin (K)
 * - Rankine (R)
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
			case 'K': return ($val * (9/5)) - 459.67;
			case 'R': return  $val - 459.67;
		}
	}

	function toKelvin($s, $val)
	{
		switch ($s) {
			case 'C': return  $val + 273.15;
			case 'F': return ($val + 459.67) * (5/9);
			case 'K': return  $val;
			case 'R': return  $val * (5/9);
		}
	}

	function toRankine($s, $val)
	{
		switch ($s) {
			case 'C': return ($val + 273.15) * (9/5);
			case 'F': return  $val + 459.67;
			case 'K': return  $val * (9/5);
			case 'R': return  $val;
		}
	}
}

$t = new temp();

if ($t->conv('C', 'F', 300) != 572)       echo "FAIL 1\n";
if ($t->conv('C', 'K', 300) != 573.15)    echo "FAIL 2\n";
if ($t->conv('C', 'R', 300) != 1031.67)   echo "FAIL 3\n";

if ($t->conv('F', 'C', 500) != 260)       echo "FAIL 4\n";
if ($t->conv('F', 'K', 500) != 533.15)    echo "FAIL 5\n"; //XXX
if ($t->conv('F', 'R', 500) != 959.67)    echo "FAIL 6\n"; //XXX

if ($t->conv('K', 'C', 0) != -273.15)     echo "FAIL 7\n";
if ($t->conv('K', 'F', 0) != -459.67)     echo "FAIL 8\n";
if ($t->conv('K', 'R', 0) != 0)           echo "FAIL 9\n";

if ($t->conv('R', 'C', 509.67) != 10)     echo "FAIL 10\n";
if ($t->conv('R', 'F', 509.67) != 50)     echo "FAIL 11\n";
if ($t->conv('R', 'K', 509.67) != 283.15) echo "FAIL 12\n"; //XXX

//FIXME: some checks fail (5,6,12) but they return correct value. datatype issue??

?>
