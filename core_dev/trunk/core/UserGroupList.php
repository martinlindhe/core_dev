<?php
/**
 * $Id$
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
        $db = SqlHandler::getInstance();

        $this->items = array();

        $q = 'SELECT * FROM tblUserGroups';
        foreach ($db->getArray($q) as $row) {
            $item = new UserGroup();
            $item->loadFromSql($row);

            $this->items[] = $item;
        }
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
