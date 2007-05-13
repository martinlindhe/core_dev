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
		'/admin/admin.php'.getProjectPath(false) => 'Admin start',
		'/admin/admin_userdata.php'.getProjectPath(false) => 'Userdata',
		'/admin/admin_stopwords.php'.getProjectPath(false) => 'Stopwords',
		'/admin/admin_moderationqueue.php'.getProjectPath(false) => 'Moderation queue',
		'/admin/admin_contact_groups.php'.getProjectPath(false) => 'Contact groups',
		'/admin/admin_events.php'.getProjectPath(false) => 'Event log',
		'/admin/admin_compat_check.php'.getProjectPath(false) => 'Compat check',
		'/admin/admin_statistics.php'.getProjectPath(false) => 'stats',
		'/admin/admin_db_info.php'.getProjectPath(false) => '$db',
		'/admin/admin_session_info.php'.getProjectPath(false) => '$session',
		'/admin/admin_phpinfo.php'.getProjectPath(false) => 'PHP'
	);
?>