<?
	$project = '../';	//Defaults to a config.php in the directory below this one
	if (!empty($_GET['pr'])) {
		if (strpbrk($_GET['pr'], '"\'/\\%&?;:.,')) die;				//checks _pr for " ' \ / % & ? ; : . ,
		$project = preg_replace( "/[^\w\.-]+/", "_", $_GET['pr']); //bra regexp för att ta bort farliga tecken från filnamn
		if ($project != $_GET['pr']) die;	//invalid chars in path
		$project = '../'.$project.'/';
	}

	require_once($project.'config.php');

	$admin_menu = array(
		'/admin/admin.php'.getProjectPath(0) => 'Admin::',
		'/admin/admin_moderationqueue.php'.getProjectPath(0) => 'Moderation',
		'/admin/admin_faq.php'.getProjectPath(0) => 'FAQ',
		'/admin/admin_feedback.php'.getProjectPath(0) => 'Feedback',
		'/admin/admin_statistics.php'.getProjectPath(0) => 'Stats',
		'/admin/admin_events.php'.getProjectPath(0) => 'Event log',
		'/admin/admin_userdata.php'.getProjectPath(0) => 'Userdata',			//kanske super-admin?
		'/admin/admin_stopwords.php'.getProjectPath(0) => 'Stopwords',		//superadmin?
		'/admin/admin_contact_groups.php'.getProjectPath(0) => 'Contact groups'	//superadmin?
	);

	$super_admin_menu = array(
		'/admin/admin_super.php'.getProjectPath(0) => 'SuperAdmin::',
		'/admin/admin_add_admin.php'.getProjectPath(0) => 'Add admin',
		'/admin/admin_compat_check.php'.getProjectPath(0) => 'Compat check',
		'/admin/admin_db_info.php'.getProjectPath(0) => '$db',
		'/admin/admin_session_info.php'.getProjectPath(0) => '$session',
		'/admin/admin_phpinfo.php'.getProjectPath(0) => 'PHP'
	);
?>