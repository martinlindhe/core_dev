<?php
/**
 * This is the defualt view for the UserEditor class
 */

//TODO: use XhtmlForm

require_once('YuiDatatable.php');

if (!$session->isSuperAdmin)
    return;

$user = new User($caller->getId() );
if (!$user->getId()) {
    echo '<h2>No such user exists</h2>';
    return;
}

echo '<h1>User admin for '.$user->getName().'</h1>';

echo 'Last IP: '.$user->getLastIp().'<br/>';
echo '<br/>';

if ($session->id != $caller->getId() && isset($_GET['remove'])) {
    //if (confirmed('Are you sure you want to remove this user?')) {  //XXX fix so confirmation works here
        $user->remove();
        echo '<div class="item">User removed</div>';
    //}
    return;
}

if (!empty($_POST['change_pwd'])) {
    $user->setPassword($_POST['change_pwd']);
    echo '<div class="item">Password changed!</div>';
    return;
}

if (!empty($_POST['grp_id'])) {
    $user->addToGroup($_POST['grp_id']);
}

if (!empty($_GET['rm_grp'])) {
    $user->removeFromGroup($_GET['rm_grp']);
}

echo '<h2>Group membership</h2>';
echo 'This user is member of the following groups:<br/>';

foreach ($user->getGroups() as $g) {
    echo '<a href="'.relurl_add( array('rm_grp' => $g->getId())).'">'.coreButton('Delete').'</a> ';
    echo ahref('coredev/admin/usergroup_details/'.$g->getId(), $g->getName()).'<br/>';
}
echo '<br/>';


$grp = new UserGroupList();

echo xhtmlForm('grp');

$x = new XhtmlComponentDropdown();
$x->name = 'grp_id';
$x->options = $grp->getIndexedList();

echo $x->render().' ';

echo xhtmlSubmit('Add');
echo xhtmlFormClose().'<br/><br/>';


echo '<h2>Password</h2>';

echo t('Change password').'<br/>';
echo xhtmlForm('pwd');
echo xhtmlPassword('change_pwd').' ';
echo xhtmlSubmit('Change');
echo xhtmlFormClose().'<br/><br/>';



echo '<h2>Login history</h2>';

$dt = new YuiDatatable();
$dt->addColumn('timeCreated',     'Timestamp');
$dt->addColumn('IP',              'IP');
$dt->addColumn('userAgent',       'User agent');
$dt->setSortOrder('timeCreated', 'desc');
$dt->setDataList( $user->getLoginHistory() );
$dt->setRowsPerPage( 10 );
echo $dt->render();




if ($session->id != $caller->getId() )
    echo '&raquo; <a href="'.relurl_add( array('remove'=>1) ).'">Remove user</a><br/><br/>';



/*
        echo '<h2>'.t('Userdata').'</h2>';
        editUserdataSettings($user->id);

        echo '<h2>'.t('Events').'</h2>';
        $events = getEvents(0, $user->id, ' LIMIT 0,40');

        echo '<table>';
        foreach ($events as $row) {
            echo '<tr>';
                echo '<td>'.$row['timeCreated'].'</td>';
                echo '<td>'.$event_name[$row['type']].'</td>';
            echo '</tr>';
        }
        echo '</table>';
*/

/*
        echo '<h2>'.t('Comments').'</h2>';
        echo showComments(COMMENT_USER, $user->getId());
*/

/*
        echo '<h2>All userdata</h2>';
        if (!empty($_POST['new_ud_key']) && isset($_POST['new_ud_val'])) {
            saveSetting(SETTING_USERDATA, 0, $userId, $_POST['new_ud_key'], $_POST['new_ud_val']);
        }
        $list = readAllSettings(SETTING_USERDATA, 0, $userId);

        echo '<table>';
        echo '<tr>';
        echo '<th>Key</th>';
        echo '<th>Value</th>';
        echo '<th>Time set</th>';
        echo '<th>Remove ('.xhtmlCheckbox('toggle', 'all', 1, false, "toggle_checkboxes(this, 'mod_userdata')").')</th>';
        echo '</tr>';
        echo xhtmlForm('mod_userdata');
        foreach ($list as $row) {
            if (!empty($_POST['del_ud_'.$row['settingId']])) {
                deleteSetting(SETTING_USERDATA, 0, $userId, $row['settingName']);
                continue;
            } else if (!empty($_POST['mod_ud_'.$row['settingId']]) && $row['settingValue'] != $_POST['mod_ud_'.$row['settingId']]) {
                saveSetting(SETTING_USERDATA, 0, $userId, $row['settingName'], $_POST['mod_ud_'.$row['settingId']]);
                $row['settingValue'] = $_POST['mod_ud_'.$row['settingId']];
            }

            echo '<tr>';
                echo '<td>'.$row['settingName'].'</td>';
                echo '<td>'.xhtmlInput('mod_ud_'.$row['settingId'], $row['settingValue']).'</td>';
                echo '<td>'.formatTime($row['timeSaved']).'</td>';
                echo '<td>'.xhtmlCheckbox('del_ud_'.$row['settingId']).'</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo 'New key: '.xhtmlInput('new_ud_key').', value: '.xhtmlInput('new_ud_val').'<br/>';

        echo xhtmlSubmit('Save changes');
        echo xhtmlFormClose();
*/
