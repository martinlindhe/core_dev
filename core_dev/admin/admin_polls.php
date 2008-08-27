<?php
/**
 * $Id$
 */

require_once('find_config.php');
require_core('atom_polls.php');

$session->requireAdmin();

require('design_admin_head.php');

managePolls(POLL_SITE);

require('design_admin_foot.php');
?>
