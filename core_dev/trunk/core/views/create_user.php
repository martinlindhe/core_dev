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

        $user_id = UserHandler::create($username, $pwd);

        if (!$user_id)
            $error->add('Failed to create user');

        if ($error->getErrorCount()) {
            echo $error->render(true);
            return;
        }

        UserSetting::setEmail($user_id, $_POST['u_email']);

        if (!empty($_POST['u_grp']))
            $user->addToGroup($_POST['u_grp']);

        echo '<div class="good">New user created. '.ahref('iview/manage_user/'.$user_id, $username).'</div>';
    }
}


echo xhtmlForm('add_user');

echo '<h1>Create new user</h1>';
echo 'Username: '.xhtmlInput('u_name').'<br/>';
echo 'Password: '.xhtmlInput('u_pwd').'<br/>';
echo 'E-mail: '.xhtmlInput('u_email').'<br/>';
echo '<br/>';

echo 'User group: ';

$x = new XhtmlComponentDropdown();
$x->name = 'u_grp';
$x->options = UserGroupList::getIndexedList();
echo $x->render();

echo '<br/>';
echo xhtmlSubmit('Create');
echo '</form>';

?>
