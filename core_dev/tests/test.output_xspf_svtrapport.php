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

$key = 'svtrapport//'.urlencode($url);
$cache = new cache();
$data = $cache->get($key);
if (!$data) {
	$data = file_get_contents($url);
	$cache->set($key, $data, 60*5); //5 minutes
}
$rss = new rss_input();
$res = $rss->parse($data);

setlocale(LC_TIME, 'sv_SE.UTF8');

$xspf = new xspf();
header('Content-type: application/xspf+xml');
echo $xspf->render($res);

?>
