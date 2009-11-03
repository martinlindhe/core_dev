<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('io_newsfeed.php');

$url = 'http://ezrss.it/search/index.php?show_name=24&show_name_exact=true&mode=rss';

//$url = 'http://martin-lindhes.blogspot.com/feeds/posts/default'; //atom
//$url = 'http://martin-lindhes.blogspot.com/feeds/posts/default?alt=rss';


$url = 'https://styggve.dyndns.org:61001/webtv/apple.php?format=rss';
//$url = 'http://www.rssboard.org/files/sample-rss-2.xml';

//$url = 'http://media.svt.se/download/mcc/vision/kluster/20090814/1131902-RAPPORT1200-PLAY-NY5.asx';




$feed = new NewsFeed();
$feed->load($url);

$list = $feed->getItems();

d($list);
die;

/*
$list[] = array(
	'pubdate' => 127652734,
	'link' => 'http://example.com/news/1',
	'title' => 'Latest news',
	'desc' => 'A short summary'
);

$list[] = array(
	'pubdate' => 127652224,
	'link' => 'http://example.com/news/2',
	'title' => 'Some older news',
	'desc' => 'A short summary'
);

$list[] = array(
	'pubdate' => 127651734,
	'link' => 'http://example.com/news/3',
	'title' => 'Very old stuff',
	'desc' => 'A short summary'
);

$feed = new NewsFeed();

$feed->addList($list);
$feed->output('rss2');
*/


?>
