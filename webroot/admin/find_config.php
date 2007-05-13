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
		'/admin/admin.php'.getProjectPath(0) => 'Admin start',
		'/admin/admin_userdata.php'.getProjectPath(0) => 'Userdata',
		'/admin/admin_stopwords.php'.getProjectPath(0) => 'Stopwords',
		'/admin/admin_moderationqueue.php'.getProjectPath(0) => 'Moderation queue',
		'/admin/admin_contact_groups.php'.getProjectPath(0) => 'Contact groups',
		'/admin/admin_events.php'.getProjectPath(0) => 'Event log',
		'/admin/admin_compat_check.php'.getProjectPath(0) => 'Compat check',
		'/admin/admin_statistics.php'.getProjectPath(0) => 'stats',
		'/admin/admin_db_info.php'.getProjectPath(0) => '$db',
		'/admin/admin_session_info.php'.getProjectPath(0) => '$session',
		'/admin/admin_phpinfo.php'.getProjectPath(0) => 'PHP'
	);
?>