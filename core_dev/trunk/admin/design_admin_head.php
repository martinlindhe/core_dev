<?php

$meta_css[] = coredev_webroot().'css/admin.css';

$header = new xhtml_header();
echo $header->render();

$admin_menu = array(
	'admin.php' => 'Admin::',
	'admin_users.php' => 'Users',
	'admin_incoming.php' => 'Incoming',
	'admin_moderation.php' => 'Moderation',
	'admin_news.php' => 'News',
	'admin_polls.php' => 'Polls',
	'admin_fortunes.php' => 'Fortunes',
	'admin_statistics.php' => 'Stats',
	'admin_events.php' => 'Event log'
);

if (!empty($config['feedback']['enabled'])) {
   'admin/admin_feedback.php'] = 'Feedback';
}

$super_admin_menu = array(
	'admin_userdata.php' => 'Userdata',
	'admin_userfiles.php' => 'Userfiles',
	'admin_stopwords.php' => 'Stopwords',
	'admin_contacts.php' => 'Contacts',
	'admin_content_search.php' => 'Content Search',
	'admin_contact_users.php' => 'Contact users',
	'admin_todo_lists.php' => 'Todo lists'
);

$super_admin_tools_menu = array(
	'admin/admin_compat_check.php' => 'Compat check',
	'admin/admin_filecheck.php' => 'File check',
	'admin/admin_db_info.php' => '$db',
	'admin/admin_session_info.php' => '$session',
	'admin/admin_ip.php' => 'Query IP',
	'admin/admin_portcheck.php' => 'Portcheck',
	'admin/admin_phpinfo.php' => 'PHP',
	'admin/admin_ip_blocks.php' => 'IP Blocks'
);

echo xhtmlMenu($admin_menu, 'admin_menu');
if ($h->session->isSuperAdmin) {
	echo xhtmlMenu($super_admin_menu, 'admin_menu');
	echo xhtmlMenu($super_admin_tools_menu, 'admin_menu');
}

echo '<a href="'.$h->session->start_page.'"> &laquo;&laquo; BACK TO SITE</a><br/><br/>';

?>
