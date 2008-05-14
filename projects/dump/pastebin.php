<?php

require_once('config.php');

require('design_head.php');

echo '<h1>Pastebin</h1>';

showComments(COMMENT_PASTEBIN, 0, 80, 30);

require('design_foot.php');
?>
