<?php
/**
 * $Id$
 */

require_once('find_config.php');
$h->session->requireSuperAdmin();

require('design_admin_head.php');

echo '<h1>Manage userfile areas</h1>';

echo 'Here you can create/modify the global categories available for userfile areas.<br/><br/>';

manageCategoriesDialog(CATEGORY_USERFILE);

require('design_admin_foot.php');
?>
