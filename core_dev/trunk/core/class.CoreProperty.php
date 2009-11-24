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
	 * Initialize object to specified time
	 *
	 * @param $s unix timestamp or strtotime() understandable string
	 */
	function __construct($s = '')
	{
		$this->set($s);
	}

	/**
	 * Convert object representation to a string
	 */
	//XXX cp. '' evaluerar true eller javetinte nåt är fel
	function __toString()
	{
		return $this->get().'';
	}

}
?>
