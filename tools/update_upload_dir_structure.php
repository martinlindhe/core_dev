<?php

// Needed due to changes in File.php r6641, 2012-05-15

die; // mission complete, 2012-05-15

require_once('../core/core.php');
require_once('../core/files.php');
require_once('../core/File.php');
require_once('../core/XmlDocumentHandler.php');


$dir = '/var/www/admin-ivr.unicorn.se/uploads';

$page = XmlDocumentHandler::getInstance();
$page->setUploadPath($dir);


$content = dir_get_tree($dir);

foreach ($content as $c) {
    $name = basename($c);

    $new_path = File::getUploadPath($name);

    echo "Moving ".$c." to ".$new_path."\n";
    rename($c, $new_path);
}

?>
