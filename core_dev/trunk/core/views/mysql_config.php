<?php
/**
 * Shows current settings
 */

echo '<div class="item">';
echo '<h2>Current database configuration</h2>';
echo 'DB driver: '.get_class($db).'<br/>';
echo 'Server version: '.$db->db_handle->server_info.'<br/>';
echo 'Client version: '.$db->db_handle->client_info.'<br/>';
echo 'Host: '.$db->host.':'.$db->port.'<br/>';
echo 'Login: '.$db->username.':'.($db->password ? $db->password : '(blank)').'<br/>';
echo 'Database: '.$db->database.'<br/>';
echo 'Configured charset: '.$db->charset;
echo '</div><br/>';

echo '<div class="item">';
echo '<h2>DB host features</h2>';
$db_time = $db->getOneItem('SELECT NOW()');
echo 'DB time: '.$db_time.' (webserver time: '.now().')<br/>';
echo '</div><br/>';

echo '<div class="item">';
echo '<h2>DB driver specific settings</h2>';
echo 'Host info: '.$db->db_handle->host_info.'<br/>';
echo 'Connection character set: '.$db->db_handle->character_set_name().'<br/>';
echo 'Last error: '.$db->db_handle->error.'<br/>';
echo 'Last errno: '.$db->db_handle->errno;
echo '</div><br/>';

echo '<div class="item">';

//Show MySQL query cache settings
$data = $db->getMappedArray('SHOW VARIABLES LIKE "%query_cache%"');
if ($data['have_query_cache'] == 'YES') {
    echo '<h2>MySQL query cache settings</h2>';
    echo 'Type: '. $data['query_cache_type'].'<br/>';        //valid values: ON, OFF or DEMAND
    echo 'Size: '. formatDataSize($data['query_cache_size']).' (total size)<br/>';
    echo 'Limit: '. formatDataSize($data['query_cache_limit']).' (per query)<br/>';
    echo 'Min result unit: '. formatDataSize($data['query_cache_min_res_unit']).'<br/>';
    echo 'Wlock invalidate: '. $data['query_cache_wlock_invalidate'].'<br/><br/>';

    //Current query cache status
    $data = $db->getMappedArray('SHOW STATUS LIKE "%Qcache%"', 'Variable_name', 'Value');
    echo '<h2>MySQL query cache status</h2>';
    echo 'Hits: '. formatNumber($data['Qcache_hits']).'<br/>';
    echo 'Inserts: '. formatNumber($data['Qcache_inserts']).'<br/>';
    echo 'Queries in cache: '. formatNumber($data['Qcache_queries_in_cache']).'<br/>';
    echo 'Total blocks: '. formatNumber($data['Qcache_total_blocks']).'<br/>';
    echo '<br/>';
    echo 'Not cached: '. formatNumber($data['Qcache_not_cached']).'<br/>';
    echo 'Free memory: '. formatDataSize($data['Qcache_free_memory']).'<br/>';
    echo '<br/>';
    echo 'Free blocks: '. formatNumber($data['Qcache_free_blocks']).'<br/>';
    echo 'Lowmem prunes: '. formatNumber($data['Qcache_lowmem_prunes']);
} else {
    echo '<h2>MySQL query cache is disabled</h2>';
}

echo '</div>';

?>
