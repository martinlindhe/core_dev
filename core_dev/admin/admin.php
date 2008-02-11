<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');

	echo createMenu($admin_menu, 'blog_menu');
	if ($session->isSuperAdmin) {
		echo createMenu($super_admin_menu, 'blog_menu');
		echo createMenu($super_admin_tools_menu, 'blog_menu');
	}

	echo 'Admin overview<br/><br/>';

	if (!empty($config['moderation']['enabled'])) {
		echo 'Moderation: <a href="admin_moderation.php'.getProjectPath(0).'">'.getModerationQueueCount().' objects</a><br/>';
	}
	if (!empty($config['feedback']['enabled'])) {
		echo 'Feedback: <a href="admin_feedback.php'.getProjectPath(0).'">'.getFeedbackCnt().' entries</a><br/>';
	}
	echo '<br/>';

	echo 'Registered users: <a href="admin_list_users.php'.getProjectPath(false).'">'.Users::cnt().'</a><br/>';
	echo 'Admins: <a href="admin_list_users.php?mode=1'.getProjectPath().'">'.Users::adminCnt().'</a><br/>';
	echo 'SuperAdmins: <a href="admin_list_users.php?mode=2'.getProjectPath().'">'.Users::superAdminCnt().'</a><br/>';
	echo 'Users logged in: <a href="admin_users_online.php">'.Users::onlineCnt().'</a><br/>';

	require($project.'design_foot.php');
?>