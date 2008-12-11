<?php

require('/var/www/core_dev/core/input_rss.php');

function rssCallback($data)
{
	print_r($data);
}

$url = 'http://tvrss.net/search/index.php?show_name=24&show_name_exact=true&mode=rss';
$url = 'http://www.dn.se/DNet/custom-jsp/rss.jsp?d=1399&numItems=20';

$data = file_get_contents($url);

$rss = new rss_input();
$rss->parse($data, 'rssCallback');

//print_r($feed);

?>
