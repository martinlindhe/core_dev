<?php
/**
 * $Id$
 */

//XXX: what's the point of this file?

require_once('find_config.php');
$session->requireSuperAdmin();

require($project.'design_head.php');
	
echo createMenu($admin_menu, 'blog_menu');
echo createMenu($super_admin_menu, 'blog_menu');
echo createMenu($super_admin_tools_menu, 'blog_menu');

echo '<h1>Add admin</h1>';
echo 'Select a user to promote to admin from the <a href="admin_list_users.php'.getProjectPath(0).'">user list</a>.';

require($project.'design_foot.php');
?>
