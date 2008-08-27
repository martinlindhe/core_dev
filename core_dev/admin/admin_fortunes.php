<?php
/**
 * $Id$
 */

require_once('find_config.php');
require_core('atom_polls.php');

$session->requireAdmin();

require('design_admin_head.php');

echo '<h1>Manage fortunes</h1>';

managePolls(POLL_FORTUNE);

require('design_admin_foot.php');;
?>
