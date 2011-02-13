<?php
/**
 * $Id$
 *
 * Shows page load time & other information
 */

$rand_id = mt_rand();

echo js_embed(
//Toggles element with name "n" between visible and hidden
'function toggle_page_profiler()'.
'{'.
    'var e = document.getElementById("page_prof_'.$rand_id.'");'.
    'e.style.display = (e.style.display ? "" : "none");'.
'}'
);

echo '| <a href="#" onclick="return toggle_page_profiler();">load</a>';

$css =
'height:200px;'.
'display:none;'.
'overflow:auto;'.
'padding:4px;'.
'background-color:#eee;'.
'border:#000 1px solid;';

echo '<div id="page_prof_'.$rand_id.'" style="'.$css.'">';

$total_time = microtime(true) - $page->getStartTime();

if (isset($db) && is_object($db)) {
    $sql_time   = $db->getTotalQueryTime();
    $php_time   = $total_time - $sql_time - $db->time_connect;

    echo 'Load: <b>'.round($total_time, 2).'s</b> ';
    echo ' (DB connect: '.round($db->time_connect, 2).'s, ';
    echo 'SQL: '.round($sql_time, 2).'s, ';
    echo 'PHP: '.round($php_time, 2).'s)';
} else {
    echo 'Load: <b>'.round($total_time, 2).'s</b>';
}
echo '<br/><br/>';

echo 'Webserver <b>'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'</b> with <b>PHP '.phpversion().'</b> from <b>'.$_SERVER['SERVER_SOFTWARE'].'</b> with '.$_SERVER['GATEWAY_INTERFACE'].'<br/>';
echo 'Client <b>'.$_SERVER['REMOTE_ADDR'].'</b> with <b>'.$_SERVER['HTTP_USER_AGENT'].'</b><br/>';

echo dm(); //memory usage
echo 'Server time: '.date('r T').'<br/>';

echo 'Server uptime: '.elapsed_seconds( uptime() ).'<br/>';


if (function_exists('apc_cache_info')) {
    //XXX move somewhere else
    $conv = new ConvertDatasize();

    $info = apc_cache_info('', true);
//d($info);
    echo '<b>APC:</b> using <b>'.round($conv->convLiteral($info['mem_size'], 'MiB'), 2).' MiB</b> ('.$info['num_hits'].' hits, '.$info['num_misses'].' misses, '.$info['num_entries'].' entries)';

//    d( apc_sma_info() );
}

echo '</div>';

?>
