<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');

	if (isset($_GET['notactivated'])) {
		echo '<h1>Not activated users</h1>';
		
		$q = 'SELECT * FROM tblUsers AS t1 ';
		$q .= 'LEFT JOIN tblSettings AS t2 ON (t1.userId = t2.ownerId AND t2.settingType='.SETTING_USERDATA.' AND t2.settingName = "activated")';
		$list = $db->getArray($q);
		d($list);
		
	} else {

		echo '<h1>User lists</h1>';

		echo '<a href="?notactivated">Not activated</a><br/>';
		echo '<a href="?activated">Activated</a><br/>';
		echo '<a href="?removed">Removed</a><br/>';
		echo '<a href="?online">Users online</a><br/>';
	}


	require($project.'design_foot.php');
?>
