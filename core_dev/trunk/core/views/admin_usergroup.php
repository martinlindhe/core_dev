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



echo '<table>';
echo '<tr><th>Name</th><th>Level</th><th>Info</th></tr>';
foreach ( $caller->getList() as $grp) {
    echo '<tr>';
    echo '<td>'.$grp->getName().'</td>';
    echo '<td>'.$grp->getLevelDesc().'</td>';
    echo '<td>'.$grp->getInfo().'</td>';
    echo '</tr>';
}
echo '</table>';
echo '<br/>';


echo '<h2>Add new group</h2>';

$form = new XhtmlForm('adm_usergroup');
$form->addInput('name', 'Group name');
$form->addTextarea('info', 'Info');
$form->addDropdown('level', 'Level', UserGroup::getUserlevels() );

$form->addSubmit('Add');
$form->setHandler('addUserGroupSubmit');
echo $form->render();

?>
