<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');
	
	echo createMenu($admin_menu, 'blog_menu');
	if ($session->isSuperAdmin) echo createMenu($super_admin_menu, 'blog_menu');
?>

	Admin overview<br/><br/>
	
	Moderation queue: <a href="admin_moderationqueue.php?pr=sample"><?=getModerationQueueCount()?> items</a><br/>
	FAQ questions: XXX<br/>
	<br/>
	Registered users: <?=getUsersCnt()?><br/>
	Users logged in: <a href="/sample/users_online.php"><?=getUsersOnlineCnt()?></a><br/>

<?
	require($project.'design_foot.php');
?>