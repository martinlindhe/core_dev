<?php

require('/var/www/core_dev/core/input_feed.php');

/**
 * Example in how to make a rss fetcher that only notifies on new content in the feed
 */
function rssCallback($data)
{
	//print_r($data);
}

$url = 'http://tvrss.net/search/index.php?show_name=24&show_name_exact=true&mode=rss';
$url = 'http://media.svt.se/download/mcc/vision/kluster/20090814/1131902-RAPPORT1200-PLAY-NY5.asx';


$feed = new input_feed();
//$res = $feed->parseRSS($url, 'rssCallback');

$res = $feed->parseASX($url, 'rssCallback');

print_r($res);

?>
