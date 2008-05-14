<?php

require_once('config.php');

require('design_head.php');

echo '<h1>File dump</h1>';

$files->showFiles(FILETYPE_FILEAREA_UPLOAD);

require('design_foot.php');
?>
