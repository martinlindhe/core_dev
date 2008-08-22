<?php
/**
 * $Id$
 *
 * Super admin tool to update tblFiles (and tblChecksums) according to data on disc.
 * Updates file size, mime types, checksums and media types
 */

require_once('find_config.php');
$session->requireSuperAdmin();

require($project.'design_head.php');

echo createMenu($admin_menu, 'blog_menu');
echo createMenu($super_admin_menu, 'blog_menu');
echo createMenu($super_admin_tools_menu, 'blog_menu');

echo '<h2>File checker utility</h2>';

$tot = Files::getFileCount();

echo $tot;

require($project.'design_foot.php');
?>
