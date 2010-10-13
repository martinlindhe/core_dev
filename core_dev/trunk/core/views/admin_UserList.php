<?php
/**
 * This is the defualt view for the UserList class
 */

//TODO: use editable yui_datatable

if (!$session->isAdmin)
    return;

if ($session->isSuperAdmin && !empty($_GET['del'])) {
    $user = new User($_GET['del']);
    $user->remove();
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

if ($session->isSuperAdmin)
    echo xhtmlForm('add_user');

echo '<table border="1">';
echo '<tr>';
echo '<th>Username</th>';
echo '<th>Last active</th>';
echo '<th>Created</th>';
echo '<th>User level</th>';
echo '</tr>';

foreach ($caller->getUsers($filter) as $user)
{
    echo '<tr>';
    echo '<td><a href="/admin/core/useredit/'.$user->getId().'">'.$user->getName().'</a></td>';
    echo '<td>'.$user->getTimeLastActive().'</td>';
    echo '<td>'.$user->getTimeCreated().'</td>';
    echo '<td>'.$user->getUserLevelName().'</td>';
    echo '</tr>';
}
echo '<tr>';
echo '<td colspan="4">Add user: '.xhtmlInput('u_name').' - pwd: '.xhtmlInput('u_pwd').'</td>';
echo '</tr>';
echo '</table>';

if ($session->isSuperAdmin) {
    echo '<br/>';
    echo xhtmlSubmit('Save changes');
    echo '</form>';
}
?>
