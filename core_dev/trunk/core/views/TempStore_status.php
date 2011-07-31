<?php

$tempstore_div = 'tss_'.mt_rand();

echo ' | <a href="#" onclick="return toggle_el(\''.$tempstore_div.'\');">tmp</a> ';

$temp = TempStore::getInstance();

$css =
'display:none;'.
'overflow:auto;'.
'padding:4px;'.
'border:#000 1px solid;';

echo '<div id="'.$tempstore_div.'" style="'.$css.'">';

foreach ($temp->getServerStats() as $host => $stat)
{
    echo 'Used memory: '.byte_count($stat['bytes']).' (of '.byte_count($stat['limit_maxbytes']).')<br/>';
    echo 'Bytes read: '.byte_count($stat['bytes_read']).'<br/>';
    echo 'Bytes written: '.byte_count($stat['bytes_written']).'<br/>';

    echo '<br/>';
    echo 'Curr '.$stat['curr_items'].' items, '.$stat['curr_connections'].' connections<br/>';
    echo 'Total '.$stat['total_items'].' items, '.$stat['total_connections'].' connections<br/>';
    echo '<br/>';

    echo 'Cmd: '.$stat['cmd_get'].' get, '.$stat['cmd_set'].' set<br/>';
    echo 'Get: '.$stat['get_hits'].' hits, '.$stat['get_misses'].' misses<br/>';
    echo 'Evictions: '.$stat['evictions'].'<br/>';  // ????

//    echo 'Threads: '.$stat['threads'].'<br/>';
    echo 'Server: <b>'.$host.'</b> running <b>memcached '.$stat['version'].'</b><br/>';
    echo 'Server time: '.sql_datetime($stat['time']).'<br/>';
    echo 'Uptime: '.elapsed_seconds($stat['uptime']).'<br/>';
}

echo '</div>';

?>
