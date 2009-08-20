<?php
/**
 * $Id$
 */

require_once('find_config.php');
$h->session->requireSuperAdmin();

require('design_admin_head.php');

echo '<h1>Manage contacts</h1>';

echo 'Here you can create/modify the contact types that users can classify their friends in.<br/><br/>';

manageCategoriesDialog(CATEGORY_CONTACT);

require('design_admin_foot.php');
?>
