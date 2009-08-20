<?php
/**
 * $Id$
 */

require_once('find_config.php');
require_core('atom_polls.php');

$h->session->requireAdmin();

require('design_admin_head.php');

echo '<h1>Polls</h1>';

managePolls(POLL_SITE);

require('design_admin_foot.php');
?>
