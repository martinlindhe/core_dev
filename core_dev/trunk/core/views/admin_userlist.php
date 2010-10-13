<?php
/**
 * This is the defualt view for the UserList class
 */

//TODO: use editable yui_datatable

if (!$session->isAdmin)
    return;

if ($session->isSuperAdmin && !empty($_GET['del'])) {
    $userhandler = new UserHandler($_GET['del']);
    $userhandler->remove();
}

echo '<h1>Manage users</h1>';
echo 'All users: <a href="/admin/core/userlist/">'.$caller->getCount().'</a><br/>';
//XXX TODO: lista användare online
echo 'Users online: <a href="/admin/core/userlist/0/online">'.$caller->onlineCount().'</a><br/>';

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

$list = $caller->getUsers($filter);

if ($session->isSuperAdmin)
    echo xhtmlForm('add_user');

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

if ($session->isSuperAdmin) {
    echo '<br/>';
    echo xhtmlSubmit('Save changes');
    echo '</form>';
}
?>
