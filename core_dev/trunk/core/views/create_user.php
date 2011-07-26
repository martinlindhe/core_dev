<?php

if (!$session->isSuperAdmin)
    return;

// process updates
if (!empty($_POST))
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
        $user->saveSetting('email', $_POST['u_email']);

        if (!empty($_POST['u_grp']))
            $user->addToGroup($_POST['u_grp']);

        echo '<div class="good">New user created. '.ahref('coredev/admin/useredit/'.$user->getId(), $username).'</div>';
    }
}


echo xhtmlForm('add_user');

echo '<h1>Create new user</h1>';
echo 'Username: '.xhtmlInput('u_name').'<br/>';
echo 'Password: '.xhtmlInput('u_pwd').'<br/>';
echo 'E-mail: '.xhtmlInput('u_email').'<br/>';
echo '<br/>';

echo 'User group: ';

$grp = new UserGroupList();

$x = new XhtmlComponentDropdown();
$x->name = 'u_grp';
$x->options = $grp->getIndexedList();
echo $x->render();

echo '<br/>';
echo xhtmlSubmit('Create');
echo '</form>';

?>
