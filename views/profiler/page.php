<?php
/**
 * Shows page load time & other information
 */

//TODO: $_SERVER['REQUEST_TIME'] är ett bättre "page start" värde (???)

namespace cd;

// measure time at page "start execute" event
$header->embedJs('var beforeload=new Date();');

// measure time when page finished loading
$header->embedJsOnload(
'document.getElementById("span_rendertime").innerHTML=(new Date()-beforeload)/1000;'
);

$container_id = 'cdc_'.mt_rand();
$wrapper_id   = 'cdw_'.mt_rand();

$header->embedCss(
'#'.$wrapper_id.
'{'.
    'z-index:999;'.     // to put on top of other elements
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
//    'background-position:0px -21px;'.  //TODO: add lighter version of expander icon tile for hover
'}'
);


echo '<div id="'.$wrapper_id.'">'; // outer wrapper

echo ahref_js('', "return toggle_el('".$container_id."');", 'expandbtn');
echo 'core_dev';

echo '<div id="'.$container_id.'" style="display:none;">';  // inner container

if (class_exists('cd\SqlHandler')) {
    $view = new ViewModel('views/profiler/mysql.php');
    echo $view->render();
}

if (class_exists('cd\TempStore')) {
    $view = new ViewModel('views/profiler/tempstore.php');
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


$used_mem = memory_get_peak_usage(false);

echo
'Used memory: <b>'.
round(ConvertDatasize::convert('byte', 'MiB', $used_mem), 1).
' MiB</b>';


$memory_limit = ini_get('memory_limit');
if ($memory_limit != '-1') { // "-1" means "no memory limit"

    $limit = ConvertDatasize::ToBytes($memory_limit);
    $pct = round($used_mem / $limit * 100, 1);
    $limit_s = round(ConvertDatasize::convert('byte', 'MiB', $limit), 1);
    echo
    ' (<b>'.$pct.'%</b>'.
    ' of <b>'.$limit_s.' MiB</b>)<br/>';
} else {
    echo ' (no limit)<br/>';
}
echo '<br/>';

if (extension_loaded('apc')) {

    $info = apc_cache_info('', true);
//d($info);
    echo 'APC: using <b>'.round(ConvertDatasize::convert('byte', 'MiB', $info['mem_size']), 2).' MiB</b><br/>';
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
echo 'Software: <b>'.$_SERVER['SERVER_SOFTWARE'].'</b> with <b><span title="PHP_VERSION_ID = '.PHP_VERSION_ID.'">PHP '.phpversion().'</span></b><br/>';

echo 'Webserver time: <b>'.date('Y-m-d H:i:s T').'</b><br/>';
echo 'System uptime: <b>'.elapsed_seconds( uptime() ).'</b><br/>';

echo '</div>'; // closing $prof_id

echo ' | <span id="span_rendertime">9.99</span>s render';

echo ahref_js('', "return hide_el('".$wrapper_id."');", 'closebtn');

echo '</div>'; // closing inner $container_id

echo '</div>'; // closing outer wrapper

?>
