<?php
/**
 * $Id$
 *
 * Conversion functions between different byte representations
 *
 * References
 * ----------
 * http://en.wikipedia.org/wiki/Units_of_information
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

class datasize
{
	var $scale = array( ///< unit scale to a bit
	'bit'  => 1,
	'kbit' => 1024,       // 2^10
	'mbit' => 1048576,    // 2^20
	'gbit' => 1073741824, // 2^30

	'b'    => 8,
	'kb'   => 8192,            // (2^10)*8
	'mb'   => 8388608,         // (2^20)*8
	'gb'   => 8589934592,      // (2^30)*8
	'tb'   => 8796093022208,   // (2^40)*8
	'pb'   => 9007199254740992,// (2^50)*8
	);

	function conv($from, $to, $val)
	{
		$from = $this->shortcode($from);
		$to   = $this->shortcode($to);
		if (!$from || !$to) return false;

		return ($val * $this->scale[$from]) / $this->scale[$to];
	}

	function shortcode($name)
	{
		if (substr($name, -1) == 's') $name = substr($name, 0, -1);

		$lookup = array(
		'bit'      => 'bit',
		'kilobit'  => 'kbit',
		'megabit'  => 'mbit',
		'gigabit'  => 'gbit',

		'byte'     => 'b',
		'kilobyte' => 'kb',
		'megabyte' => 'mb',
		'gigabyte' => 'gb',
		'terabyte' => 'tb',
		'petabyte' => 'pb'
		);

		$name = strtolower($name);

		if (!empty($lookup[$name])) return $lookup[$name];
		if (array_search($name, $lookup)) return $name;
		return false;
	}
}

?>
