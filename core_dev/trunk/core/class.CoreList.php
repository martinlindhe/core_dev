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

	function getKeyVals()
	{
		$res = array();
		foreach ($this->items as $item) {
			if (get_parent_class($item) != 'CoreItem') { 		//XXX only works for CoreItem type
				echo 'CoreList->getKeyVals: cant handle object type '.get_class($item).ln();
				continue;
			}
			$res[ $item->getId() ] = $item->getTitle();
		}
		return $res;
	}

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
