<?php
/**
 * $Id$
 *
 * Conversion functions for different distance systems
 *
 * Supported distance systems:
 *  Metric
 *  xxx
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
		case 'm': return $this->toMeters($from, $val);
		}
		return false;
	}

	function toMeters($s, $val)
	{
		switch ($s) {
		case 'xx': return $val;
		}
	}
}

//mile (mi) 	≡ 1 760 yd = 5 280 ft = 80 chains

?>
