<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('input_feed.php');

/**
 * Example in how to make a rss fetcher that only notifies on new content in the feed
 */
function rssCallback($data)
{
	//print_r($data);
}

$url = 'http://ezrss.it/search/index.php?show_name=24&show_name_exact=true&mode=rss';

//$url = 'http://martin-lindhes.blogspot.com/feeds/posts/default'; //atom
//$url = 'http://martin-lindhes.blogspot.com/feeds/posts/default?alt=rss';

$url = 'https://styggve.dyndns.org:61001/xspf/playrapport.php?format=atom';

//$url = 'http://media.svt.se/download/mcc/vision/kluster/20090814/1131902-RAPPORT1200-PLAY-NY5.asx';

$url = 'https://styggve.dyndns.org:61001/xspf/aftonbladet.php?format=m3u';


$feed = new input_feed();
//$res = $feed->parseRSS($url, 'rssCallback');

$res = $feed->fetch($url, 'rssCallback');

print_r($res);

?>
