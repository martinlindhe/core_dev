<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('IconReader.php');

$in = '/devel/core_dev/trunk/tests/ICO_files/32x32x4-2.ico';


foreach (IconReader::getImages($in) as $idx => $i)
    imagepng($i, 'out4'.$idx.'.png');


?>
