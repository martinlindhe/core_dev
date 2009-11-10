<?php
/**
 * $Id$
 *
 * Conversion functions between different duration scales
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Conversion_of_units#Time
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

class ConvertDuration
{
	private $scale = array( ///< unit scale to a bit
	'second' => 1,
	'minute' => 60,
	'hour'   => 3600,
	'day'    => 86400,
	);

	private $lookup = array(
	'sec'   => 'second',
	'min'   => 'minute',
	'hr'    => 'hour',
	'dy'    => 'day',
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
