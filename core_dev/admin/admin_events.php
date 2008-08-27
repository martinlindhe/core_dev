<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireAdmin();

require('design_admin_head.php');

$db->showEvents();

require('design_admin_foot.php');
?>
