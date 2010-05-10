<?php
/**
 * $Id$
 *
 * Tool to view, modify and delete a user
 */

//STATUS: wip

class UserEditor
{
    private $id;
    function setId($n) { $this->id = $n; }

    function render()
    {
        $session = SessionHandler::getInstance();

        if (!$session->isSuperAdmin)
            return;

        $user = new UserHandler($this->id);
        if (!$user->getId()) {
            echo '<h2>No such user exists</h2>';
            return;
        }

        echo '<h1>User admin for '.$user->getName().'</h1>';

        if ($session->isSuperAdmin) {
            if ($session->id != $this->id && isset($_GET['remove'])) {
                $user->remove();
                echo '<div class="item">User removed</div>';
                return;
            }
            //if (isset($_GET['block'])) addBlock(BLOCK_USERID, $userId);
            if (!empty($_POST['chgpwd'])) {
                $user->setPassword($_POST['chgpwd']);
                echo '<div class="item">Password changed!</div>';
                return;
            }
        }

        if ($session->isSuperAdmin) {
            if ($session->id != $this->id) {
                echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$this->id.'&remove">Remove user</a><br/><br/>';
                //echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$this->id.'&block">Block user</a><br/><br/>';
            }

            echo xhtmlForm();
            echo t('Change password').': ';
            echo xhtmlPassword('chgpwd');
            echo xhtmlSubmit('Change');
            echo xhtmlFormClose().'<br/><br/>';
        }
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

    }

}

?>
