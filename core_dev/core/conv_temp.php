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
		//XXX: the rounding is neccesary to work around PHP's handling of floats,
		//     or some will return .0000000000001 precision which make testcase fail
		switch ($to) {
			case 'C': return round($this->toCelcius($from, $val), 10);
			case 'F': return round($this->toFarenheit($from, $val), 10);
			case 'K': return round($this->toKelvin($from, $val), 10);
			case 'R': return round($this->toRankine($from, $val), 10);
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

?>
