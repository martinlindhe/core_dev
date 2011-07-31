<?php
/**
 * $Id$
 *
 * Shows page load time & other information
 */

$css =
'position:fixed;'.
'right:0;'.
'bottom:0;'.
'text-align:right;'.
'margin:2px;'.
'padding:2px;'.
'padding-top:0px;'.
'border:1px dashed #aaa;'.
'color:#000;'.
'background-color:#fafafa;'.
'font:9px verdana;'.
'text-align:left;';

$container_id = 'cd_c_'.mt_rand();

echo '<div id="'.$container_id .'" style="'.$css.'">';

echo '<a class="closebtn" href="#" onclick="hide_el(\''.$container_id.'\');"></a>';

echo 'core_dev 0.2-svn ';

if (class_exists('SqlHandler')) {
    $view = new ViewModel('views/sql_profiler.php');
    echo $view->render();
}

if (class_exists('TempStore')) {
    $view = new ViewModel('views/TempStore_status.php');
    echo $view->render();
}

$prof_id = 'prof_'.mt_rand();

$header->embedCss(
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
'}'
);

echo '| <a href="#" onclick="return toggle_el(\''.$prof_id.'\');">load</a>';

$css =
'display:none;'.
'width:370px;'.
'padding:4px;'.
'border:#000 1px solid;';

echo '<div id="'.$prof_id.'" style="'.$css.'">';

$total_time = microtime(true) - $page->getStartTime();

if (isset($db) && $db instanceof DatabaseMySQLProfiler) {
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

echo 'Client <b>'.$_SERVER['REMOTE_ADDR'].'</b> using <b>'.$_SERVER['HTTP_USER_AGENT'].'</b><br/>';
echo 'Server <b>'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'</b> running <b>'.$_SERVER['SERVER_SOFTWARE'].'</b> with <b>PHP '.phpversion().'</b><br/>';

echo 'Server time: '.sql_datetime( time() ).' '.date('T').'<br/>';
echo 'Uptime: '.elapsed_seconds( uptime() ).'<br/>';

echo '</div>'; // closing $prof_id

echo '</div>';

?>
