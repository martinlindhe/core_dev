<?php

//TODO: show memcache version: http://se.php.net/manual/en/memcached.getversion.php
//TODO: show stats: http://se.php.net/manual/en/memcached.getstats.php

$store = TempStore::getInstance();

echo '<hr/>';
echo 'TempStore servers:<br/>';
foreach ($store->getServerPool() as $serv) {
    echo $serv->render().'<br/>';
}


//print_r( $store->getServerStats() );  //XXX dont seem to work 2011-01-01

//echo $store->getServerVersions(); ///XXX dont seem to work 2011-01-01

?>
