<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2010 <martin@startwars.org>
 */

//STATUS: wip

//TODO: move GET/POST handling to separate function
//TODO: factor our sql from here

//TODO: move render() to a view
//TODO: use editable yui_datatable

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
        $session = SessionHandler::getInstance();

        if (!$session->isAdmin)
            return;

        if ($session->isSuperAdmin && !empty($_GET['del'])) {
            $userhandler = new UserHandler($_GET['del']);
            $userhandler->remove();
        }

        echo '<h1>Manage users</h1>';
        echo 'All users: <a href="/admin/core/userlist/">'.$this->getCount().'</a><br/>';
//XXX TODO: lista användare online
        echo 'Users online: <a href="/admin/core/userlist/0/online">'.$this->onlineCount().'</a><br/>';

        $filter = '';
        if (!empty($_POST['usearch'])) $filter = $_POST['usearch'];

        //process updates
        if ($session->isSuperAdmin && !empty($_POST))
        {
            if (!empty($_POST['u_name']) && !empty($_POST['u_pwd']) && isset($_POST['u_mode']))
            {
                $auth = AuthHandler::getInstance();

                $new_id = $auth->register($_POST['u_name'], $_POST['u_pwd'], $_POST['u_pwd'], $_POST['u_mode']);
                if (!is_numeric($new_id)) {
                    echo '<div class="critical">'.$new_id.'</div>';
                } else {
                    echo '<div class="okay">New user created. <a href="/admin/core/useredit/'.$new_id.'">'.$_POST['u_name'].'</a></div>';
                }
            }
        }

        echo '<br/>';
        echo xhtmlForm('usearch_frm');
        echo 'Username filter: '.xhtmlInput('usearch');
        echo xhtmlSubmit('Search');
        echo xhtmlFormClose();
        echo '<br/>';

        $list = $this->getUsers($filter);

        if ($session->isSuperAdmin) echo '<form method="post" action="">';
        echo '<table summary="" border="1">';
        echo '<tr>';
        echo '<th>Username</th>';
        echo '<th>Last active</th>';
        echo '<th>Created</th>';
        echo '</tr>';
        foreach ($list as $user)
        {
            echo '<tr'.($user['timeDeleted']?' class="critical"':'').'>';
            echo '<td><a href="/admin/core/useredit/'.$user['userId'].'">'.$user['userName'].'</a></td>';
            echo '<td>'.$user['timeLastActive'].'</td>';
            echo '<td>'.$user['timeCreated'].'</td>';
            echo '</tr>';
        }
        echo '<tr>';
        echo '<td colspan="3">Add user: '.xhtmlInput('u_name').' - pwd: '.xhtmlInput('u_pwd').'</td>';
        echo '</tr>';
        echo '</table>';

        echo 'XXX TODO: on creation, select & add user to a user group';

        if ($session->isSuperAdmin) {
            echo '<br/>';
            echo xhtmlSubmit('Save changes');
            echo '</form>';
        }

    }
}


?>
