<h1>Manage user groups</h1>

<?php

function addUserGroupSubmit($p, $caller)
{
    $grp = new UserGroup();
    $grp->setName($p['name']);
    $grp->setInfo($p['info']);
    $grp->setLevel($p['level']);
    $grp->save();

    return true;
}

$header->setTitle('Admin: Manage user groups');

$form = new XhtmlForm('adm_usergroup');
$form->addInput('name', 'Group name');
$form->addTextarea('info', 'Info');
$form->addDropdown('level', 'Level', UserGroup::getUserlevels() );

$form->addSubmit('Add');
$form->setHandler('addUserGroupSubmit');
echo $form->render();

?>
