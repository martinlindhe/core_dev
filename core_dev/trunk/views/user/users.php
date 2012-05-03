<?php

require_once('UserList.php');
require_once('YuiDatatable.php');

switch ($this->owner) {
case 'online':
    $list = UserList::getUsersOnline();
    echo '<h2>Showing all users online';

    echo ' ('.count($list).' hits)</h2>';

    $dt = new YuiDatatable();
    $dt->addColumn('id',    'Username', 'link', 'u/profile/', 'name');
    $dt->addColumn('time_last_active',  'Last active');
    $dt->addColumn('type',              'Type', 'array', getSessionTypes() );
    $dt->setDataSource( $list );
    //$dt->setRowsPerPage(10);
    echo $dt->render();
    break;


default:
    throw new Exception ('no such view: '.$this->owner);
}

?>
