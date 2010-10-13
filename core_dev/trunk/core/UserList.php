<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: factor our sql from here

require_once('UserHandler.php');

class UserList
{
    /**
     * @return total number of users (excluding deleted ones)
     */
    function getCount()
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT COUNT(*) FROM tblUsers';
        $q .= ' WHERE timeDeleted IS NULL';

        return $db->getOneItem($q);
    }

    /**
     * @return number of users online
     */
    function onlineCount()
    {
        $session = SessionHandler::getInstance();
        $db = SqlHandler::getInstance();

        $q  = 'SELECT COUNT(*) FROM tblUsers WHERE timeDeleted IS NULL';
        $q .= ' AND timeLastActive >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
        return $db->getOneItem($q);
    }

    /**
     * @returns array of all users online
     */
    function allOnline()
    {
        $session = SessionHandler::getInstance();
        $db = SqlHandler::getInstance();

        $q  = 'SELECT * FROM tblUsers WHERE timeDeleted IS NULL';
        $q .= ' AND timeLastActive >= DATE_SUB(NOW(),INTERVAL '.$session->online_timeout.' SECOND)';
        $q .= ' ORDER BY timeLastActive DESC';
        return $db->getArray($q);
    }

    /**
     * @param $filter partial username matching
     */
    function getUsers($filter = '')
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT * FROM tblUsers';
        $q .= ' WHERE timeDeleted IS NULL';

        if ($filter) $q .= ' AND userName LIKE "%'.$db->escape($filter).'%"';

        return $db->getArray($q);
    }

    /**
     * Returns a id->name array
     */
    function getFlat($filter = '')
    {
        $db = SqlHandler::getInstance();

        $q = 'SELECT userId, userName FROM tblUsers';
        $q .= ' WHERE timeDeleted IS NULL';

        if ($filter) $q .= ' AND userName LIKE "%'.$db->escape($filter).'%"';

        return $db->getMappedArray($q);
    }

    function render()
    {
        $view = new ViewModel('views/admin_userlist.php', $this);
        return $view->render();
    }
}


?>
