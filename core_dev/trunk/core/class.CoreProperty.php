<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//STATUS: wip

class CoreProperty
{
	/**
	 * Initialize object to specified value
	 *
	 * @param $s string or numeric value
	 */
	function __construct($s = '')
	{
		$this->set($s);
	}

	/**
	 * Convert object representation to a string
	 */
	function __toString()
	{
		return $this->get().'';   	//XXX cp. '' evaluerar true eller javetinte nåt är fel
	}

}

?>
