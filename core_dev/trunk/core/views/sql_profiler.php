<?php
/**
 * $Id$
 *
 * Shows SQL query profiling information
 */

//STATUS: wip

$rand_id = mt_rand();

$header->embedJs(
//Toggles element with name "n" between visible and hidden
'function toggle_sql_profiler()'.
'{'.
    'var e = document.getElementById("sql_prof_'.$rand_id.'");'.
    'e.style.display = (e.style.display ? "" : "none");'.
'}'
);

// XXX this should be done in SqlQuery->render() or so, but
$header->embedCss(
'.hover:hover{ background-color: #ccc; }'
);

echo '<a href="#" onclick="return toggle_sql_profiler();">'.count($db->queries).' sql</a>';

$sql_time = 0;
$error = false;
$res = '';

foreach ($db->queries as $prof)
    $res .= $prof->render();

$css =
($error ? '' : ' display:none;').
'overflow:auto;'.
'max-width:400px;'.
'border:#000 1px solid;';

echo '<div id="sql_prof_'.$rand_id.'" style="'.$css.'">';

echo $res;

if (is_client_localhost())
{
    echo 'Database <b>'.$db->host.':'.$db->port.'</b>';
    if ($db->db_handle)
        echo ' with <b>MySQL '.$db->db_handle->server_info.'</b>';
    else
        echo ' <b>(CONNECTION NOT INITIALIZED)</b>';
    echo '<br/>';
}

echo
count($db->queries).' '.(count($db->queries) == 1 ? 'query' : 'queries').
' in '.round($db->getTotalQueryTime(), 2).'s<br/>';

echo '</div>';

?>
