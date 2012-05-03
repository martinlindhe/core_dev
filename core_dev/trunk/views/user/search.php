<?php

require_once('UserList.php');
require_once('YuiDatatable.php');

switch ($this->owner) {
case 'user':

    function handleSearch($p)
    {
        $list = UserList::getUsers($p['q']);

        echo '<h2>Showing users matching <u>'.$p['q'].'</u>';
        echo ' ('.count($list).' hits)</h2>';

        $dt = new YuiDatatable();
        $dt->addColumn('id',    'Username', 'link', 'u/profile/', 'name');
        $dt->addColumn('time_last_active',  'Last active');
        $dt->setDataSource($list);

        echo $dt->render();
    }

    $form = new XhtmlForm();

    $form->addInput('q', 'Username search');
    $form->setFocus('q');
    $form->addSubmit('Search');
    $form->setHandler('handleSearch');

    echo $form->render();
    echo '<br/>';

    echo '&raquo; '.ahref('u/users/online', 'Show users online').'<br/>';
    echo '&raquo; '.ahref('u/users/newest', 'Show new members').'<br/>';
    break;

default:
    throw new Exception ('no such view: '.$this->owner);
}

?>
