<?php
/**
 * $Id$
 */

require_once('find_config.php');
$h->session->requireAdmin();

require('design_admin_head.php');

echo '<h1>Admin overview</h1>';

if (!empty($config['moderation']['enabled'])) {
    echo 'Moderation: <a href="admin_moderation.php">'.getModerationQueueCount().' objects</a><br/>';
}
if (!empty($config['feedback']['enabled'])) {
    echo 'Feedback: <a href="admin_feedback.php">'.getFeedbackCnt().' entries</a><br/>';
}
echo '<br/>';

require('design_admin_foot.php');
?>
