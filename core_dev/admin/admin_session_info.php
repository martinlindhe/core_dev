<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireSuperAdmin();

require('design_admin_head.php');

$session->showInfo();

require('design_admin_foot.php');
?>
