<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2012 <martin@startwars.org>
 */

//STATUS: wip

require_once('User.php');

class UserList
{
    /**
     * @return total number of users (excluding deleted ones)
     */
    static function getCount()
    {
        $q = 'SELECT COUNT(*) FROM tblUsers WHERE time_deleted IS NULL';
        return Sql::pSelectItem($q);
    }

    /**
     * @return number of users online
     */
    static function onlineCount()
    {
        $session = SessionHandler::getInstance();

        $q =
        'SELECT COUNT(*) FROM tblUsers WHERE time_deleted IS NULL'.
        ' AND time_last_active >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
        return Sql::pSelectItem($q);
    }

    /**
     * @return array of User objects for all users online
     */
    static function getUsersOnline($filter = '')
    {
        $session = SessionHandler::getInstance();
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblUsers WHERE time_deleted IS NULL';

        if ($filter)
            $q .= ' AND userName LIKE "%'.$db->escape($filter).'%"';

        $q .=
        ' AND time_last_active >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)'.
        ' ORDER BY time_last_active DESC';

        $list = $db->getArray($q);
        return SqlObject::loadObjects($list, 'User');
    }

    /**
     * @param $filter partial username matching
     * @return array of User objects
     */
    static function getUsers($filter = '')
    {
        $db = SqlHandler::getInstance();

        $q =
        'SELECT * FROM tblUsers'.
        ' WHERE time_deleted IS NULL';

        if ($filter) {
            $q .= ' AND name LIKE ?';
            $list = Sql::pSelect($q, 's', '%'.$filter.'%');
        } else {
            $list = Sql::pSelect($q);
        }

        return SqlObject::loadObjects($list, 'User');
    }

    /**
     * Returns a id->name array
     */
    static function getFlat($filter = '')
    {
        $db = SqlHandler::getInstance();

        $q =
        'SELECT id, name FROM tblUsers'.
        ' WHERE time_deleted IS NULL';

        if ($filter)
            $q .= ' AND name LIKE "%'.$db->escape($filter).'%"';

        return $db->getMappedArray($q);
    }

    static function getNewUsers($limit = 10)
    {
        $q =
        'SELECT * FROM tblUsers'.
        ' ORDER BY time_created DESC LIMIT '.intval($limit);

        $list = Sql::pSelect($q);

        return SqlObject::loadObjects($list, 'User');
    }

}

?>
