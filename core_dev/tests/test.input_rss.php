<?php

require('/var/www/core_dev/core/input_rss.php');

//playing with feeds from http://tvrss.net/shows/

$url = 'http://tvrss.net/search/index.php?show_name=24&show_name_exact=true&mode=rss';

$url = 'http://www.dn.se/DNet/custom-jsp/rss.jsp?d=1399&numItems=20';	//feed uses htmlentities etc

$data = file_get_contents($url);

$rss = new rss_input();
$feed = $rss->parse($data);

print_r($feed);

?>
