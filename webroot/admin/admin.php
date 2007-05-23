<?
	require_once('find_config.php');
	$session->requireAdmin();

	require($project.'design_head.php');
	
	echo createMenu($admin_menu, 'blog_menu');
	if ($session->isSuperAdmin) echo createMenu($super_admin_menu, 'blog_menu');
?>

	todo - visa lite siffror<br/><br/>
	
	Moderation queue: XXX items<br/>
	FAQ questions: XXX<br/>
	<br/>
	Registered users: XXX<br/>
	Users logged in: YYY<br/>

<?
	require($project.'design_foot.php');
?>