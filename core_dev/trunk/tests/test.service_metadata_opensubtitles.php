<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('service_metadata_opensubtitles.php');


$x = OpenSubtitlesHash('/media/downloads/dump/Cast.Away.2000.720p.BluRay.DTS.x264-ESiR/Cast.Away.2000.720p.BluRay.DTS.x264-ESiR.mkv');

echo $x.ln();
?>
