<?php
/**
 * $Id$
 *
 * Shows SQL query profiling information
 */

//STATUS: wip

$rand_id = mt_rand(0,99999);

echo js_embed(
//Toggles element with name "n" between visible and hidden
'function toggle_sql_profiler()'.
'{'.
    'var e = document.getElementById("sql_prof_'.$rand_id.'");'.
    'e.style.display = (e.style.display ? "" : "none");'.
'}'
);

echo '<br/><br/>';
echo '<a href="#" onclick="return toggle_sql_profiler();">'.$db->queries_cnt.' sql</a>';

$sql_height = ($db->queries_cnt*60)+70;
if ($sql_height > 200) $sql_height = 200;

$css_display = count($db->query_error) ? '' : ' display:none;';

echo '<div id="sql_prof_'.$rand_id.'" style="height:'.$sql_height.'px;'.$css_display.' overflow: auto; padding: 4px; color: #000; background-color:#E0E0E0; border: #000 1px solid; font: 9px verdana; text-align: left;">';

$sql_time = 0;
for ($i=0; $i < $db->queries_cnt; $i++)
{
    $sql_time += $db->time_spent[$i];

    $query = htmlentities(nl2br($db->queries[$i]), ENT_COMPAT, 'UTF-8');

    $keywords = array(
    'SELECT ', 'UPDATE ', 'INSERT ', 'DELETE ',
    ' FROM ', ' SET ', ' WHERE ', ' LEFT JOIN ', ' INNER JOIN ', ' GROUP BY ', ' ORDER BY ',
    ' ON ', ' AS ', ' AND ', ' OR ', ' LIMIT ', ' BETWEEN ',
    ' IS NULL', ' IS NOT NULL', ' DESC', ' ASC',
    ' != ',
    'NOW()', ' DATE(',
    ' COUNT(', ' SUM(',
    );

    $decorated = array(
    '<b>SELECT</b> ', '<b>UPDATE</b> ', '<b>INSERT</b> ', '<b>DELETE</b> ',
    '<br/><b>FROM</b> ', '<br/><b>SET</b> ', '<br/><b>WHERE</b> ', '<br/><b>LEFT JOIN</b> ', '<br/><b>INNER JOIN</b> ', '<br/><b>GROUP BY</b> ', '<br/><b>ORDER BY</b> ',
    ' <b>ON</b> ', ' <b>AS</b> ', ' <b>AND</b> ', ' <b>OR</b> ', ' <b>LIMIT</b> ', ' <b>BETWEEN</b> ',
    ' <b>IS NULL</b>', ' <b>IS NOT NULL</b>', ' <b>DESC</b>', ' <b>ASC</b>',
    ' <b>!=</b> ',
    '<b>NOW()</b>', ' <b>DATE</b>(',
    ' <b>COUNT</b>(', ' <b>SUM</b>(',
    );

    $query = str_replace($keywords, $decorated, $query);

    echo '<table summary=""><tr><td width="40">';
    if (!empty($db->query_error[$i])) {
        echo coreButton('Error', '', 'SQL Error');
    } else {
        echo round($db->time_spent[$i], 2).'s';
    }
    echo '</td><td>';

    if (!empty($db->query_error[$i]))
        echo 'Error: <b>'.$db->query_error[$i].'</b><br/><br/>';

    echo $query;
    echo '</td></tr></table>';
    echo '<hr/>';
}

$total_time = microtime(true) - $db->ts_initial;
$php_time   = $total_time - $sql_time - $db->time_connect;

echo
'Time spent: <b>'.round($total_time, 2).'s</b> '.
' (DB connect: '.round($db->time_connect, 2).'s, '.
count($db->queries).' SQL queries: '.round($sql_time, 2).'s, '.
'PHP: '.round($php_time, 2).'s)<br/>';

if (is_client_localhost())
{
    echo 'Database <b>'.$db->host.':'.$db->port.'</b> with <b>MySQL '.$db->db_handle->server_info.'</b><br/>';
    echo 'Webserver <b>'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].'</b> with <b>PHP '.phpversion().'</b> from <b>'.$_SERVER['SERVER_SOFTWARE'].'</b> with '.$_SERVER['GATEWAY_INTERFACE'].'<br/>';
    echo 'Client <b>'.$_SERVER['REMOTE_ADDR'].'</b> with <b>'.$_SERVER['HTTP_USER_AGENT'].'</b><br/>';
}

echo dm(); //memory usage
echo 'Server time: '.date('r T');

echo '</div>';

?>
