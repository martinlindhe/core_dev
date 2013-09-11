<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

//STATUS: drop this code & rework it into user classes: FeedWriter, Playlist

//TODO: use list sort code from newsfeed & playlist

namespace cd;

require_once('CoreBase.php');

class CoreList extends CoreBase
{
    protected $items = array(); ///< list of objects

    function getItems() { return $this->items; }

    function getKeyVals()
    {
        $res = array();
        foreach ($this->items as $item) {
            if (get_parent_class($item) != 'CategoryItem') {         //XXX only works for CategoryItem type
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
