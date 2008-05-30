<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireAdmin();

if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;
$userId = $_GET['id'];

if ($session->isSuperAdmin && isset($_GET['delete'])) {
	Users::removeUser($userId);
}

require($project.'design_head.php');

echo createMenu($admin_menu, 'blog_menu');

if (!Users::exists($userId)) {
	echo '<h2>No such user exists</h2>';
	require($project.'design_foot.php');
	die;
}

echo '<h1>User admin for '.Users::getName($userId).'</h1>';

if ($session->isSuperAdmin) {
	echo '<a href="'.$_SERVER['PHP_SELF'].'?id='.$userId.'&amp;delete">Delete user</a><br/><br/>';
}

editUserdataSettings($userId);

showComments(COMMENT_USER, $userId);

require($project.'design_foot.php');
?>
