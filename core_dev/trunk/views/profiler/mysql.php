<?php
/**
 * Shows SQL query profiling information
 */

//STATUS: wip

$header->registerCss(
'.cd_sql_row'.
'{'.
    'overflow:auto;'.
    'max-width:400px;'.
'}'
);

$header->registerCss(
'.cd_sql_row:hover'.
'{'.
    'background-color:#ccc;'.
'}'
);

$header->registerCss(
'.cd_sql_box,.cd_sql_box_p'.
'{'.
    'width:30px;'.
    'float:left;'.
'}'
);

$header->registerCss(
'.cd_sql_box_p'.
'{'.
    'background-color:#B1F9AA;'.
'}'
);

$header->registerCss(
'.cd_sql_text'.
'{'.
    'width:300px;'.
    'float:left;'.
'}'
);

function print_query($q)
{
    if (! ($q instanceof SqlQuery))
        throw new Exception ('odd input: '.$q);

    $query = htmlentities(nl2br($q->query), ENT_COMPAT, 'UTF-8');

    $keywords = array(
    'SELECT ', 'UPDATE ', 'INSERT ', 'DELETE ', 'SHOW ',
    ' FROM ', ' SET ', ' WHERE ',
    ' LEFT JOIN ', ' LEFT OUTER JOIN ', ' INNER JOIN ',
    ' GROUP BY ', ' ORDER BY ',
    ' ON ', ' AS ', ' AND ', ' OR ', ' LIMIT ', ' BETWEEN ',
    ' STATUS',
    ' IS NULL', ' IS NOT NULL', ' DESC', ' ASC',
    ' != ',
    'NOW()', ' DATE(',
    ' COUNT(', ' DISTINCT(', ' SUM(', ' LENGTH(', ' CHAR_LENGTH(',
    ' MIN(', ' MAX(',
    );

    $decorated = array(
    '<b>SELECT</b> ', '<b>UPDATE</b> ', '<b>INSERT</b> ', '<b>DELETE</b> ', '<b>SHOW</b> ',
    ' <b>FROM</b> ', '<br/><b>SET</b> ', '<br/><b>WHERE</b> ',
    '<br/><b>LEFT JOIN</b> ', '<br/><b>LEFT OUTER JOIN</b> ', '<br/><b>INNER JOIN</b> ',
    '<br/><b>GROUP BY</b> ', '<br/><b>ORDER BY</b> ',
    ' <b>ON</b> ', ' <b>AS</b> ', ' <b>AND</b> ', ' <b>OR</b> ', ' <b>LIMIT</b> ', ' <b>BETWEEN</b> ',
    ' <b>STATUS</b>',
    ' <b>IS NULL</b>', ' <b>IS NOT NULL</b>', ' <b>DESC</b>', ' <b>ASC</b>',
    ' <b>!=</b> ',
    '<b>NOW()</b>', ' <b>DATE</b>(',
    ' <b>COUNT</b>( ', ' <b>DISTINCT</b>( ',' <b>SUM</b>( ', ' <b>LENGTH</b>( ', ' <b>CHAR_LENGTH</b>( ',
    ' <b>MIN</b>( ',' <b>MAX</b>( ',
    );

    $query = str_replace($keywords, $decorated, $query);

    echo
    '<div class="cd_sql_row">'.
    '<div class="'.($q->prepared ? 'cd_sql_box_p' : 'cd_sql_box').'">';

    if ($q->error)
        echo coreButton('Error', '', 'SQL Error');
    else
        echo round($q->time, 2).'s';

    echo '</div><div class="cd_sql_text">';

    if ($q->error)
        echo 'Error: <b>'.$q->error.'</b><br/><br/>';

    echo $query;

    if ($q->format)
        echo ' ('.$q->format.')';
    if ($q->params)
        echo ': '.implode(', ', $q->params);

    echo
    '</div>'.

    '</div>';
}

$sql_div = 'cd_sql'.mt_rand();

$db_time = $db->pSelectItem('SELECT NOW()');
$uptime  = $db->pSelectRow('SHOW STATUS WHERE Variable_name = ?', 's', 'Uptime');

echo ahref_js(count($db->queries).' sql', "return toggle_el('".$sql_div."')");

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

echo 'Database server time: <b>'.$db_time.'</b><br/>';
echo 'Web server time: <b>'.sql_datetime( time() ).'</b><br/>';
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
