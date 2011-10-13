<?php

require_once('UserDataType.php');

$session->requireSuperAdmin();

switch ($this->owner) {
case 'list':
    echo '<h1>Existing userdata types</h1>';

    $list = UserDataType::getAll();
    d($list);

    break;


default:
    echo 'No handler for view '.$this->owner;
}

?>
