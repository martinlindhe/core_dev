<?php
//TODO: present uptime better than in seconds, use a "rought" estimate

$rand_id = mt_rand(0,99999);

echo js_embed(
//Toggles element with name "n" between visible and hidden
'function toggle_tempstore()'.
'{'.
    'var e = document.getElementById("tss_'.$rand_id.'");'.
    'e.style.display = (e.style.display ? "" : "none");'.
'}'
);

echo ' | <a href="#" onclick="return toggle_tempstore();">tmp</a>';


$store = TempStore::getInstance();

$height = 250;

echo '<div id="tss_'.$rand_id.'" style="height:'.$height.'px;display:none; overflow: auto; padding: 4px; color: #000; background-color:#E0E0E0; border: #000 1px solid; font: 9px verdana; text-align: left;">';


foreach ($store->getServerStats() as $host => $stat) {
    echo '<h3>TempStore server '.$host.'</h3>';
    echo 'Memcached version '.$stat['version'].'<br/>';
    echo 'Server time: '.sql_datetime($stat['time']).'<br/>';
    echo 'Uptime: '.$stat['uptime'].' sec<br/>';
    echo 'Threads: '.$stat['threads'].'<br/>';

    echo 'Curr items: '.$stat['curr_items'].'<br/>';
    echo 'Total items: '.$stat['total_items'].'<br/>';
    echo 'Max bytes: '.$stat['limit_maxbytes'].'<br/>';

    echo 'Curr connections: '.$stat['curr_connections'].'<br/>';
    echo 'Total connections: '.$stat['total_connections'].'<br/>';

    echo 'Bytes: '.$stat['bytes'].'<br/>';  // ????
    echo 'Cmd get: '.$stat['cmd_get'].'<br/>';
    echo 'Cmd set: '.$stat['cmd_set'].'<br/>';
    echo 'Get hits: '.$stat['get_hits'].'<br/>';
    echo 'Get misses: '.$stat['get_misses'].'<br/>';
    echo 'Evictions: '.$stat['evictions'].'<br/>';  // ????
    echo 'Bytes read: '.$stat['bytes_read'].'<br/>';
    echo 'Bytes written: '.$stat['bytes_written'].'<br/>';
}

echo '</div>';

?>

