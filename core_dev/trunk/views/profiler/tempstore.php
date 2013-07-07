<?php
/**
 * Shows detailed information of all connected memcached servers, which is used by the TempStore class
 */

namespace cd;

$tempstore_div = 'tss_'.mt_rand();

echo ' | '.ahref_js('cache', "return toggle_el('".$tempstore_div."')").' ';

$temp = TempStore::getInstance();

$css =
'display:none;'.
'overflow:auto;'.
'padding:4px;'.
'border:#000 1px solid;';

echo '<div id="'.$tempstore_div.'" style="'.$css.'">';

// TODO one view for memcached, one for Redis

// memcached stats:
if ($temp instanceof TempStoreRedis) {

    $config = $temp->getServerConfig();
//d($config);

    $info = $temp->getServerInfo();
//d($info);
    echo 'Redis '.$info['redis_version'].', '.$info['redis_mode'].' mode<br/>';
    echo 'at <b>'.$config['bind'].':'.$config['port'].'</b><br/>';
    echo 'Uptime <b>'.elapsed_seconds($info['uptime_in_seconds']).'</b><br/>';
    echo 'Connected clients: <b>'.$info['connected_clients'].'</b><br/>';
    echo 'Used memory: <b>'.byte_count($info['used_memory']).'</b><br/>';

    echo '</div>';
    return;
}

$pool = $temp->getServerStats();

if (!$pool) {
    echo 'No server configured';
    echo '</div>';
    return;
}

foreach ($pool as $host => $stat)
{
    echo 'Read: <b>'.byte_count($stat['bytes_read']).'</b><br/>';
    echo 'Written: <b>'.byte_count($stat['bytes_written']).'</b><br/>';
    echo 'Used memory: <b>'.byte_count($stat['bytes']).'</b>'.
    ' (<b>'.round($stat['bytes'] / $stat['limit_maxbytes'] * 100, 1).'%</b>'.

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

echo '</div>';

?>
