<?php
/**
 * $Id$
 */


$project = '../../';

require_once($project.'config.php');


$admin_menu = array(
	$config['core']['web_root'].'admin/admin.php'.getProjectPath(0) => 'Admin::',
	$config['core']['web_root'].'admin/admin_users.php'.getProjectPath(0) => 'Users',
	$config['core']['web_root'].'admin/admin_moderation.php'.getProjectPath(0) => 'Moderation',
	$config['core']['web_root'].'admin/admin_news.php'.getProjectPath(0) => 'News',
	$config['core']['web_root'].'admin/admin_polls.php'.getProjectPath(0) => 'Polls',
	$config['core']['web_root'].'admin/admin_feedback.php'.getProjectPath(0) => 'Feedback',	//todo: hide if feedback-module is disabled
	$config['core']['web_root'].'admin/admin_statistics.php'.getProjectPath(0) => 'Stats',
	$config['core']['web_root'].'admin/admin_events.php'.getProjectPath(0) => 'Event log'
);

$super_admin_menu = array(
	$config['core']['web_root'].'admin/admin_userdata.php'.getProjectPath(0) => 'Userdata',
	$config['core']['web_root'].'admin/admin_userfiles.php'.getProjectPath(0) => 'Userfiles',
	$config['core']['web_root'].'admin/admin_stopwords.php'.getProjectPath(0) => 'Stopwords',
	$config['core']['web_root'].'admin/admin_contacts.php'.getProjectPath(0) => 'Contacts',
	$config['core']['web_root'].'admin/admin_content_search.php'.getProjectPath(0) => 'Content Search',
	$config['core']['web_root'].'admin/admin_contact_users.php'.getProjectPath(0) => 'Contact users',
	$config['core']['web_root'].'admin/admin_todo_lists.php'.getProjectPath(0) => 'Todo lists'
);

$super_admin_tools_menu = array(
	$config['core']['web_root'].'admin/admin_compat_check.php'.getProjectPath(0) => 'Compat check',
	$config['core']['web_root'].'admin/admin_db_info.php'.getProjectPath(0) => '$db',
	$config['core']['web_root'].'admin/admin_session_info.php'.getProjectPath(0) => '$session',
	$config['core']['web_root'].'admin/admin_ip.php'.getProjectPath(0) => 'Query IP',
	$config['core']['web_root'].'admin/admin_portcheck.php'.getProjectPath(0) => 'Portcheck',
	$config['core']['web_root'].'admin/admin_phpinfo.php'.getProjectPath(0) => 'PHP',
	$config['core']['web_root'].'admin/admin_ip_blocks.php'.getProjectPath(0) => 'IP Blocks'
);

set_include_path($config['core']['fs_root'].'core/');
require_once('functions_forum.php');
restore_include_path();
?>
