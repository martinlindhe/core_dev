<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('service_metadata_opensubtitles.php');


$x = OpenSubtitlesHash('/media/tvserier/tvserier/Heroes/Heroes.S04E10.720p.HDTV.X264-DIMENSION.mkv');

echo $x.ln();
?>
