<?php
/**
 * Converts svtrapport.se flash player feeds into XSPF playlists
 *
 * XSPF playlists are known to be compatible with: VLC, Totem
 */

require_once('/var/www/core_dev/trunk/core/input_feed.php');
require_once('/var/www/core_dev/trunk/core/io_playlist.php');
require_once('/var/www/core_dev/trunk/core/class.Cache.php');


$url = 'https://styggve.dyndns.org:61001/webtv/playrapport.php?format=xspf';

$url = 'http://media.svt.se/download/mcc/vision/20090919/DENSTORARESAN-PLAY_tote.asx';


$rss = new input_feed();
$res = $rss->getList($url);
if (!$res) die("input_feed FAIL\n");
setlocale(LC_TIME, 'sv_SE.UTF8');

d($res); die;

$pl = new Playlist();
$pl->addList($res);
echo $pl->output('pls');

?>
