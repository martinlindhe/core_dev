<?php
/**
 * This is the defualt view for the UserList class
 */

//TODO: use editable YuiDatatable
//XXX TODO: lista användare online

require_once('UserList.php');

if (!$session->isAdmin)
    return;

if ($session->isSuperAdmin && !empty($_GET['del'])) {
    $user = new User($_GET['del']);
    $user->remove();
}

echo '<h1>Manage users</h1>';
echo 'All users: '.ahref('coredev/admin/userlist', UserList::getCount()).'<br/>';
echo 'Users online: '.ahref('coredev/admin/userlist/0/online', UserList::onlineCount()).'<br/>';

$filter = '';
if (!empty($_POST['usearch'])) $filter = $_POST['usearch'];

//process updates
if ($session->isSuperAdmin && !empty($_POST))
{
    if (!empty($_POST['u_name']) && !empty($_POST['u_pwd']))
    {
        $username = trim($_POST['u_name']);
        $pwd      = trim($_POST['u_pwd']);

        $user = new User();
        $user->create($username);
        if (!$user->getId())
            $error->add('Failed to create user');

        if ($error->getErrorCount()) {
            echo $error->render(true);
            return;
        }

        $user->setPassword($pwd);

        if (!empty($_POST['u_grp']))
            $user->addToGroup($_POST['u_grp']);

        echo '<div class="good">New user created. '.ahref('coredev/admin/useredit/'.$user->getId(), $username).'</div>';
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
echo '<th>Last Ip</th>';
echo '<th>Created</th>';
echo '<th>User level</th>';
echo '<th>Groups</th>';
echo '</tr>';

foreach (UserList::getUsers($filter) as $user)
{
    echo '<tr>';
    echo '<td>'.ahref('coredev/admin/useredit/'.$user->getId(), $user->getName()).'</a></td>';
    echo '<td>'.$user->getTimeLastActive().'</td>';
    echo '<td>'.$user->getLastIp().'</td>';
    echo '<td>'.$user->getTimeCreated().'</td>';
    echo '<td>'.$user->getUserLevelName().'</td>';

    $grps = array();
    foreach ($user->getGroups() as $g)
        $grps[] = $g->getName();

    echo '<td>'.implode(', ', $grps).'</td>';

    echo '</tr>';
}
echo '<tr>';
echo '<td colspan="6">';
echo 'Add user: '.xhtmlInput('u_name').' - pwd: '.xhtmlInput('u_pwd');

$grp = new UserGroupList();

$x = new XhtmlComponentDropdown();
$x->name = 'u_grp';
$x->options = $grp->getIndexedList();
echo $x->render();


echo '</td>';
echo '</tr>';
echo '</table>';

if ($session->isSuperAdmin) {
    echo '<br/>';
    echo xhtmlSubmit('Save changes');
    echo '</form>';
}
?>
