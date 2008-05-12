<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireSuperAdmin();

require($project.'design_head.php');

echo createMenu($admin_menu, 'blog_menu');
echo createMenu($super_admin_menu, 'blog_menu');
echo createMenu($super_admin_tools_menu, 'blog_menu');

echo '<h1>Manage userfile areas</h1>';

echo 'Here you can create/modify the global categories available for userfile areas.<br/><br/>';
	
manageCategoriesDialog(CATEGORY_USERFILE);

require($project.'design_foot.php');
?>
