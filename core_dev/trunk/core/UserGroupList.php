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
    function render()
    {
        $view = new ViewModel('views/admin_usergroup.php');
        return $view->render();
    }
}

?>
