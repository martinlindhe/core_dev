<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireSuperAdmin();

require('design_admin_head.php');

echo '<h1>Current session information</h1>';

$session->showInfo();

require('design_admin_foot.php');
?>
