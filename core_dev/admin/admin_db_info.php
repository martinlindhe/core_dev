<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireSuperAdmin();

require('design_admin_head.php');

$db->showConfig();

require('design_admin_foot.php');
?>
