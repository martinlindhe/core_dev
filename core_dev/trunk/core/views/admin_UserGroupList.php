<?php
/**
 * This is the defualt view for the UserGroupList class
 */

function addUserGroupSubmit($p)
{
    $grp = new UserGroup();
    $grp->setName($p['name']);
    $grp->setInfo($p['info']);
    $grp->setLevel($p['level']);
    $grp->save();

    redir('admin/core/usergroup');
}

$header->setTitle('Admin: Manage user groups');

echo '<h1>Manage user groups</h1>';

echo '<h2>Add new group</h2>';

$form = new XhtmlForm('adm_usergroup');
$form->addInput('name', 'Group name');
$form->addTextarea('info', 'Info');
$form->addDropdown('level', 'Level', User::getUserLevels() );

$form->addSubmit('Add');
$form->setHandler('addUserGroupSubmit');
echo $form->render();


echo '<br/>';
echo '<h2>Existing groups</h2>';

echo '<table>';
echo '<tr><th>Name</th><th>Level</th><th>Info</th><th>Created</th></tr>';
foreach ( $caller->getItems() as $grp) {
    echo '<tr>';
    echo '<td>'.ahref('coredev/admin/usergroup_details/'.$grp->getId(), $grp->getName()).'</td>';
    echo '<td>'.$grp->getLevelDesc().'</td>';
    echo '<td>'.$grp->getInfo().'</td>';
    echo '<td>'.sql_datetime( $grp->getTimeCreated() ).'</td>';
    echo '</tr>';
}
echo '</table>';

?>
