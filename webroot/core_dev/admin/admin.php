<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');
	
	echo createMenu($admin_menu, 'blog_menu');
	if ($session->isSuperAdmin) echo createMenu($super_admin_menu, 'blog_menu');

	echo 'Admin overview<br/><br/>';
	
	if (!empty($config['moderation']['enabled'])) echo 'Moderation: <a href="admin_moderation.php?pr=sample">'.getModerationQueueCount().' objects</a><br/>';
	if (!empty($config['feedback']['enabled'])) echo 'Feedback: '.getFeedbackCnt().' entries<br/>';
	echo '<br/>';
	echo 'Registered users: <a href="admin_list_users.php'.getProjectPath(false).'">'.getUsersCnt().'</a><br/>';
	echo 'Admins: <a href="admin_list_users.php?mode=1'.getProjectPath().'">'.getAdminsCnt().'</a><br/>';
	echo 'SuperAdmins: <a href="admin_list_users.php?mode=2'.getProjectPath().'">'.getSuperAdminsCnt().'</a><br/>';
	echo 'Users logged in: <a href="/sample/users_online.php">'.getUsersOnlineCnt().'</a><br/>';

	require($project.'design_foot.php');
?>