<?php

namespace cd;

if (!($temp instanceof TempStoreMemcached)) {
    return;
}

$pool = $temp->getServerStats();

if (!$pool) {
    echo 'No server configured';
    return;
}

foreach ($pool as $host => $stat) {
    $pct = 0;
    if ($stat['limit_maxbytes']) {
        $pct = $stat['bytes'] / $stat['limit_maxbytes'];
    }

    echo 'Read: <b>'.byte_count($stat['bytes_read']).'</b><br/>';
    echo 'Written: <b>'.byte_count($stat['bytes_written']).'</b><br/>';
    echo 'Used memory: <b>'.byte_count($stat['bytes']).'</b>'.
    ' (<b>'.round($pct * 100, 1).'%</b>'.

    ' of <b>'.byte_count($stat['limit_maxbytes']).'</b>)'.
    '<br/>';
    echo '<br/>';

    echo 'Get: <b>'.$stat['get_hits'].'</b> hits, <b>'.$stat['get_misses'].'</b> misses<br/>';
    echo 'Cmd: <b>'.$stat['cmd_get'].'</b> get, <b>'.$stat['cmd_set'].'</b> set<br/>';
    echo '<br/>';

    echo 'Currently <b>'.$stat['curr_items'].'</b> items, <b>'.$stat['curr_connections'].'</b> connections<br/>';
    echo 'Total <b>'.$stat['total_items'].'</b> items, <b>'.$stat['total_connections'].'</b> connections<br/>';
    echo '<br/>';

    // ???
    echo 'Evictions: <b>'.$stat['evictions'].'</b><br/>';
    echo 'Threads: <b>'.$stat['threads'].'</b><br/>';
    echo '<br/>';

    echo 'Server: <b>'.$host.'</b><br/>';
    echo 'Software: <b>memcached '.$stat['version'].'</b><br/>';

    echo 'Local time: <b>'.sql_datetime($stat['time']).'</b><br/>';
    echo 'Uptime: <b>'.elapsed_seconds($stat['uptime']).'</b><br/>';
}
