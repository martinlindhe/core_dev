<?php
/**
 * $Id$
 *
 * Misc sql-related functions
 *
 * @author Martin Lindhe, 2009-2011 <martin@startwars.org>
 */

/**
 * @param $ts UNIX timestamp
 * @return given UNIX timestamp in MySQL datetime format (YYYY-MM-DD HH:MM:SS)
 */
function sql_datetime($ts)
{
    if (!$ts)
        return '';

    if (!is_numeric($ts))
        $ts = strtotime($ts);

    return date('Y-m-d H:i:s', $ts);
}

/**
 * @param $ts UNIX timestamp
 * @return given UNIX timestamp in MySQL date format (YYYY-MM-DD)
 */
function sql_date($ts)
{
    if (!$ts)
        return '';

    if (!is_numeric($ts))
        $ts = strtotime($ts);

    return date('Y-m-d', $ts);
}

/**
 * @param $ts UNIX timestamp
 * @return given UNIX timestamp in MySQL time format (HH:MM:SS)
 */
function sql_time($ts)
{
    if (!$ts)
        return '';

    if (!is_numeric($ts))
        $ts = strtotime($ts);

    return date('H:i:s', $ts);
}

/**
 * Combines a array into a OR statement
 *
 * @return e.g. key=val1 OR key=val2 OR key=val3
 */
function sql_or_array($key, $vals, $pad = '', $numeric = true)
{
    $db = SqlHandler::getInstance();

    $tmp = array();
    foreach ($vals as $val) {
        if ($numeric && !is_numeric($val))
            throw new Exception ('sql_or_array INVALID value');

        $val .= $pad;
        $tmp[] = $key.($numeric ? $val : '"'.$db->escape($val).'"');
    }

    return implode(' OR ', $tmp);
}

/**
 * Returns the current time in the same format as the MySQL "NOW()" command
 * @return time in MySQL datetime format
 */
function now()
{
    return strftime('%Y-%m-%d %H:%M:%S');
}

?>
