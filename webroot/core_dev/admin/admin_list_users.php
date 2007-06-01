<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');
	
	echo createMenu($admin_menu, 'blog_menu');

	echo '<h2>list users</h2>';
	echo 'As a super admin, you can upgrade users to other user levels from this page.<br/><br/>';

	$mode = 0;
	if (!empty($_GET['mode'])) $mode = $_GET['mode'];
	$list = getUsers($mode);

	if ($session->isSuperAdmin && !empty($_POST)) {
		for ($i=0; $i<count($list); $i++) {
			if ($_POST['mode_'.$list[$i]['userId']] != $list[$i]['userMode']) {
				setUserMode($list[$i]['userId'], $_POST['mode_'.$list[$i]['userId']]);
				$list[$i]['userMode'] = $_POST['mode_'.$list[$i]['userId']];
			}
		}
	}

	if ($session->isSuperAdmin) echo '<form method="post" action="">';
	echo '<table summary="" border="1">';
	echo '<tr>';
	echo '<th>Username</th>';
	echo '<th>Last active</th>';
	echo '<th>Created</th>';
	echo '<th>User mode</th>';
	echo '</tr>';
	foreach ($list as $user)
	{
		echo '<tr>';
		echo '<td>'.nameLink($user['userId'], $user['userName']).'</td>';
		echo '<td>'.$user['timeLastActive'].'</td>';
		echo '<td>'.$user['timeCreated'].'</td>';
		echo '<td>';
			if ($session->isSuperAdmin) {
				echo '<select name="mode_'.$user['userId'].'">';
				echo '<option value="0"'.($user['userMode']==0?' selected="selected"':'').'>Normal</option>';
				echo '<option value="1"'.($user['userMode']==1?' selected="selected"':'').'>Admin</option>';
				echo '<option value="2"'.($user['userMode']==2?' selected="selected"':'').'>Super admin</option>';
				echo '</select>';
			} else {
				echo $user['userMode'];
			}
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';

	if ($session->isSuperAdmin) {
		echo '<input type="submit" class="button" value="Save changes"/>';
		echo '</form>';
	}

	require($project.'design_foot.php');
?>