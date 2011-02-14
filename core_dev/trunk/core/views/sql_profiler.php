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

$header->embedCss(
'.hover:hover{ background-color: #ccc; }'
);

echo '<a href="#" onclick="return toggle_sql_profiler();">'.count($db->queries).' sql</a>';

$sql_time = 0;
$error = false;
$res = '';

foreach ($db->queries as $prof)
{
    $query = htmlentities(nl2br($prof->query), ENT_COMPAT, 'UTF-8');

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
    ' <b>FROM</b> ', '<br/><b>SET</b> ', '<br/><b>WHERE</b> ', '<br/><b>LEFT JOIN</b> ', '<br/><b>INNER JOIN</b> ', '<br/><b>GROUP BY</b> ', '<br/><b>ORDER BY</b> ',
    ' <b>ON</b> ', ' <b>AS</b> ', ' <b>AND</b> ', ' <b>OR</b> ', ' <b>LIMIT</b> ', ' <b>BETWEEN</b> ',
    ' <b>IS NULL</b>', ' <b>IS NOT NULL</b>', ' <b>DESC</b>', ' <b>ASC</b>',
    ' <b>!=</b> ',
    '<b>NOW()</b>', ' <b>DATE</b>(',
    ' <b>COUNT</b>(', ' <b>SUM</b>(',
    );

    $query = str_replace($keywords, $decorated, $query);

    if ($prof->prepared)
        $res .= '<table summary="" class="hover" style="background-color: #B2A23D" width="100%">';
    else
        $res .= '<table summary="" class="hover">';

    $res .= '<tr><td width="30">';

    if ($prof->error)
        $res .= coreButton('Error', '', 'SQL Error');
    else
        $res .= round($prof->time, 2).'s';

    $res .=  '</td><td>';

    if ($prof->error) {
        $error = true;
        $res .=  'Error: <b>'.$prof->error.'</b><br/><br/>';
    }

    $res .= $query;

    if ($prof->format) $res .= ' ('.$prof->format.')';
    if ($prof->params) $res .= ': '.implode(', ', $prof->params);

    $res .= '</td></tr></table>';
//    $res .= '<hr/>';
}

$css =
($error ? '' : ' display:none;').
'overflow:auto;'.
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
