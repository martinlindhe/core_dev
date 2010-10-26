<?php
/**
 * This is the defualt view for the UserGroup class
 */

//TODO: ability to remove a empty usergroup

$header->setTitle('Admin: Edit user group: '.$caller->getName() );

echo '<h1>Edit user group: '.$caller->getName().'</h1>';

function saveUserGroupSubmit($p, $caller)
{
    $grp = new UserGroup($p['g_id']);
    $grp->setName($p['name']);
    $grp->setInfo($p['info']);
    $grp->setLevel($p['level']);
    $grp->save();

    return true;
}

$form = new XhtmlForm('adm_usergroup');
$form->addHidden('g_id', $caller->getId() ); //XXXX hax
$form->addInput('name', 'Group name', $caller->getName() );
$form->addTextarea('info', 'Info', $caller->getInfo() );
$form->addDropdown('level', 'Level', User::getUserLevels(), $caller->getLevel() );

$form->addSubmit('Save');
$form->setHandler('saveUserGroupSubmit');
echo $form->render();
echo '<br/><br/>';

echo '<h1>Group members</h1>';

foreach ($caller->getMembers() as $user) {
    echo ahref('admin/core/useredit/'.$user->getId(), $user->getName() ).'<br/>';
}

?>
