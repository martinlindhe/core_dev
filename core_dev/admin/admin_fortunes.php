<?php
/**
 * $Id$
 */

require_once('find_config.php');
require_core('atom_polls.php');

$session->requireAdmin();

require($project.'design_head.php');

echo createMenu($admin_menu, 'blog_menu');

managePolls(POLL_FORTUNE);

require($project.'design_foot.php');
?>
