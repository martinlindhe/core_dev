<?php

$meta_css[] = $config['core']['web_root'].'css/admin.css';

$header = new xhtml_header();
echo $header->render();

$admin_menu = array(
	$config['core']['web_root'].'admin/admin.php' => 'Admin::',
	$config['core']['web_root'].'admin/admin_users.php' => 'Users',
	$config['core']['web_root'].'admin/admin_incoming.php' => 'Incoming',
	$config['core']['web_root'].'admin/admin_moderation.php' => 'Moderation',
	$config['core']['web_root'].'admin/admin_news.php' => 'News',
	$config['core']['web_root'].'admin/admin_polls.php' => 'Polls',
	$config['core']['web_root'].'admin/admin_fortunes.php' => 'Fortunes',
	$config['core']['web_root'].'admin/admin_statistics.php' => 'Stats',
	$config['core']['web_root'].'admin/admin_events.php' => 'Event log'
);

if (!empty($config['feedback']['enabled'])) {
    $admin_menu[$config['core']['web_root'].'admin/admin_feedback.php'] = 'Feedback';
}

$super_admin_menu = array(
	$config['core']['web_root'].'admin/admin_userdata.php' => 'Userdata',
	$config['core']['web_root'].'admin/admin_userfiles.php' => 'Userfiles',
	$config['core']['web_root'].'admin/admin_stopwords.php' => 'Stopwords',
	$config['core']['web_root'].'admin/admin_contacts.php' => 'Contacts',
	$config['core']['web_root'].'admin/admin_content_search.php' => 'Content Search',
	$config['core']['web_root'].'admin/admin_contact_users.php' => 'Contact users',
	$config['core']['web_root'].'admin/admin_todo_lists.php' => 'Todo lists'
);

$super_admin_tools_menu = array(
	$config['core']['web_root'].'admin/admin_compat_check.php' => 'Compat check',
	$config['core']['web_root'].'admin/admin_filecheck.php' => 'File check',
	$config['core']['web_root'].'admin/admin_db_info.php' => '$db',
	$config['core']['web_root'].'admin/admin_session_info.php' => '$session',
	$config['core']['web_root'].'admin/admin_ip.php' => 'Query IP',
	$config['core']['web_root'].'admin/admin_portcheck.php' => 'Portcheck',
	$config['core']['web_root'].'admin/admin_phpinfo.php' => 'PHP',
	$config['core']['web_root'].'admin/admin_ip_blocks.php' => 'IP Blocks'
);

echo createMenu($admin_menu, 'admin_menu');
if ($h->session->isSuperAdmin) {
	echo createMenu($super_admin_menu, 'admin_menu');
	echo createMenu($super_admin_tools_menu, 'admin_menu');
}

echo '<a href="'.$config['app']['web_root'].$h->session->start_page.'"> &laquo;&laquo; BACK TO SITE</a><br/><br/>';

?>
