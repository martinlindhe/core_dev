<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireAdmin();

if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
$userId = $_GET['id'];

require('design_admin_head.php');

if ($session->isSuperAdmin) {
	if (isset($_GET['delete'])) Users::removeUser($userId);
	if (isset($_GET['block'])) addBlock(BLOCK_USERID, $userId);
	if (!empty($_POST['chgpwd'])) {
		Users::setPassword($userId, $_POST['chgpwd']);
		echo '<div class="item">Password changed!</div>';
	}
}

if (!Users::exists($userId)) {
	echo '<h2>No such user exists</h2>';
	require('design_admin_foot.php');
	die;
}

echo '<h1>User admin for '.Users::getName($userId).'</h1>';

if ($session->isSuperAdmin) {
	echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$userId.'&amp;delete">Delete user</a><br/><br/>';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$userId.'&amp;block">Block user</a><br/><br/>';

	echo xhtmlForm();
	echo t('Change password').': ';
	echo xhtmlPassword('chgpwd');
	echo xhtmlSubmit('Change');
	echo xhtmlFormClose().'<br/><br/>';
}

echo '<h2>'.t('Userdata').'</h2>';
editUserdataSettings($userId);

echo '<h2>'.t('Events').'</h2>';
$events = getEvents(0, $userId, ' LIMIT 0,40');

echo '<table>';
foreach ($events as $row) {
	echo '<tr>';
		echo '<td>'.$row['timeCreated'].'</td>';
		echo '<td>'.$event_name[$row['type']].'</td>';
	echo '</tr>';
}
echo '</table>';

echo '<h2>'.t('Comments').'</h2>';
showComments(COMMENT_USER, $userId);

echo '<h2>All userdata</h2>';
if (!empty($_POST['new_ud_key']) && isset($_POST['new_ud_val'])) {
	saveSetting(SETTING_USERDATA, $userId, $_POST['new_ud_key'], $_POST['new_ud_val']);
}
$list = readAllSettings(SETTING_USERDATA, $userId);

echo '<table>';
echo '<tr>';
echo '<th>Key</th>';
echo '<th>Value</th>';
echo '<th>Time set</th>';
echo '<th>Remove</th>';
echo '</tr>';
echo xhtmlForm('mod_userdata');
foreach ($list as $row) {
	if (!empty($_POST['del_ud_'.$row['settingId']])) {
		deleteSetting(SETTING_USERDATA, $userId, $row['settingName']);
		continue;
	} else if (!empty($_POST['mod_ud_'.$row['settingId']])) {
		saveSetting(SETTING_USERDATA, $userId, $row['settingName'], $_POST['mod_ud_'.$row['settingId']]);
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
//FIXME "check all checkboxes" javascript
echo 'New key: '.xhtmlInput('new_ud_key').', value: '.xhtmlInput('new_ud_val').'<br/>';
echo xhtmlSubmit('Save changes');
echo xhtmlFormClose();

require('design_admin_foot.php');
?>
