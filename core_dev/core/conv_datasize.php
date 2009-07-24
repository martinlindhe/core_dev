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

class mass
{
	var $scale = array( ///< unit scale to a bit
	'bit'      => 1,
	'kilobit'  => 1024,       // 2^10
	'megabit'  => 1048576,    // 2^20
	'gigabit'  => 1073741824, // 2^30

	'byte'     => 8,
	'kilobyte' => 8192,            // (2^10)*8
	'megabyte' => 8388608,         // (2^20)*8
	'gigabyte' => 8589934592,      // (2^30)*8
	'terabyte' => 8796093022208,   // (2^40)*8
	'petabyte' => 9007199254740992,// (2^50)*8
	);

	function conv($from, $to, $val)
	{
		if (empty($this->scale[$from]) || empty($this->scale[$to])) return false;

		return ($val * $this->scale[$from]) / $this->scale[$to];
	}
}

?>
