<?php
require('/var/www/core_dev/core/output_feed.php');

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

$feed = new output_feed();

$feed->addList($list);
$feed->output('rss2');

?>
