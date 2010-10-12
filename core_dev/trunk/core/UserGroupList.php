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
    function getList()
    {
        $db = SqlHandler::getInstance();

        $items = array();

        $q = 'SELECT * FROM tblUserGroups';
        foreach ($db->getArray($q) as $row) {
            $item = new UserGroup();
            $item->loadFromSql($row);

            $items[] = $item;
        }
        return $items;
    }

    function render()
    {
        $view = new ViewModel('views/admin_usergroup.php', $this);
        return $view->render();
    }
}

?>
