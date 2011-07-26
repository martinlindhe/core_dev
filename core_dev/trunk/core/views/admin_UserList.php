<?php
/**
 * This is the defualt view for the UserList class
 */

//TODO: use editable YuiDatatable

require_once('UserList.php');

if (!$session->isAdmin)
    return;

echo '<h1>Manage users</h1>';
echo 'All users: '.ahref('coredev/admin/userlist/', UserList::getCount()).'<br/>';
echo 'Users online: '.ahref('coredev/admin/userlist/?online', UserList::onlineCount()).'<br/>';

$filter = '';
if (!empty($_POST['usearch'])) $filter = $_POST['usearch'];

echo '<br/>';
echo xhtmlForm('usearch_frm');
echo 'Username filter: '.xhtmlInput('usearch');
echo xhtmlSubmit('Search');
echo xhtmlFormClose();
echo '<br/>';

echo '<table border="1">';
echo '<tr>';
echo '<th>Username</th>';
echo '<th>E-mail</th>';
echo '<th>Last active</th>';
echo '<th>Last Ip</th>';
echo '<th>Created</th>';
echo '<th>User level</th>';
echo '<th>Groups</th>';
echo '</tr>';

if (isset($_GET['online']))
{
    $list = UserList::getUsersOnline($filter);
    echo '<h2>Showing all users online';
}
else
{
    $list = UserList::getUsers($filter);
    echo '<h2>Showing all users';
}

if ($filter)
    echo ', matching <u>'.$filter.'</u>';

echo ' ('.count($list).' hits)</h2>';


foreach ($list as $user)
{
    if ($user->isOnline()) echo '<tr style="background:#79EFFF;">';
    else echo '<tr>';
    echo '<td>'.ahref('coredev/admin/useredit/'.$user->getId(), $user->getName()).'</a></td>';
    echo '<td>'.$user->getEmail().'</td>';
    echo '<td>'.sql_datetime($user->getTimeLastActive()).'</td>';
    echo '<td>'.$user->getLastIp().'</td>';
    echo '<td>'.sql_datetime($user->getTimeCreated()).'</td>';
    echo '<td>'.$user->getUserLevelName().'</td>';

    $grps = array();
    foreach ($user->getGroups() as $g)
        $grps[] = $g->getName();

    echo '<td>'.implode(', ', $grps).'</td>';

    echo '</tr>';
}
echo '</table>';
echo '<br/>';

if ($session->isSuperAdmin)
    echo ahref('coredev/view/create_user/', 'Create new user');

?>
