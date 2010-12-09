<?php
/**
 * $Id$
 *
 * Object holding a list of UserGroup objects
 *
 * @author Martin Lindhe, 2010 <martin@startwars.org>
 */

//STATUS: wip

require_once('UserGroup.php');

class UserGroupList
{
    private $items;

    function __construct()
    {
        $this->load();
    }

    private function load()
    {
        $db = SqlHandler::getInstance();

        $arr = array();

        foreach ($db->pSelect('SELECT * FROM tblUserGroups') as $row)
        {
            $item = new UserGroup();
            $item->loadFromSql($row);

            $arr[] = $item;
        }

        $this->items = $arr;
    }

    function getItems() { return $this->items; }

    /**
     * @return array of id=>name pairs
     */
    function getIndexedList()
    {
        $res = array();

        foreach ($this->items as $i)
            $res[ $i->getId() ] = $i->getName();

        return $res;
    }

    function render()
    {
        $view = new ViewModel('views/admin_UserGroupList.php', $this);
        return $view->render();
    }
}

?>
