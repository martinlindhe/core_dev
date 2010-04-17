<?php
/**
 * $Id$
 *
 * Shows SQL query profiling information
 */

//STATUS: wip

$rand_id = mt_rand(1,5000000);

echo '<br/>';
echo '<a href="#" onclick="return toggle_element(\'sql_profiling'.$rand_id.'\');">'.$this->db->queries_cnt.' sql</a>';

$sql_height = ($this->db->queries_cnt*60)+70;
if ($sql_height > 400) $sql_height = 400;

$css_display = count($this->db->query_error) ? '' : ' display:none;';

echo '<div id="sql_profiling'.$rand_id.'" style="height:'.$sql_height.'px;'.$css_display.' overflow: auto; padding: 4px; color: #000; background-color:#E0E0E0; border: #000 1px solid; font: 9px verdana; text-align: left;">';

$sql_time = 0;
for ($i=0; $i < $this->db->queries_cnt; $i++)
{
    $sql_time += $this->db->time_spent[$i];

    $query = htmlentities(nl2br($this->db->queries[$i]), ENT_COMPAT, 'UTF-8');

    $keywords = array(
    'SELECT ', 'UPDATE ', 'INSERT ', 'DELETE ',
    ' FROM ', ' SET ', ' WHERE ', ' LEFT JOIN ', ' GROUP BY ', ' ORDER BY ',
    ' ON ', ' AS ', ' AND ', ' OR ', ' LIMIT ', ' BETWEEN ',
    ' IS NULL', ' NOT NULL ', ' DESC', ' ASC',
    ' != ',
    'NOW()', 'COUNT',
    );

    $decorated = array(
    '<b>SELECT</b> ', '<b>UPDATE</b> ', '<b>INSERT</b> ', '<b>DELETE</b> ',
    '<br/><b>FROM</b> ', '<br/><b>SET</b> ', '<br/><b>WHERE</b> ', '<br/><b>LEFT JOIN</b> ', '<br/><b>GROUP BY</b> ', '<br/><b>ORDER BY</b> ',
    ' <b>ON</b> ', ' <b>AS</b> ', ' <b>AND</b> ', ' <b>OR</b> ', ' <b>LIMIT</b> ', ' <b>BETWEEN</b> ',
    ' <b>IS NULL</b>', ' <b>NOT NULL</b>', ' <b>DESC</b>', ' <b>ASC</b>',
    ' <b>!=</b> ',
    '<b>NOW()</b>', '<b>COUNT</b>',
    );

    $query = str_replace($keywords, $decorated, $query);

    echo '<table summary=""><tr><td width="40">';
    if (!empty($this->db->query_error[$i])) {
        echo coreButton('Error', '', 'SQL Error');
    } else {
        echo round($this->db->time_spent[$i], 2).'s';
    }
    echo '</td><td>';

    if (!empty($this->db->query_error[$i]))
        echo 'Error: <b>'.$this->db->query_error[$i].'</b><br/><br/>';

    echo $query;
    echo '</td></tr></table>';
    echo '<hr/>';
}

$total_time = microtime(true) - $this->db->time_initial + $sql_time + $this->db->time_connect;
$php_time = $total_time - $sql_time - $this->db->time_connect;

echo
'Time spent: <b>'.round($total_time, 2).'s</b> '.
' (DB connect: '.round($this->db->time_connect, 2).'s, '.
count($this->db->queries).' SQL queries: '.round($sql_time, 2).'s, '.
'PHP: '.round($php_time, 2).'s)<br/>';

if (is_client_localhost()) {

    echo 'Client <b>'.$_SERVER['REMOTE_ADDR'].'</b> using <b>'.$_SERVER['HTTP_USER_AGENT'].'</b><br/>';
    echo '<b>'.$this->db->host.':'.$this->db->port.'</b> running <b>MySQL '.$this->db->db_handle->server_info.'</b><br/>';
    echo '<b>'.$_SERVER['SERVER_NAME'].'</b> running <b>PHP '.phpversion().'</b> from <b>'.$_SERVER['SERVER_SOFTWARE'].'</b> with '.$_SERVER['GATEWAY_INTERFACE'].'<br/>';
}

//show memory usage
echo dm();
echo 'Server time: '.date('r T');

echo '</div>';

/**
 * Returns true if client ip is localhost
 */
function is_client_localhost() //XXX: move to lib
{
    //XXX resolve ip's to common format
    if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' ||  $_SERVER['REMOTE_ADDR'] == '::ffff:127.0.0.1')
        return true;

    return false;
}

?>
