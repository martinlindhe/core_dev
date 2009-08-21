<?php

require_once('/var/www/core_dev/core/class.Cache.php');

$cache = new Cache();

$x = $cache->get('coredev_cache_test');
if ($x) echo "CACHED: ".$x."\n";
else {
	$cache->set('coredev_cache_test', time());
	echo "STORED\n";
}

?>
