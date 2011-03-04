<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('IconReader.php');

$base_dir = '/devel/core_dev/trunk/tests/ICO_files/';

$files = array(
//'16x16x1-1.ico',  //XXXX 1 bpp unsupported
/*'16x16x4-1.ico',
'32x32x4-1.ico',
'32x32x4-2.ico',
'multi-1.ico',
'multi-3.ico',*/
'vista_icon.ico',  // from Spotify.exe, has PNG resource. XXX nautilus 2.32 dont show thumbnail but should be fixed
//'minor-bug-1.ico',  //first 2 images have green borders where it should be transparency (???) "icotool" from icoutils package does the same
);

$out_dir = 'ico_png/';
if (!is_dir($out_dir))
    mkdir($out_dir);

foreach ($files as $in) {
    echo 'Reading '.$in.":\n";
    print_r( IconReader::listLmages($base_dir.$in) );

    foreach (IconReader::getImages($base_dir.$in) as $idx => $i)
        imagepng($i, $out_dir.basename($in).'-'.($idx+1).'.png');

}




?>
