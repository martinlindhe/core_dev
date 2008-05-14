<?php

require_once('config.php');
require('design_head.php');

echo '<h1>Site polls</h1>';

showPolls(POLL_SITE);

require('design_foot.php');
?>
