<?php
/**
 * $Id$
 */

require_once('find_config.php');
$session->requireAdmin();

require('design_admin_head.php');

echo '<h1>Event log</h1>';

$db->showEvents();

require('design_admin_foot.php');
?>
