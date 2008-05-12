<?php

/**
 * $Id$
 *
 * Returns a RSS 2.0 feed with the latest news postings
 * http://en.wikipedia.org/wiki/RSS_(file_format)
 */

require_once('find_config.php');

$_category = 0;
if (!empty($_GET['c']) && is_numeric($_GET['c'])) $_category = $_GET['c'];

$list = getPublishedNews($_category, 10);

$title = 'Nyheter';
$description = '10 senaste publicerade nyheterna';
$copyright = '';
$language = 'sv-se';
$webmaster = 'Martin Lindhe';
$server_url = 'http://www.url.com/';
$publish_date = date('r', time() );

header('Content-type: application/xml');

echo '<?xml version="1.0" encoding="utf-8"?>';
echo '<rss version="2.0">';
echo '<channel>';
echo '<title>'.$title.'</title>';
echo '<link>'.$server_url.'</link>';
echo '<description>'.$description.'</description>';
echo '<language>'.$language.'</language>';
echo '<pubDate>'.$publish_date.'</pubDate>';
echo '<copyright>'.$copyright.'</copyright>';
echo '<webMaster>'.$webmaster.'</webMaster>';
echo '<generator>uReply RSS propagator</generator>';

echo '<image>';
	echo '<title>image title</title>';
	echo '<link>'.$server_url.'</link>';
	echo '<description>Click the url</description>';
	echo '<url>http://localhost/'.$config['core']['web_root'].'gfx/icon_warning_big.png</url>';
	echo '<width>82</width>';
	echo '<height>45</height>';
echo '</image>';

$path = basename($_GET['pr']);
if ($db->escape($path) != $_GET['pr']) die;

for ($i=0; $i<count($list); $i++) {
	if (!$list[$i]['rss_enabled']) continue;

	$item_url = 'http://localhost/'.$path.'/news.php?news='.$list[$i]['newsId'];

	echo '<item>';
		echo '<title>'.$list[$i]['title'].'</title>';
		echo '<pubDate>'.$list[$i]['timeToPublish'].'</pubDate>';	//fixme: hur ska publish date formateras?

		echo '<link>'.$item_url.'</link>';
		echo '<guid>'.$item_url.'</guid>';
		echo '<description>'.$list[$i]['body'].'</description>';
	echo '</item>';
}

echo '</channel>';
echo '</rss>';
?>
