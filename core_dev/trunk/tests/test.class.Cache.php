<?php

require_once('/var/www/core_dev/trunk/core/class.Cache.php');

$pool = array(
'127.0.0.1',
'192.168.77.220:27788'
);

$cache = new Cache($pool);

$x = $cache->get('coredev_cache_test');
if ($x) {
	echo "CACHED: ".$x."\n";
} else {
	$cache->set('coredev_cache_test', time());
	echo "STORED\n";
}

?>
