<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireAdmin();

require('design_admin_head.php');

if ($session->isSuperAdmin && !empty($_GET['del'])) {
	Users::removeUser($_GET['del']);
}
if ($session->isSuperAdmin && !empty($_GET['del_block'])) {
	removeBlock(BLOCK_USERID, $_GET['del_block']);
}

$pager['head'] = '';

echo '<h1>User lists</h1>';

echo '<a href="?notactivated">Not activated</a> | ';
echo '<a href="?activated">Activated</a>  | ';
echo '<a href="?blocked">Blocked</a>  | ';
echo '<a href="?removed">Removed</a> | ';
echo '<a href="?online">Users online</a>';

$order = '';

if (isset($_GET['order'])) {
	$order = ' ORDER BY '.$_GET['order'];
} else {
	$order = ' ORDER BY ';
}


if (isset($_GET['notactivated'])) {
	echo '<h1>Not activated users</h1>';

	$q = 'SELECT count(t1.userId) FROM tblUsers AS t1';
	$q .= ' LEFT JOIN tblSettings AS t2 ON (t1.userId = t2.ownerId AND t2.settingType='.SETTING_USERDATA.' AND t2.settingName = "activated")';
	$q .= ' WHERE t1.timeCreated IS NOT NULL AND t1.timeDeleted IS NULL AND (t2.settingValue != "1" OR t2.settingValue IS NULL) ORDER BY t1.timeLastActive DESC';
	$cnt = $db->getOneItem($q);

	$pager = makePager($cnt, 20);

	$q = 'SELECT t1.* FROM tblUsers AS t1';
	$q .= ' LEFT JOIN tblSettings AS t2 ON (t1.userId = t2.ownerId AND t2.settingType='.SETTING_USERDATA.' AND t2.settingName = "activated")';
	$q .= ' WHERE t1.timeCreated IS NOT NULL AND t1.timeDeleted IS NULL AND (t2.settingValue != "1" OR t2.settingValue IS NULL) ORDER BY t1.timeLastActive DESC';
	$q .= $pager['limit'];
	$list = $db->getArray($q);

} else if (isset($_GET['activated'])) {
	echo '<h1>Activated users</h1>';

	$q = 'SELECT count(t1.userId) FROM tblUsers AS t1';
	$q .= ' LEFT JOIN tblSettings AS t2 ON (t1.userId = t2.ownerId AND t2.settingType='.SETTING_USERDATA.' AND t2.settingName = "activated")';
	$q .= ' WHERE t1.timeDeleted IS NULL AND t2.settingValue = "1" ORDER BY t1.timeLastActive DESC';
	$cnt = $db->getOneItem($q);

	$pager = makePager($cnt, 20);

	$q = 'SELECT t1.* FROM tblUsers AS t1';
	$q .= ' LEFT JOIN tblSettings AS t2 ON (t1.userId = t2.ownerId AND t2.settingType='.SETTING_USERDATA.' AND t2.settingName = "activated")';
	$q .= ' WHERE t1.timeDeleted IS NULL AND t2.settingValue = "1" ORDER BY t1.timeLastActive DESC';
	$q .= $pager['limit'];
	$list = $db->getArray($q);

} else if (isset($_GET['removed'])) {
	echo '<h1>Removed users</h1>';

	$q = 'SELECT count(userId) FROM tblUsers AS t1 WHERE timeDeleted IS NOT NULL ORDER BY timeLastActive DESC';
	$cnt = $db->getOneItem($q);

	$pager = makePager($cnt, 20);

 	$q = 'SELECT * FROM tblUsers AS t1 WHERE timeDeleted IS NOT NULL ORDER BY timeLastActive DESC';
	$q .= $pager['limit'];
	$list = $db->getArray($q);

} else if (isset($_GET['online'])) {
	echo '<h1>Users online</h1>';
	echo 'Was active in the last '.shortTimePeriod($session->online_timeout).'<br/><br/>';

	$cnt = Users::onlineCnt();

	$pager = makePager($cnt, 20);

	$list = Users::allOnline($pager['limit']);

} else if (isset($_GET['blocked'])) {
	echo '<h1>Users blocked by admin by userid</h1>';

	$cnt = getBlocksCount(BLOCK_USERID);

	$pager = makePager($cnt, 20);

	$list = getBlocks(BLOCK_USERID, $pager['limit']);
	$list2 = array();
	foreach($list as $row) {
		$temp = array(
						'userId' => $row['rule'],
						'userName' => Users::getName($row['rule']),
						'timeCreated' => $row['timeCreated'],
						'blocker' => $row['createdBy'],
						'timeDeleted' => NULL
						);
		$list2[] = $temp;
	}
	$list = $list2;
}

echo $pager['head'];

if (isset($list)) {
	echo '<table>';
	echo '<tr>';
	echo '<th>Username</th>';
	$email = $cell = $birth = false;

	if ($auth->userdata) {
		$email = getUserdataFieldIdByType(USERDATA_TYPE_EMAIL);
		$cell = getUserdataFieldIdByType(USERDATA_TYPE_CELLPHONE); // TODO: Make this work
		$birth = getUserdataFieldIdByType(USERDATA_TYPE_BIRTHDATE); // TODO: Make this work
	}

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
	if (isset($_GET['removed'])) {
		echo '<th>Time Deleted</th>';
	}
	echo '<th>&nbsp;</th>';
	echo '</tr>';
	$i = 0;

	foreach ($list as $row) {
		$i ++;
		echo '<tr'.($i%2 ? ' style="background-color:#ccc"' : '').'>';
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
		if (isset($_GET['removed'])) {
			echo '<td>'.$row['timeDeleted'].'</td>';
		}

		echo '<td>';
		if (!isset($_GET['blocked']) && $session->isSuperAdmin && $session->id != $row['userId'] && !$row['timeDeleted']) {
			echo coreButton('Delete', '?del='.$row['userId']);
		} else if (isset($_GET['blocked']) && $session->isSuperAdmin) {
			echo coreButton('Delete', '?del_block='.$row['userId']);
		}
		else echo '&nbsp;';
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
}

echo $pager['head'];

require('design_admin_foot.php');
?>
