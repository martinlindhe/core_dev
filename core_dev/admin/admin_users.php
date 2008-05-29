<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireAdmin();

require($project.'design_head.php');

echo createMenu($admin_menu, 'blog_menu');

if ($session->isSuperAdmin && !empty($_GET['del'])) {
	Users::removeUser($_GET['del']);
}

echo '<h1>User lists</h1>';

echo '<a href="?notactivated">Not activated</a> | ';
echo '<a href="?activated">Activated</a>  | ';
echo '<a href="?removed">Removed</a> | ';
echo '<a href="?online">Users online</a>';

if (isset($_GET['notactivated'])) {
	echo '<h1>Not activated users</h1>';
		
	$q = 'SELECT t1.* FROM tblUsers AS t1';
	$q .= ' LEFT JOIN tblSettings AS t2 ON (t1.userId = t2.ownerId AND t2.settingType='.SETTING_USERDATA.' AND t2.settingName = "activated")';
	$q .= ' WHERE t1.timeCreated IS NOT NULL AND (t2.settingValue != "1" OR t2.settingValue IS NULL) ORDER BY t1.timeLastActive DESC';
	$list = $db->getArray($q);
} else if (isset($_GET['activated'])) {
	echo '<h1>Activated users</h1>';

	$q = 'SELECT t1.* FROM tblUsers AS t1';
	$q .= ' LEFT JOIN tblSettings AS t2 ON (t1.userId = t2.ownerId AND t2.settingType='.SETTING_USERDATA.' AND t2.settingName = "activated")';
	$q .= ' WHERE t2.settingValue = "1" ORDER BY t1.timeLastActive DESC';
	$list = $db->getArray($q);

} else if (isset($_GET['removed'])) {
	echo '<h1>Removed users</h1>';

	$q = 'SELECT * FROM tblUsers WHERE timeDeleted IS NOT NULL ORDER BY t1.timeLastActive DESC';
	$list = $db->getArray($q);
} else if (isset($_GET['online'])) {
	echo '<h1>Users online</h1>';
	echo 'Was active in the last '.shortTimePeriod($session->online_timeout).'<br/><br/>';
	$list = Users::allOnline();
}

if (isset($list)) {
	echo '<table>';
	echo '<tr>';
	echo '<th>Username</th>';
	$email = getUserdataFieldIdByType(USERDATA_TYPE_EMAIL);
	$cell = getUserdataFieldIdByType(USERDATA_TYPE_CELLPHONE); // TODO: Make this work
	$birth = getUserdataFieldIdByType(USERDATA_TYPE_BIRTHDATE); // TODO: Make this work

	if ($email) {
		echo '<th>Email</th>';
	}
	if ($cell) {
		echo '<th>Mobile</th>';
	}
	if (isset($_GET['activated']) && $birth) {
		echo '<th>Birthdate</th>';
	}
	echo '<th>Time created</th>';
	if (isset($_GET['notactivated']) && $auth->mail_activate) {
		echo '<th>Activation code</th>';
	}
	if (isset($_GET['activated'])) {
		echo '<th>Activation Date</th>';
	}
	echo '<th>&nbsp;</th>';
	echo '</tr>';
	foreach ($list as $row) {
		echo '<tr>';
		echo '<td>'.Users::link($row['userId'], $row['userName']).'</td>';
		if ($email) {
			echo '<td>'.loadUserdataEmail($row['userId']).'</td>';
		}
		if ($cell) {
			echo '<td>'.loadUserdataCellphone($row['userId']).'</td>';
		}
		if (isset($_GET['activated']) && $birth) {
			echo '<td>'.loadUserdataBirthdate($row['userId']).'</td>';
		}
		echo '<td>'.$row['timeCreated'].'</td>';
		if (isset($_GET['notactivated']) && $auth->mail_activate) {
			echo '<td>'.(getActivationCode(ACTIVATE_EMAIL,$row['userId'])?getActivationCode(ACTIVATE_EMAIL,$row['userId']):getActivationCode(ACTIVATE_SMS,$row['userId'])).'</td>';
		}
		if (isset($_GET['activated'])) {
			echo '<td>'.(getActivationDate(ACTIVATE_EMAIL,$row['userId'])?getActivationDate(ACTIVATE_EMAIL,$row['userId']):getActivationDate(ACTIVATE_SMS,$row['userId'])).'</td>';
		}

		echo '<td>';
		if ($session->isSuperAdmin && $session->id != $row['userId'] && !$row['timeDeleted']) echo '<a href="?del='.$row['userId'].getProjectPath().'">del</a>';
		else echo '&nbsp;';
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
}

require($project.'design_foot.php');
?>
