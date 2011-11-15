<?php

require_once('UserList.php');
require_once('YuiDatatable.php');

switch ($this->owner) {
case 'user':

    function handleSearch($p)
    {
        $filter = !empty($p['q']) ? $p['q'] : '';

        $list = UserList::getUsers($filter);

        echo '<h2>Showing all users';

        if ($filter)
            echo ', matching <u>'.$filter.'</u>';

        echo ' ('.count($list).' hits)</h2>';

        $dt = new YuiDatatable();
        $dt->addColumn('id',    'Username', 'link', 'iview/profile/', 'name');
        $dt->addColumn('time_last_active',  'Last active');
//        $dt->addColumn('time_created',      'Created');
//        $dt->addColumn('is_online',         'Online?');
//        $dt->addColumn('type',              'Type', 'array', getUserTypes() );
//        $dt->addColumn('userlevel',         'User level', 'array', getUserLevels() );

        $dt->setDataSource($list);

        echo $dt->render();
    }

    $form = new XhtmlForm();

    $form->addInput('q', 'Username search');

    $form->addSubmit('Search');
    $form->setHandler('handleSearch');

    echo $form->render();
    break;

default:
    throw new Exception ('no such view: '.$this->owner);
}

?>
