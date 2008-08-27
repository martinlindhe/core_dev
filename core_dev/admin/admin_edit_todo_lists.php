<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireSuperAdmin();

require('design_admin_head.php');

echo 'Admin edit todo list categories<br/><br/>';

manageCategoriesDialog(CATEGORY_TODOLIST);

echo '<br/><br/>';
echo '<a href="admin_current_work.php">Back to current work</a>';

require('design_admin_foot.php');
?>
