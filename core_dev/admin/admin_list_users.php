<?php
/**
 * $Id$
 */

require_once('find_config.php');
$h->session->requireAdmin();

require('design_admin_head.php');

echo '<h1>List users</h1>';
echo 'As a super admin, you can upgrade users to other user levels, or remove them from the system from this page.<br/><br/>';

if ($h->session->isSuperAdmin && !empty($_GET['del'])) {
	Users::removeUser($_GET['del']);
}

$usermatch = '';
if (!empty($_POST['usearch'])) $usermatch = $_POST['usearch'];

$mode = 0;
if (!empty($_GET['mode'])) $mode = $_GET['mode'];

if ($h->session->isSuperAdmin && !empty($_POST)) {
	$list = Users::getUsers($mode);
	foreach ($list as $row) {
		if (empty($_POST['mode_'.$row['userId']])) continue;
		$newmode = $_POST['mode_'.$row['userId']];
		if ($newmode != $row['userMode']) {
			Users::setMode($row['userId'], $newmode);
		}
	}

	if (!empty($_POST['u_name']) && !empty($_POST['u_pwd']) && isset($_POST['u_mode'])) {
		$newUserId = $h->user->register($_POST['u_name'], $_POST['u_pwd'], $_POST['u_pwd'], $_POST['u_mode']);
		if (!is_numeric($newUserId)) {
			echo '<div class="critical">'.$newUserId.'</div>';
		} else {
			echo '<div class="okay">New user created. Go to user page: '.Users::link($newUserId, $_POST['u_name']).'</div>';
		}
	}
}

echo '<form method="post">';
echo 'Username filter: '.xhtmlInput('usearch');
echo xhtmlSubmit('Search');
echo '</form><br/>';

$tot_cnt = Users::cnt($mode, $usermatch);
$limit = 25;
$pager = makePager($tot_cnt, $limit);

$list = Users::getUsers($mode, $pager['limit'], $usermatch);

echo $pager['head'];

if ($h->session->isSuperAdmin) echo '<form method="post" action="">';
echo '<table summary="" border="1">';
echo '<tr>';
echo '<th>Username</th>';
echo '<th>Last active</th>';
echo '<th>Created</th>';
echo '<th>User mode</th>';
echo '</tr>';
foreach ($list as $user)
{
	echo '<tr'.($user['timeDeleted']?' class="critical"':'').'>';
	echo '<td><a href="admin_user.php?id='.$user['userId'].'">'.$user['userName'].'</a></td>';
	echo '<td>'.$user['timeLastActive'].'</td>';
	echo '<td>'.$user['timeCreated'].'</td>';
	echo '<td>';
	if ($h->session->isSuperAdmin) {
		echo '<select name="mode_'.$user['userId'].'">';
		echo '<option value="'.USERLEVEL_NORMAL.'"'.($user['userMode']==USERLEVEL_NORMAL?' selected="selected"':'').'>Normal</option>';
		echo '<option value="'.USERLEVEL_WEBMASTER.'"'.($user['userMode']==USERLEVEL_WEBMASTER?' selected="selected"':'').'>Webmaster</option>';
		echo '<option value="'.USERLEVEL_ADMIN.'"'.($user['userMode']==USERLEVEL_ADMIN?' selected="selected"':'').'>Admin</option>';
		echo '<option value="'.USERLEVEL_SUPERADMIN.'"'.($user['userMode']==USERLEVEL_SUPERADMIN?' selected="selected"':'').'>Super admin</option>';
		echo '</select> ';
		if ($h->session->id != $user['userId'] && !$user['timeDeleted']) {
			echo coreButton('Delete', '?del='.$user['userId']);
		}
	} else {
		echo $user['userMode'];
	}
	echo '</td>';
	echo '</tr>';
}
echo '<tr>';
echo '<td colspan="3">Add user: '.xhtmlInput('u_name').' - pwd: '.xhtmlInput('u_pwd').'</td>';
echo '<td>';
if ($h->session->isSuperAdmin) {
	echo '<select name="u_mode">';
	echo '<option value="'.USERLEVEL_NORMAL.'">Normal</option>';
	echo '<option value="'.USERLEVEL_WEBMASTER.'">Webmaster</option>';
	echo '<option value="'.USERLEVEL_ADMIN.'">Admin</option>';
	echo '<option value="'.USERLEVEL_SUPERADMIN.'">Super admin</option>';
	echo '</select>';
} else {
	echo 'normal user';
}
echo '</td>';
echo '</tr>';
echo '</table>';

if ($h->session->isSuperAdmin) {
	echo xhtmlSubmit('Save changes');
	echo '</form>';
}

require('design_admin_foot.php');
?>
