<?php

$rand_id = mt_rand();

echo js_embed(
'function toggle_tempstore()'.
'{'.
    'var e = document.getElementById("tss_'.$rand_id.'");'.
    'e.style.display = (e.style.display ? "" : "none");'.
'}'
);

echo ' | <a href="#" onclick="return toggle_tempstore();">tmp</a>';

$store = TempStore::getInstance();

$css =
'height:250px;'.
'display:none;'.
'overflow:auto;'.
'padding:4px;'.
'border:#000 1px solid;';

echo '<div id="tss_'.$rand_id.'" style="'.$css.'">';

foreach ($store->getServerStats() as $host => $stat)
{
    echo '<h3>TempStore server '.$host.'</h3>';
    echo 'Uptime: '.elapsed_seconds($stat['uptime']).'<br/>';
    echo 'Server time: '.sql_datetime($stat['time']).'<br/>';
    echo 'Threads: '.$stat['threads'].'<br/>';

    echo 'Curr items: '.$stat['curr_items'].'<br/>';
    echo 'Total items: '.$stat['total_items'].'<br/>';
    echo 'Max bytes: '.byte_count($stat['limit_maxbytes']).'<br/>';

    echo 'Curr connections: '.$stat['curr_connections'].'<br/>';
    echo 'Total connections: '.$stat['total_connections'].'<br/>';

    echo 'Bytes: '.byte_count($stat['bytes']).'<br/>';  // ????
    echo 'Cmd get: '.$stat['cmd_get'].'<br/>';
    echo 'Cmd set: '.$stat['cmd_set'].'<br/>';
    echo 'Get hits: '.$stat['get_hits'].'<br/>';
    echo 'Get misses: '.$stat['get_misses'].'<br/>';
    echo 'Evictions: '.$stat['evictions'].'<br/>';  // ????
    echo 'Bytes read: '.byte_count($stat['bytes_read']).'<br/>';
    echo 'Bytes written: '.byte_count($stat['bytes_written']).'<br/>';
    echo 'Memcached version '.$stat['version'].'<br/>';
}

echo '</div>';

?>

