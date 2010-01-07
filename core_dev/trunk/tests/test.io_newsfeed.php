<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('io_newsfeed.php');

die('XXX: cant really test like this');


$url = 'http://styggve.dyndns.org/webtv/apple.php?format=rss';

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
