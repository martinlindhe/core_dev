<?php

namespace cd;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__).'/../core/');

require_once('Cache.php');

$cache = new Cache();
$cache->setDebug();
$cache->setTimeout(60);

if (!$cache->isActive() )
    die("CACHE NOT ACTIVE\n");

$x = $cache->get('coredev_cache_test');
if ($x) {
    echo "CACHED: ".$x."\n";
} else {
    $cache->set('coredev_cache_test', time());
    echo "STORED\n";
}

?>
