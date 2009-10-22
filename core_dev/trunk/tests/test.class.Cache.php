<?php

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('class.Cache.php');

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
