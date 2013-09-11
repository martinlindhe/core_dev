<?php

namespace cd;

require_once('UserList.php');
require_once('YuiDatatable.php');
require_once('Visit.php');

$session->requireLoggedIn();

switch ($this->owner) {
case 'profile':
    echo '<h2>Showing the most recent visits of your profile page</h2>';

    $list = Visit::getAll(PROFILE, $session->id);

    $dt = new YuiDatatable();
    $dt->addColumn('ref',    'User', 'link', 'u/profile/', 'name');
    $dt->addColumn('time',  'Time of visit');
    $dt->setDataSource( $list );
    //$dt->setRowsPerPage(10);
    echo $dt->render();
    break;

default:
    throw new \Exception ('no such view: '.$this->owner);
}

?>
