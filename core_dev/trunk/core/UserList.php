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

        $q = 'SELECT COUNT(*) FROM tblUsers WHERE timeDeleted IS NULL';
        return $db->pSelectItem($q);
    }

    /**
     * @return number of users online
     */
    static function onlineCount()
    {
        $session = SessionHandler::getInstance();
        $db = SqlHandler::getInstance();

        $q  = 'SELECT COUNT(*) FROM tblUsers WHERE timeDeleted IS NULL';
        $q .= ' AND timeLastActive >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
        return $db->getOneItem($q);
    }

    /**
     * @return array of User objects for all users online
     */
    static function getUsersOnline()
    {
        $session = SessionHandler::getInstance();
        $db = SqlHandler::getInstance();

        $q  = 'SELECT * FROM tblUsers WHERE timeDeleted IS NULL';
        $q .= ' AND timeLastActive >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
        $q .= ' ORDER BY timeLastActive DESC';

        $users = array();

        foreach ($db->getArray($q) as $row) {
            $user = new User();
            $user->loadFromSql($row);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * @param $filter partial username matching
     * @return array of User objects
     */
    static function getUsers($filter = '')
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblUsers';
        $q .= ' WHERE timeDeleted IS NULL';

        if ($filter)
            $q .= ' AND userName LIKE "%'.$db->escape($filter).'%"';

        $users = array();

        foreach ($db->getArray($q) as $row) {
            $user = new User();
            $user->loadFromSql($row);
            $users[] = $user;
        }

        return $users;
    }

    /**
     * Returns a id->name array
     */
    static function getFlat($filter = '')
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT userId, userName FROM tblUsers';
        $q .= ' WHERE timeDeleted IS NULL';

        if ($filter)
            $q .= ' AND userName LIKE "%'.$db->escape($filter).'%"';

        return $db->getMappedArray($q);
    }

    static function render()
    {
        $view = new ViewModel('views/admin_UserList.php');
        return $view->render();
    }
}

?>
