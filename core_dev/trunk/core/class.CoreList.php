<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//STATUS: wip

//TODO: use list sort code from newsfeed & playlist

require_once('class.CoreBase.php');

class CoreList extends CoreBase
{
	protected $items = array(); ///< list of objects

	function getItems() { return $this->items; }

	function addItem($i)
	{
		$this->items[] = $i;
	}

	/**
	 * Adds a array of objects to the list
	 *
	 * @param $list list of objects
	 */
	function addItems($list)
	{
		foreach ($list as $e)
			$this->addItem($e);
	}

}


?>
