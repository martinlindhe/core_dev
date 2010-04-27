<?php
/**
 * $Id$
 *
 * Misc sql-related functions
 *
 * @author Martin Lindhe, 2009-2010 <martin@startwars.org>
 */

/**
 * Returns given UNIX timestamp in MySQL datetime format
 *
 * @param $ts is a UNIX timestamp
 * @return given UNIX timestamp in MySQL datetime format
 */
function sql_datetime($ts)
{
    if (!is_numeric($ts))
        $ts = strtotime($ts);

    return date('Y-m-d H:i:s', $ts);
}

/**
 * Returns given UNIX timestamp in MySQL date format
 *
 * @param $ts is a UNIX timestamp
 * @return given UNIX timestamp in MySQL date format
 */
function sql_date($ts)
{
    if (!is_numeric($ts))
        $ts = strtotime($ts);

    return date('Y-m-d', $ts);
}

/**
 * Combines a array into a OR statement
 *
 * @return e.g. key=val1 OR key=val2 OR key=val3
 */
function sql_or_array($key, $vals)
{
    $db = SqlHandler::getInstance();

    $tmp = array();
    foreach ($vals as $val) {
        $tmp[] = $key.(is_numeric($val) ? $val : '"'.$db->escape($val).'"');
    }

    return implode(' OR ', $tmp);
}

?>
