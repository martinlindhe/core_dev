<?php

//STATUS: wip

//TODO: ability to set default prefix for all links shown here

switch ($this->owner)
{
case 'userlist':
    $userlist = new UserList();
    $userlist->setMode($this->child);
    echo $userlist->render();
    break;

case 'useredit':
    $useredit = new UserEditor();
    $useredit->setId($this->child);
    echo $useredit->render();
    break;

case 'phpinfo':
    phpinfo();
    break;

default:
    echo '<h1>core_dev admin</h1>';
    echo '<a href="/admin/core/userlist">Manage users</a><br/>';
    echo '<a href="/admin/core/phpinfo">phpinfo()</a><br/>';
    break;
}

?>
