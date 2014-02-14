<?php
/**
 * Converts svtrapport.se flash player feeds into XSPF playlists
 *
 * XSPF playlists are known to be compatible with: VLC, Totem
 */

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('io_newsfeed.php');
require_once('io_playlist.php');
require_once('Cache.php');


$url = 'xxx/webtv/playrapport.php?format=xspf';

$url = 'http://media.svt.se/download/mcc/vision/kluster/20091021/PG-1133804-003A-BOOMSHAKALACK2-02.asx';

$pl = new Playlist();
$pl->load($url);

echo $pl->render('m3u');
