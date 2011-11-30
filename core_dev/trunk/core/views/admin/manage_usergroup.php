<?php
/**
 * This is the user group manager
 */

//TODO: ability to remove a empty usergroup


if (!$session->isAdmin)
    return;

$grp = new UserGroup($this->owner);

$header->setTitle('Admin: Manage user group: '.$grp->getName() );

echo '<h1>Manage user group: '.$grp->getName().'</h1>';

echo 'Created at '.sql_datetime($grp->getTimeCreated()).' by '.$grp->getCreatorName().'<br/><br/>';

function saveUserGroupSubmit($p)
{
    $grp = new UserGroup($p['g_id']);
    $grp->setName($p['name']);
    $grp->setInfo($p['info']);
    $grp->setLevel($p['level']);
    $grp->save();

    return true;
}

$form = new XhtmlForm('adm_usergroup');
$form->addHidden('g_id', $grp->getId() ); //XXXX hax
$form->addInput('name', 'Group name', $grp->getName() );
$form->addTextarea('info', 'Info', $grp->getInfo() );
$form->addDropdown('level', 'Level', getUserLevels(), $grp->getLevel() );

$form->addSubmit('Save');
$form->setHandler('saveUserGroupSubmit');
echo $form->render();
echo '<br/><br/>';

echo '<h1>Group members</h1>';

foreach ($grp->getMembers() as $user)
    echo ahref('a/manage_user/'.$user->id, $user->name).'<br/>';

?>
