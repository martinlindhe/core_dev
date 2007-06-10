<?
	$project = '../';	//Defaults to a config.php in the directory below this one
	if (!empty($_GET['pr'])) {
		if (strpbrk($_GET['pr'], '"\'/\\%&?;:.,')) die;				//checks _pr for " ' \ / % & ? ; : . ,
		$project = preg_replace( "/[^\w\.-]+/", "_", $_GET['pr']); //bra regexp för att ta bort farliga tecken från filnamn
		if ($project != $_GET['pr']) die;	//invalid chars in path
		$project = '../../'.$project.'/';
	}

	if (!is_file($project.'config.php')) {
		$project = '../../';
		if (!is_file($project.'config.php')) die('cant find config path');
	}

	require_once($project.'config.php');

	$admin_menu = array(
		$config['core_web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin::',
		$config['core_web_root'].'admin/admin_moderation.php'.getProjectPath(0) => 'Moderation',
		$config['core_web_root'].'admin/admin_faq.php'.getProjectPath(0) => 'FAQ',
		$config['core_web_root'].'admin/admin_news.php'.getProjectPath(0) => 'News',
		$config['core_web_root'].'admin/admin_polls.php'.getProjectPath(0) => 'Polls',
		$config['core_web_root'].'admin/admin_feedback.php'.getProjectPath(0) => 'Feedback',	//todo: hide if feedback-module is disabled
		$config['core_web_root'].'admin/admin_statistics.php'.getProjectPath(0) => 'Stats',
		$config['core_web_root'].'admin/admin_events.php'.getProjectPath(0) => 'Event log',
		$config['core_web_root'].'admin/admin_userdata.php'.getProjectPath(0) => 'Userdata',			//kanske super-admin?
		$config['core_web_root'].'admin/admin_stopwords.php'.getProjectPath(0) => 'Stopwords',		//superadmin?
		$config['core_web_root'].'admin/admin_contact_groups.php'.getProjectPath(0) => 'Contact groups'	//superadmin?
	);

	$super_admin_menu = array(
		$config['core_web_root'].'admin/admin_super.php'.getProjectPath(0) => 'SuperAdmin::',
		$config['core_web_root'].'admin/admin_add_admin.php'.getProjectPath(0) => 'Add admin',
		$config['core_web_root'].'admin/admin_compat_check.php'.getProjectPath(0) => 'Compat check',
		$config['core_web_root'].'admin/admin_db_info.php'.getProjectPath(0) => '$db',
		$config['core_web_root'].'admin/admin_session_info.php'.getProjectPath(0) => '$session',
		$config['core_web_root'].'admin/admin_ip.php'.getProjectPath(0) => 'Query IP',
		$config['core_web_root'].'admin/admin_portcheck.php'.getProjectPath(0) => 'Portcheck',
		$config['core_web_root'].'admin/admin_phpinfo.php'.getProjectPath(0) => 'PHP'
	);
?>