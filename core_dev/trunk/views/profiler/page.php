<?php
/**
 * Shows page load time & other information
 */

require_once('HttpUserAgent.php');

$header->registerJsFunction(
'function loadtime()'.
'{'.
    'var s=(new Date()-beforeload)/1000;'.
    'document.getElementById("span_rendertime").innerHTML=s;'.
'}'
);

$header->embedJs( // code will be in <head>
'var beforeload=new Date();'.
'window.onload=loadtime;'
);

$container_id = 'cdc_'.mt_rand();
$wrapper_id   = 'cdw_'.mt_rand();

$header->embedCss(
'#'.$wrapper_id.
'{'.
    'position:fixed;'.
    'right:0;'.
    'bottom:0;'.
    'margin:2px;'.
    'padding:2px;'.
    'padding-top:0px;'.
    'border:1px dashed #aaa;'.
    'color:#000;'.
    'background-color:#fafafa;'.
    'font:9px verdana;'.
'}'.
'a.closebtn'.
'{'.
    'display:block;'.
    'float:right;'.
    'width:7px;'.
    'height:7px;'.
    'margin-left:4px;'.
    'margin-top:4px;'.
    'background:url("'.relurl('core_dev/gfx/close.gif').'");'.
'}'.
'a.closebtn:hover'.
'{'.
    'background-position:0px -7px;'.
'}'.

'a.expandbtn'.
'{'.
    'display:block;'.
    'float:left;'.
    'width:7px;'.
    'height:7px;'.
    'margin-right:4px;'.
    'margin-top:4px;'.
    'background:url("'.relurl('core_dev/gfx/close.gif').'");'.
    'background-position:0px -28px;'.
'}'.
'a.expandbtn:hover'.
'{'.
//    'background-position:0px -21px;'.  //TOODO: add lighter version of expander icon tile for hover
'}'

);



echo '<div id="'.$wrapper_id.'">'; // outer wrapper

echo ahref_js('', "return toggle_el('".$container_id."');", 'expandbtn');
echo 'core_dev';

echo '<div id="'.$container_id.'" style="display:none;">';  // inner container

if (class_exists('SqlHandler')) {
    $view = new ViewModel('views/profiler/mysql.php');
    echo $view->render();
}

if (class_exists('TempStore')) {
    $view = new ViewModel('views/profiler/memcached.php');
    echo $view->render();
}

$prof_id = 'prof_'.mt_rand();

$total_time = microtime(true) - $page->getStartTime();

if (isset($db) && $db instanceof DatabaseMySQLProfiler) {
    $sql_time   = $db->getTotalQueryTime();
    $php_time   = $total_time - $sql_time - $db->time_connect;
}

echo '| '.ahref_js(round($total_time, 2).'s web', "return toggle_el('".$prof_id."')");

$css =
'display:none;'.
'width:400px;'.
'padding:4px;'.
'border:#000 1px solid;';

echo '<div id="'.$prof_id.'" style="'.$css.'">';

if (isset($db) && $db instanceof DatabaseMySQLProfiler) {
    echo 'Load: <b>'.round($total_time, 2).'s</b> ';
    echo ' (DB connect: '.round($db->time_connect, 2).'s, ';
    echo 'SQL: '.round($sql_time, 2).'s, ';
    echo 'PHP: '.round($php_time, 2).'s)';
} else {
    echo 'Load: <b>'.round($total_time, 2).'s</b>';
}
echo '<br/>';


$conv = new ConvertDatasize(); // XXXX this should be static class
$used_mem = memory_get_peak_usage(false);

echo
'Used memory: <b>'.
round($conv->convLiteral($used_mem, 'MiB', 'byte'), 1).
' MiB</b>';

// "-1" means "no memory limit"
if (ini_get('memory_limit') != '-1') {
    //XXX simplify datasize conversion
    $limit = $conv->convLiteral(ini_get('memory_limit'), 'byte'); //convert from "128M", or "4G" to bytes
    echo
    ' (<b>'.round($used_mem / $limit * 100, 1).'%</b>'.
    ' of <b>'.$conv->convLiteral($limit, 'MiB').' MiB</b>)<br/>';
} else {
    echo ' (no limit)<br/>';
}
echo '<br/>';

if (function_exists('apc_cache_info')) {
    $conv = new ConvertDatasize();

    $info = apc_cache_info('', true);
//d($info);
    echo 'APC: using <b>'.round($conv->convLiteral($info['mem_size'], 'MiB'), 2).' MiB</b><br/>';
    echo 'APC: <b>'.$info['num_hits'].'</b> hits, <b>'.$info['num_misses'].'</b> misses<br/>';
    echo 'APC: <b>'.$info['num_entries'].'</b> entries (max <b>'.$info['num_slots'].'</b>)<br/>';
    echo '<br/>';
}

$client = HttpUserAgent::getBrowser();

echo
 'Client: <b>'.$_SERVER['REMOTE_ADDR'].'</b>'.
' using '.
' <span title="'.$_SERVER['HTTP_USER_AGENT'].'" style="font-weight:bold">'.$client->name.' '.$client->version.' ('.$client->os.', '.$client->arch.')</span><br/>';
echo '<br/>';

echo 'Webserver: <b>'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'</b><br/>';
echo 'Software: <b>'.$_SERVER['SERVER_SOFTWARE'].'</b> with <b>PHP '.phpversion().'</b><br/>';

echo 'Local time: <b>'.date('Y-m-d H:i:s T').'</b><br/>';
echo 'Uptime: <b>'.elapsed_seconds( uptime() ).'</b><br/>';

echo '</div>'; // closing $prof_id

echo ' | <span id="span_rendertime">0.00</span>s render';

echo ahref_js('', "return hide_el('".$wrapper_id."');", 'closebtn');

echo '</div>'; // closing inner $container_id

echo '</div>'; // closing outer wrapper

?>
