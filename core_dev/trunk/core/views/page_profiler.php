<?php
/**
 * $Id$
 *
 * Shows page load time & other information
 */

$css =
'position:absolute;'.
'right:0;'.
'bottom:0;'.
'text-align:right;'.
'padding:2px;'.
'padding-top:0px;'.
'border:1px dashed #aaa;'.
'color:#000;'.
'background-color:#fafafa;'.
'font:9px verdana;'.
'text-align:left;';

echo '<div id="x2x2xx" style="'.$css.'">';
echo 'core_dev 0.2-svn ';

if (class_exists('SqlHandler')) {
    $db = SqlHandler::getInstance();

    if ($db instanceof DatabaseMySQLProfiler)
        echo $db->renderProfiler();
}

if (class_exists('TempStore')) {
    $store = TempStore::getInstance();
    echo $store->renderStatus();
}

$pager_id = 'page_prof_'.mt_rand();

echo js_embed(
//Toggles element with name "n" between visible and hidden
'function toggle_page_profiler()'.
'{'.
    'var e = document.getElementById("'.$pager_id.'");'.
    'e.style.display = (e.style.display ? "" : "none");'.
'}'
);

echo '| <a href="#" onclick="return toggle_page_profiler();">load</a>';

$css =
'display:none;'.
'overflow:auto;'.
'padding:4px;'.
'border:#000 1px solid;';

echo '<div id="'.$pager_id.'" style="'.$css.'">';

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
echo '<br/>';
echo dm(); //memory usage
echo '<br/>';

echo 'Server <b>'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'</b> running <b>'.$_SERVER['SERVER_SOFTWARE'].'</b> with <b>PHP '.phpversion().'</b><br/>';
echo 'Client <b>'.$_SERVER['REMOTE_ADDR'].'</b> using <b>'.$_SERVER['HTTP_USER_AGENT'].'</b><br/>';

echo 'Server time: '.date('r T').'<br/>';
echo 'Server uptime: '.elapsed_seconds( uptime() ).'<br/>';

echo '</div>'; // closing $pager_id



echo '</div>';

?>
