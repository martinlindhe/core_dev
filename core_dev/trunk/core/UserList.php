<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: wip

//TODO: factor our sql from here

require_once('User.php');

class UserList
{
    /**
     * @return total number of users (excluding deleted ones)
     */
    static function getCount()
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT COUNT(*) FROM tblUsers WHERE time_deleted IS NULL';
        return $db->pSelectItem($q);
    }

    /**
     * @return number of users online
     */
    static function onlineCount()
    {
        $session = SessionHandler::getInstance();
        $db = SqlHandler::getInstance();

        $q  = 'SELECT COUNT(*) FROM tblUsers WHERE time_deleted IS NULL';
        $q .= ' AND time_last_active >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
        return $db->getOneItem($q);
    }

    /**
     * @return array of User objects for all users online
     */
    static function getUsersOnline($filter = '')
    {
        $session = SessionHandler::getInstance();
        $db = SqlHandler::getInstance();

        $q  = 'SELECT * FROM tblUsers WHERE time_deleted IS NULL';

        if ($filter)
            $q .= ' AND userName LIKE "%'.$db->escape($filter).'%"';

        $q .= ' AND time_last_active >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
        $q .= ' ORDER BY time_last_active DESC';

        $list = $db->getArray($q);
        return SqlObject::loadObjects($list, 'User');
    }

    /**
     * @param $filter partial username matching
     * @return array of User objects
     */
    static function getUsers($filter = '')  //XXXX use SqlObject
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblUsers';
        $q .= ' WHERE time_deleted IS NULL';

        if ($filter)
            $q .= ' AND name LIKE "%'.$db->escape($filter).'%"';

        $list = $db->getArray($q);
        return SqlObject::loadObjects($list, 'User');
    }

    /**
     * Returns a id->name array
     */
    static function getFlat($filter = '')
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT id, name FROM tblUsers';
        $q .= ' WHERE time_deleted IS NULL';

        if ($filter)
            $q .= ' AND name LIKE "%'.$db->escape($filter).'%"';

        return $db->getMappedArray($q);
    }

}

?>
