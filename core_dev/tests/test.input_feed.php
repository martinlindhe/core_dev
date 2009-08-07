<?php

require('/var/www/core_dev/core/input_feed.php');

/**
 * Example in how to make a rss fetcher that only notifies on new content in the feed
 */
function rssCallback($data)
{
	$sha1 = sha1($data['link']);	//may want to use other factors to determine the uniqeness

	$cache_path = '/tmp/rsstest/';
	if (!file_exists($cache_path)) mkdir($cache_path);
	if (file_exists($cache_path.$sha1)) return;

	//only displayed if item is new
	print_r($data);

	touch($cache_path.$sha1);
}

$url = 'http://tvrss.net/search/index.php?show_name=24&show_name_exact=true&mode=rss';

$data = file_get_contents($url);

$rss = new input_feed();
$rss->parse($data, 'rssCallback');

//print_r($feed);

?>
