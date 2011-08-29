<?php
/**
 * $Id$
 *
 * Shows SQL query profiling information
 */

//STATUS: wip

function print_query($q)
{
    if (! ($q instanceof SqlQuery))
        throw new Exception ('odd input: '.$q);

    $query = htmlentities(nl2br($q->query), ENT_COMPAT, 'UTF-8');

    $keywords = array(
    'SELECT ', 'UPDATE ', 'INSERT ', 'DELETE ',
    ' FROM ', ' SET ', ' WHERE ',
    ' LEFT JOIN ', ' LEFT OUTER JOIN ', ' INNER JOIN ',
    ' GROUP BY ', ' ORDER BY ',
    ' ON ', ' AS ', ' AND ', ' OR ', ' LIMIT ', ' BETWEEN ',
    ' IS NULL', ' IS NOT NULL', ' DESC', ' ASC',
    ' != ',
    'NOW()', ' DATE(',
    ' COUNT(', ' SUM(',
    );

    $decorated = array(
    '<b>SELECT</b> ', '<b>UPDATE</b> ', '<b>INSERT</b> ', '<b>DELETE</b> ',
    ' <b>FROM</b> ', '<br/><b>SET</b> ', '<br/><b>WHERE</b> ',
    '<br/><b>LEFT JOIN</b> ', '<br/><b>LEFT OUTER JOIN</b> ', '<br/><b>INNER JOIN</b> ',
    '<br/><b>GROUP BY</b> ', '<br/><b>ORDER BY</b> ',
    ' <b>ON</b> ', ' <b>AS</b> ', ' <b>AND</b> ', ' <b>OR</b> ', ' <b>LIMIT</b> ', ' <b>BETWEEN</b> ',
    ' <b>IS NULL</b>', ' <b>IS NOT NULL</b>', ' <b>DESC</b>', ' <b>ASC</b>',
    ' <b>!=</b> ',
    '<b>NOW()</b>', ' <b>DATE</b>(',
    ' <b>COUNT</b>(', ' <b>SUM</b>(',
    );

    $query = str_replace($keywords, $decorated, $query);

    $css =
    'overflow:auto;'.
    'max-width:400px;'.
    'max-height:100px;';

    Echo
    '<div style="'.$css.'">'.
    '<table summary="" class="hover" width="100%" cellpadding="0">'.
    '<tr><td width="30"'.($q->prepared ? ' style="background-color:#B1F9AA"': '').'>';

    if ($q->error)
        echo coreButton('Error', '', 'SQL Error');
    else
        echo round($q->time, 2).'s';

    echo '</td><td>';

    if ($q->error)
        echo 'Error: <b>'.$q->error.'</b><br/><br/>';

    echo $query;

    if ($q->format)
        echo ' ('.$q->format.')';
    if ($q->params)
        echo ': '.implode(', ', $q->params);

    echo
    '</td></tr></table>'.
    '</div>';
}

$sql_div = 'sql_'.mt_rand();

$header->embedCss(
'.hover:hover{'.
    'background-color:#ccc;'.
'}'
);

$db_time = $db->getOneItem('SELECT NOW()');
$uptime  = $db->getOneRow('SHOW STATUS WHERE Variable_name="Uptime"');

echo '<a href="#" onclick="return toggle_el(\''.$sql_div.'\');">'.count($db->queries).' sql</a>';

$show_div = $db->getErrorCount() ? true : false;

$css =
($show_div ? '' : 'display:none;').
'overflow:auto;'.
'max-width:400px;'.
'border:#000 1px solid;';

echo '<div id="'.$sql_div.'" style="'.$css.'">';

foreach ($db->queries as $q)
    print_query($q);

echo
count($db->queries).' '.(count($db->queries) == 1 ? 'query' : 'queries').
' in '.round($db->getTotalQueryTime(), 2).'s<br/>';
echo '<br/>';

echo 'Server: <b>'.$db->getHost().'</b><br/>';
echo 'Software: <b>MySQL '.$db->db_handle->server_info.'</b><br/>';
echo 'Local time: <b>'.$db_time.'</b><br/>';
echo 'Uptime: <b>'.elapsed_seconds($uptime['Value']).'</b><br/>';


if (is_client_localhost())
{
    echo 'Database <b>'.$db->host.':'.$db->port.'</b>';
    if ($db->db_handle)
        echo ' with <b>MySQL '.$db->db_handle->server_info.'</b>';
    else
        echo ' <b>(CONNECTION NOT INITIALIZED)</b>';
    echo '<br/>';
}

echo '</div>';

?>
