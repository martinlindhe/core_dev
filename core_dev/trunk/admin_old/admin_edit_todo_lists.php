<?php
/**
 * $Id$
 */

require_once('find_config.php');
$h->session->requireSuperAdmin();

require('design_admin_head.php');

echo '<h1>Edit todo list categories</h1>';

manageCategoriesDialog(CATEGORY_TODOLIST);

echo '<br/><br/>';
echo '<a href="admin_current_work.php">Back to current work</a>';

require('design_admin_foot.php');
?>
