<?php
/**
 * Converts svtrapport.se flash player feeds into XSPF playlists
 *
 * XSPF playlists are known to be compatible with: VLC, Totem
 */

require_once('/var/www/core_dev/core/input_rss.php');
require_once('/var/www/core_dev/core/output_xspf.php');
require_once('/var/www/core_dev/core/class.Cache.php');


$url = 'http://xml.svtplay.se/v1/teaser/list/103913/?start=1&num=100&vformat=flv&orderBy=editorial';

$rss = new rss_input();
$res = $rss->parse($url);

setlocale(LC_TIME, 'sv_SE.UTF8');

$pl = new output_playlist();
//$pl->format = 'xspf';
$pl->format = 'm3u';
$pl->addList($res);
echo $pl->output();

?>
