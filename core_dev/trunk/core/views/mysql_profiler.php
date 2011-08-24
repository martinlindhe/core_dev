<?php
/**
 * $Id$
 *
 * Shows SQL query profiling information
 */

//STATUS: wip

$sql_div = 'sql_'.mt_rand();

$header->embedCss(
'.hover:hover{'.
    'background-color:#ccc;'.
'}'
);

$db_time = $db->getOneItem('SELECT NOW()');
$uptime  = $db->getOneRow('SHOW STATUS WHERE Variable_name="Uptime"');

$res = '';
foreach ($db->queries as $prof)
    $res .= $prof->render();

echo '<a href="#" onclick="return toggle_el(\''.$sql_div.'\');">'.count($db->queries).' sql</a>';

$show_div = $db->getErrorCount() ? true : false;

$css =
($show_div ? '' : 'display:none;').
'overflow:auto;'.
'max-width:400px;'.
'border:#000 1px solid;';

echo '<div id="'.$sql_div.'" style="'.$css.'">';

echo $res;

echo
count($db->queries).' '.(count($db->queries) == 1 ? 'query' : 'queries').
' in '.round($db->getTotalQueryTime(), 2).'s<br/>';
echo '<br/>';

echo 'MySQL server: <b>'.$db->db_handle->server_info.'</b><br/>';
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
