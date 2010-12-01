<?php
/**
 * This is the defualt view for the UserList class
 */

//TODO: use editable YuiDatatable

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
    if (!empty($_POST['u_name']) && !empty($_POST['u_pwd']))
    {
//        $auth = AuthHandler::getInstance();
//        $error = ErrorHandler::getInstance();
        $username = trim($_POST['u_name']);
        $pwd      = trim($_POST['u_pwd']);

//        if ($this->reserved_usercheck && isReservedUsername($username)) return t('Username is not allowed');
/*
        //Checks if email was required, and if so if it was correctly entered
        if ($this->userdata) {
            $chk = verifyRequiredUserdataFields();
            if ($chk !== true) return $chk;
        }
*/
        $user = new User();
        $user->create($username);
        if (!$user->id)
            $error->add('Failed to create user');

        if ($error->getErrorCount()) {
            echo $error->render(true);
            return;
        }

        $user->setPassword($pwd);

        if (!empty($_POST['u_grp']))
            $user->addToGroup($_POST['u_grp']);

        echo '<div class="good">New user created. <a href="/admin/core/useredit/'.$new_id.'">'.$_POST['u_name'].'</a></div>';
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
echo '<th>Groups</th>';
echo '</tr>';

foreach ($caller->getUsers($filter) as $user)
{
    echo '<tr>';
    echo '<td><a href="/admin/core/useredit/'.$user->getId().'">'.$user->getName().'</a></td>';
    echo '<td>'.$user->getTimeLastActive().'</td>';
    echo '<td>'.$user->getTimeCreated().'</td>';
    echo '<td>'.$user->getUserLevelName().'</td>';

    $grps = array();
    foreach ($user->getGroups() as $g)
        $grps[] = $g->getName();

    echo '<td>'.implode(', ', $grps).'</td>';

    echo '</tr>';
}
echo '<tr>';
echo '<td colspan="5">';
echo 'Add user: '.xhtmlInput('u_name').' - pwd: '.xhtmlInput('u_pwd');

$grp = new UserGroupList();
echo xhtmlSelectArray('u_grp', $grp->getIndexedList() ).' ';
echo '</td>';
echo '</tr>';
echo '</table>';

if ($session->isSuperAdmin) {
    echo '<br/>';
    echo xhtmlSubmit('Save changes');
    echo '</form>';
}
?>
