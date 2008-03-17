<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	if (isset($_GET['notactivated'])) {
		echo '<h1>Not activated users</h1>';
		
		$q = 'SELECT t1.* FROM tblUsers AS t1';
		$q .= ' LEFT JOIN tblSettings AS t2 ON (t1.userId = t2.ownerId AND t2.settingType='.SETTING_USERDATA.' AND t2.settingName = "activated")';
		$q .= ' WHERE t2.settingValue != "1" OR t2.settingValue IS NULL';
		$list = $db->getArray($q);
	} else if (isset($_GET['activated'])) {
		echo '<h1>Activated users</h1>';

		$q = 'SELECT t1.* FROM tblUsers AS t1';
		$q .= ' LEFT JOIN tblSettings AS t2 ON (t1.userId = t2.ownerId AND t2.settingType='.SETTING_USERDATA.' AND t2.settingName = "activated")';
		$q .= ' WHERE t2.settingValue = "1"';
		$list = $db->getArray($q);

	} else if (isset($_GET['removed'])) {
		$q = 'SELECT * FROM tblUsers WHERE timeDeleted IS NOT NULL';
		$list = $db->getArray($q);
	} else if (isset($_GET['online'])) {
		$list = Users::allOnline();
	}

	if (!isset($list)) {
		echo '<h1>User lists</h1>';

		echo '<a href="?notactivated">Not activated</a><br/>';
		echo '<a href="?activated">Activated</a><br/>';
		echo '<a href="?removed">Removed</a><br/>';
		echo '<a href="?online">Users online</a><br/>';
	} else {
		echo '<table>';
		echo '<tr>';
		echo '<th>Username</th>';
		echo '<th>Time created</th>';
		echo '</tr>';
		foreach ($list as $row) {
			echo '<tr>';
			echo '<td>'.Users::link($row['userId'], $row['userName']).'</td>';
			echo '<td>'.$row['timeCreated'].'</td>';
			echo '</tr>';
		}
		echo '</table>';
	}

	require($project.'design_foot.php');
?>
