<?php

require_once('config.php');
require('design_head.php');

echo '<h1>Issues</h1>';

$list = getIssues(0);
d($list);


require('design_foot.php');
?>
