<?php
/**
 * $Id$
 *
 * Object holding a list of UserGroup objects
 *
 * @author Martin Lindhe, 2010-2011 <martin@startwars.org>
 */

//STATUS: wip

require_once('UserGroup.php');

class UserGroupList
{
    static function getItems()
    {
        $db = SqlHandler::getInstance();

        $arr = array();

        foreach ($db->pSelect('SELECT * FROM tblUserGroups') as $row)
        {
            $item = new UserGroup();
            $item->loadFromSql($row);

            $arr[] = $item;
        }

        return $arr;
    }

    /**
     * @return array of id=>name pairs
     */
    static function getIndexedList()
    {
        $res = array();

        foreach (self::getItems() as $i)
            $res[ $i->getId() ] = $i->getName();

        return $res;
    }

}

?>
