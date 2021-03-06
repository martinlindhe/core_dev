<?php
/**
 * Shows SQL query profiling information
 */

//STATUS: wip

namespace cd;

if (!$db || !$db->isConnected())
    return;

$sql_div = 'cd_sql'.mt_rand();

$header->registerCss(
'#'.$sql_div.
'{'.
    'max-width:400px;'.
    'max-height:600px;'.
    'overflow-y:scroll;'.
    'border:#000 1px solid;'.
'}'
);

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
        throw new \Exception ('odd input: '.$q);

    $query = htmlentities(nl2br($q->query), ENT_COMPAT, 'UTF-8');

    $keywords = array(
    'SELECT ', 'UPDATE ', 'INSERT ', 'DELETE ', 'SHOW ', 'CALL ',
    ' FROM ', ' SET ', ' WHERE ', ' HAVING ',
    ' LEFT JOIN ', ' LEFT OUTER JOIN ', ' INNER JOIN ',
    ' GROUP BY ', ' ORDER BY ',
    ' ON ', ' AS ', ' AND ', ' OR ', ' LIMIT ', ' BETWEEN ',
    ' STATUS',
    ' IS NULL', ' IS NOT NULL', ' DESC', ' ASC',
    ' != ',
    'NOW()', ' DATE(',
    ' COUNT(', ' DISTINCT(', ' SUM(', ' LENGTH(', ' CHAR_LENGTH(',
    ' LOWER(', ' RAND(',
    ' MIN(', ' MAX(',
    ' SHA1(',',SHA1(', // FIXME can this be described in one rule?
    );

    $decorated = array(
    '<b>SELECT</b> ', '<b>UPDATE</b> ', '<b>INSERT</b> ', '<b>DELETE</b> ', '<b>SHOW</b> ', '<b>CALL</b> ',
    '<br/><b>FROM</b> ', '<br/><b>SET</b> ', '<br/><b>WHERE</b> ', '<br/><b>HAVING</b> ',
    '<br/><b>LEFT JOIN</b> ', '<br/><b>LEFT OUTER JOIN</b> ', '<br/><b>INNER JOIN</b> ',
    '<br/><b>GROUP BY</b> ', '<br/><b>ORDER BY</b> ',
    ' <b>ON</b> ', ' <b>AS</b> ', ' <b>AND</b> ', ' <b>OR</b> ', ' <b>LIMIT</b> ', ' <b>BETWEEN</b> ',
    ' <b>STATUS</b>',
    ' <b>IS NULL</b>', ' <b>IS NOT NULL</b>', ' <b>DESC</b>', ' <b>ASC</b>',
    ' <b>!=</b> ',
    '<b>NOW()</b>', ' <b>DATE</b>(',
    ' <b>COUNT</b>( ', ' <b>DISTINCT</b>( ',' <b>SUM</b>( ', ' <b>LENGTH</b>( ', ' <b>CHAR_LENGTH</b>( ',
    ' <b>LOWER</b>( ', ' <b>RAND</b>( ',
    ' <b>MIN</b>( ',' <b>MAX</b>( ',
    ' <b>SHA1</b>( ',', <b>SHA1</b>( ',
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
    if ($q->params) {
        echo ': ';

        $tmp = array();

        foreach ($q->params as $param) {
            $bg_col = 'white';
            if (is_int($param)) {
                $bg_col = '#A2B0FF'; // blueish
            } else if (is_float($param)) {
                $bg_col = '#FF6EDB'; // pink
            } else if (is_string($param)) {
                $bg_col = '#26D938'; // greenish
            }

            if (is_string($param)) {
                $tmp[] = '<span style="background-color:'.$bg_col.';">'.$param.'</span>';
            } else {
                $tmp[] = '<span style="background-color:'.$bg_col.';">'.serialize($param).'</span>';
            }
        }
        echo implode(', ', $tmp);
    }

    echo
    '</div>'.

    '</div>';
}



echo ahref_js(count($db->queries).' sql', "return toggle_el('".$sql_div."')");

$show_div = $db->getErrorCount() ? true : false;

$css =
($show_div ? '' : 'display:none;');

echo '<div id="'.$sql_div.'" style="'.$css.'">';

foreach ($db->queries as $q)
    print_query($q);

echo
count($db->queries).' '.(count($db->queries) == 1 ? 'query' : 'queries').
' in '.round($db->getTotalQueryTime(), 2).'s<br/>';
echo '<br/>';

echo 'Server: <b>'.$db->getHost().'</b><br/>';

/*
$db_time = Sql::pSelectItem('SELECT NOW()');
$uptime  = Sql::pSelectRow('SHOW STATUS WHERE Variable_name = ?', 's', 'Uptime');

echo 'Database server time: <b>'.$db_time.'</b><br/>';
echo 'Web server time: <b>'.sql_datetime( time() ).'</b><br/>';
echo 'Uptime: <b>'.elapsed_seconds($uptime['Value']).'</b><br/>';
*/

echo '</div>';
