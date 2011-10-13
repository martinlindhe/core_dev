<?php

//TODO: ability to edit userdata field
//TODO: ability to add new userdata field

require_once('UserDataField.php');
require_once('YuiDatatable.php');

$session->requireSuperAdmin();

switch ($this->owner) {
case 'list':
    echo '<h1>Existing userdata fields</h1>';

    $list = UserDataField::getAll();

    $dt = new YuiDatatable();
    $dt->addColumn('id',    'Name', 'link', 'coredev/view/userdata/edit/', 'name');
    $dt->addColumn('type',  'Type'); //, 'array', getUserTypes() );

    $dt->setDataList( $list );
    echo $dt->render();

    break;

case 'edit':
    // child = field id
    echo '<h1>Edit userdata field</h1>';

    $field = UserDataField::get($this->child);
    d($field);
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
