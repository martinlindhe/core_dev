<?php
/**
 * Converts svtrapport.se flash player feeds into XSPF playlists
 *
 * XSPF playlists are known to be compatible with: VLC, Totem
 */

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('input_feed.php');
require_once('io_playlist.php');
require_once('class.Cache.php');


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
