<?php

//STATUS: wip

require_once('UserGroupList.php');

switch ($this->view) {
case 'admin':
    $session->requireSuperAdmin();

    switch ($this->owner)
    {
    case 'userlist':
        $userlist = new UserList();
        echo $userlist->render();
        break;

    case 'useredit': //child=user id
        // XXX link to here is hardcoded in admin_UserList.php view
        $useredit = new UserEditor();
        $useredit->setId($this->child);
        echo $useredit->render();
        break;

    case 'usergroup':
        $grouplist = new UserGroupList();
        echo $grouplist->render();
        break;

    case 'usergroup_details': //child=group id
        // XXX link to here is hardcoded in admin_UserGroupList.php
        $details = new UserGroup($this->child);
        echo $details->render();
        break;

    case 'phpinfo':
        phpinfo();
        break;

    default:
        echo '<h1>core_dev admin</h1>';
        echo ahref('coredev/admin/userlist', 'Manage users').'<br/>';
        echo ahref('coredev/admin/usergroup', 'Manage user groups').'<br/>';
        echo '<br/>';
        echo ahref('coredev/admin/phpinfo', 'phpinfo()').'<br/>';
        break;
    }
    break;

case 'view':
    // view built in view. owner = name of view in core/views/

    $file = $page->getCoreDevInclude().'views/'.$this->owner.'.php';
    if (!file_exists($file))
        throw new Exception ('DEBUG: view not found '.$file);

    $view = new ViewModel($file);
    echo $view->render();
    break;

default:
    throw new Exception ('DEBUG: no such view '.$this->view);
}

?>
