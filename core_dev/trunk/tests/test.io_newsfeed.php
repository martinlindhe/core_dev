<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require('io_newsfeed.php');

$url = 'http://ezrss.it/search/index.php?show_name=24&show_name_exact=true&mode=rss';

//$url = 'http://martin-lindhes.blogspot.com/feeds/posts/default'; //atom
//$url = 'http://martin-lindhes.blogspot.com/feeds/posts/default?alt=rss';

$url = 'https://styggve.dyndns.org:61001/webtv/playrapport.php?format=rss';

//$url = 'http://media.svt.se/download/mcc/vision/kluster/20090814/1131902-RAPPORT1200-PLAY-NY5.asx';


$feed = new NewsFeed();
$feed->load($url);
d($feed->getList() );

?>
