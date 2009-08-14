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

$rss = new input_feed();
$res = $rss->parseRSS($url, 'rssCallback');

//print_r($res);

?>
