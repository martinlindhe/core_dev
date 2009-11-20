<?php
/**
 * $Id$
 *
 * Conversion functions between different duration scales
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Conversion_of_units#Time
 * http://en.wikipedia.org/wiki/Leap_year
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('class.CoreBase.php');

class ConvertDuration extends CoreBase
{
	private $scale = array( ///< unit scale to a second
	'sec'  => 1,
	'min'  => 60,
	'hr'   => 3600,
	'dy'   => 86400,
	'week' => 604800,
	'mo'   => 2592000,  //30 days
	'yr'   => 31556952, //365.2425 days (gregorian year)
	);

	private $lookup = array(
	'second' => 'sec',
	'minute' => 'min',
	'hour'   => 'hr',
	'day'    => 'dy',
	'month'  => 'mo',
	'year'   => 'yr',
	);

	function setPrecision($n) { $this->precision = $n; }

	function getShortcode($name)
	{
		$name = strtolower($name);
		if (substr($name, -1) == 's') $name = substr($name, 0, -1);

		if (!empty($this->lookup[$name])) return $this->lookup[$name];
		if (array_search($name, $this->lookup)) return $name;
		return false;
	}

	function conv($from, $to, $val)
	{
		$from = $this->getShortcode($from);
		$to   = $this->getShortcode($to);
		if (!$from || !$to) return false;

		return ($val * $this->scale[$from]) / $this->scale[$to];
	}

}

?>
