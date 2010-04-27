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
    private $usermode;
    function setMode($n) { $this->usermode = $n; }

    /**
     * @return total number of users (excluding deleted ones)
     */
    function getCount($usermode = 0)
    {
        if (!is_numeric($usermode)) return false;

        $db = SqlHandler::getInstance();

        $q = 'SELECT COUNT(*) FROM tblUsers';
        $q .= ' WHERE timeDeleted IS NULL';
        if ($usermode)
            $q .= ' AND userMode='.$usermode;

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
        if ($this->usermode)
            $q .= ' AND userMode='.$this->usermode;

        if ($filter) $q .= ' AND userName LIKE "%'.$db->escape($filter).'%"';

        return $db->getArray($q);
    }

    function render()  //XXX make a view of this
    {
        $session = SessionHandler::getInstance();

        if (!$session->isSuperAdmin)
            return;

        if ($session->isSuperAdmin && !empty($_GET['del'])) {
            $userhandler = new UserHandler($_GET['del']);
            $userhandler->remove();
        }

        echo 'All users: <a href="/admin/userlist/">'.$this->getCount().'</a><br/>';
        echo 'Webmasters: <a href="/admin/userlist/'.USERLEVEL_WEBMASTER.'">'.$this->getCount(USERLEVEL_WEBMASTER).'</a><br/>';
        echo 'Admins: <a href="/admin/userlist/'.USERLEVEL_ADMIN.'">'.$this->getCount(USERLEVEL_ADMIN).'</a><br/>';
        echo 'SuperAdmins: <a href="/admin/userlist/'.USERLEVEL_SUPERADMIN.'">'.$this->getCount(USERLEVEL_SUPERADMIN).'</a><br/>';
//XXX TODO: lista användare online
        echo 'Users online: <a href="/admin/userlist/0/online">'.$this->onlineCount().'</a><br/>';

        $filter = '';
        if (!empty($_POST['usearch'])) $filter = $_POST['usearch'];

        //process updates
        if ($session->isSuperAdmin && !empty($_POST)) {
            $list = $this->getUsers();
            foreach ($list as $row) {
                if (empty($_POST['mode_'.$row['userId']])) continue;
                $newmode = $_POST['mode_'.$row['userId']];
                if ($newmode != $row['userMode']) {
                    $userhandler = new UserHandler($row['userId']);
                    $userhandler->setMode($newmode);
                }
            }

            if (!empty($_POST['u_name']) && !empty($_POST['u_pwd']) && isset($_POST['u_mode'])) {
                $auth = AuthHandler::getInstance();

                $new_id = $auth->register($_POST['u_name'], $_POST['u_pwd'], $_POST['u_pwd'], $_POST['u_mode']);
                if (!is_numeric($new_id)) {
                    echo '<div class="critical">'.$new_id.'</div>';
                } else {
                    echo '<div class="okay">New user created. <a href="/admin/useredit/'.$new_id.'">'.$_POST['u_name'].'</a></div>';
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
        echo '<th>User mode</th>';
        echo '</tr>';
        foreach ($list as $user)
        {
            echo '<tr'.($user['timeDeleted']?' class="critical"':'').'>';
            echo '<td><a href="/admin/useredit/'.$user['userId'].'">'.$user['userName'].'</a></td>';
            echo '<td>'.$user['timeLastActive'].'</td>';
            echo '<td>'.$user['timeCreated'].'</td>';
            echo '<td>';
            if ($session->isSuperAdmin) {
                echo '<select name="mode_'.$user['userId'].'">';
                echo '<option value="'.USERLEVEL_NORMAL.'"'.($user['userMode']==USERLEVEL_NORMAL?' selected="selected"':'').'>Normal</option>';
                echo '<option value="'.USERLEVEL_WEBMASTER.'"'.($user['userMode']==USERLEVEL_WEBMASTER?' selected="selected"':'').'>Webmaster</option>';
                echo '<option value="'.USERLEVEL_ADMIN.'"'.($user['userMode']==USERLEVEL_ADMIN?' selected="selected"':'').'>Admin</option>';
                echo '<option value="'.USERLEVEL_SUPERADMIN.'"'.($user['userMode']==USERLEVEL_SUPERADMIN?' selected="selected"':'').'>Super admin</option>';
                echo '</select> ';
                if ($session->id != $user['userId'] && !$user['timeDeleted']) {
                    echo coreButton('Delete', '?del='.$user['userId']);
                }
            } else {
                echo $user['userMode'];
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '<tr>';
        echo '<td colspan="3">Add user: '.xhtmlInput('u_name').' - pwd: '.xhtmlInput('u_pwd').'</td>';
        echo '<td>';
        if ($session->isSuperAdmin) {
            echo '<select name="u_mode">';
            echo '<option value="'.USERLEVEL_NORMAL.'">Normal</option>';
            echo '<option value="'.USERLEVEL_WEBMASTER.'">Webmaster</option>';
            echo '<option value="'.USERLEVEL_ADMIN.'">Admin</option>';
            echo '<option value="'.USERLEVEL_SUPERADMIN.'">Super admin</option>';
            echo '</select>';
        } else {
            echo 'normal user';
        }
        echo '</td>';
        echo '</tr>';
        echo '</table>';

        if ($session->isSuperAdmin) {
            echo xhtmlSubmit('Save changes');
            echo '</form>';
        }

    }
}


?>
