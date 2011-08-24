<?php
/**
 * Shows information about current MySQL connection
 */

//TODO: present data in pretty tables
//TODO: use pie charts to show percentage of used memory etc

$session->requireSuperAdmin();

echo '<h1>Default MySQL db information</h1>';
echo 'Driver: '.get_class($db).'<br/>';
echo 'Server version: <b>'.$db->db_handle->server_info.'</b><br/>';
echo 'Client version: <b>'.$db->db_handle->client_info.'</b><br/>';
echo 'Host: <b>'.$db->host.':'.$db->port.'</b><br/>';
echo 'Username: <b>'.$db->username.'</b><br/>';
// echo 'Password: '.($db->password ? $db->password : '(blank)').'<br/>';
echo 'Database: '.$db->database.'<br/>';
echo 'Configured charset: '.$db->charset.'<br/>';
echo '<br/>';

echo '<h2>Database time</h2>';
$db_time = $db->getOneItem('SELECT NOW()');
echo 'Database time: '.$db_time.'<br/>';
echo 'Webserver time: '.now().'<br/>';

$uptime = $db->getOneRow('SHOW STATUS WHERE Variable_name="Uptime"');
echo 'Database uptime: '.elapsed_seconds($uptime['Value']).'<br/>';

echo '<br/>';

echo '<h2>Driver specific settings</h2>';
echo 'Host info: '.$db->db_handle->host_info.'<br/>';
echo 'Connection character set: '.$db->db_handle->character_set_name().'<br/>';
echo 'Last error: '.$db->db_handle->error.'<br/>';
echo 'Last errno: '.$db->db_handle->errno.'<br/>';
echo '<br/>';

// show MySQL query cache settings
$data = $db->getMappedArray('SHOW VARIABLES LIKE "%query_cache%"');
if ($data['have_query_cache'] == 'YES') {
    echo '<h2>MySQL query cache settings</h2>';
    echo 'Type: '. $data['query_cache_type'].'<br/>';        //valid values: ON, OFF or DEMAND
    echo 'Size: '. formatDataSize($data['query_cache_size']).' (total size)<br/>';
    echo 'Limit: '. formatDataSize($data['query_cache_limit']).' (per query)<br/>';
    echo 'Min result unit: '. formatDataSize($data['query_cache_min_res_unit']).'<br/>';
    echo 'Wlock invalidate: '. $data['query_cache_wlock_invalidate'].'<br/><br/>';

    // current query cache status
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

?>
