<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireAdmin();

require('design_admin_head.php');

echo '<h1>Admin overview</h1>';

if (!empty($config['moderation']['enabled'])) {
	echo 'Moderation: <a href="admin_moderation.php">'.getModerationQueueCount().' objects</a><br/>';
}
if (!empty($config['feedback']['enabled'])) {
	echo 'Feedback: <a href="admin_feedback.php">'.getFeedbackCnt().' entries</a><br/>';
}
echo '<br/>';

echo 'Registered users: <a href="admin_list_users.php">'.Users::cnt().'</a><br/>';
echo 'Webmasters: <a href="admin_list_users.php?mode='.USERLEVEL_WEBMASTER.'">'.Users::webmasterCnt().'</a><br/>';
echo 'Admins: <a href="admin_list_users.php?mode='.USERLEVEL_ADMIN.'">'.Users::adminCnt().'</a><br/>';
echo 'SuperAdmins: <a href="admin_list_users.php?mode='.USERLEVEL_SUPERADMIN.'">'.Users::superAdminCnt().'</a><br/>';
echo 'Users logged in: <a href="admin_users.php?online">'.Users::onlineCnt().'</a><br/>';

require('design_admin_foot.php');
?>
