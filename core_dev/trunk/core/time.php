<?php
/**
 * $Id$
 *
 * Random time-related functions
 *
 * @author Martin Lindhe, 2007-2011 <martin@startwars.org>
 */

//STATUS: some parts is used all over core_dev internals,
//        maybe rework into a time object extended from php's internal object?

require_once('core.php'); // for php_min_ver()
require_once('sql_misc.php');

/**
 * Converts a SQL timestamp to a text string representing how long ago this timestamp occured
 *
 * @param $sql_time SQL timestamp
 * @return text string representing how long ago this timestamp occured
 */
function ago($sql_time)
{
    //XXX deprecate!!! use Timestamp->getRelative() instead
    if (function_exists('agoOverride')) {
        return agoOverride($sql_time);
    }

    $old_time = strtotime($sql_time);
    $curr_time = time();

    if ($curr_time >= $old_time) {
        return shortTimePeriod($curr_time - $old_time).' ago';
    } else {
        return shortTimePeriod($old_time - $curr_time).' in the future';
    }
}

/**
 * Returns the age (in years)
 * Does not take into account timezone offset differences
 *
 * @param $sql_time sql timestamp representing day of birth
 * @return number of years old
 */
function age($ts)
{
    return num_years($ts, time());
}

/**
 * Calculates the number of years between two dates
 */
function num_years($d1, $d2)
{
    $d1 = ts($d1);
    $d2 = ts($d2);

    if (php_min_ver('5.3')) {
        $dt1 = new DateTime(sql_date($d1));
        $dt2 = new DateTime(sql_date($d2));
        $interval = $dt1->diff($dt2);
        return $interval->y;
    }

    return floor(($d2 - $d1) / 60 / 60 / 24 / 365.25);
}

/**
 * @return number of days (also counting the dates) the date period spans over
 */
function num_days($d1, $d2)
{
    $d1 = ts($d1);
    $d2 = ts($d2);

    if (php_min_ver('5.3')) {
        $dt1 = new DateTime(sql_date($d1));
        $dt2 = new DateTime(sql_date($d2));
        $interval = $dt1->diff($dt2);

        $days = $interval->format('%a'); //Total amount of days
        return $days + 1;
    }

    if ($d1 > $d2)
        $date_diff = $d1 - $d2;
    else
        $date_diff = $d2 - $d1;

    $days = ceil($date_diff / (3600*24)) + 1;

    return $days;
}

/**
 * Converts a timespan into human-readable text
 *
 * @param $secs number of seconds to present
 * @return returns a sting like: 4h10m3s
 */
function shortTimePeriod($secs) //XXX rename to something with duration. also see Duration class .. DROP for elapsed_seconds() ?
{
    if (is_float($secs)) $secs = ceil($secs);
    $retval = '';

    //years
    $a = date('Y', $secs) - 1970;
    if ($a==1) $retval = $a.' year, ';
    else if ($a>0) $retval = $a.' years, ';
    $secs -= (((($a*60)*60)*24)*30)*365;

    //months
    $a = date('n',$secs)-1;
    if ($a==1) $retval .= $a.' month, ';
    else if($a>0) $retval .= $a.' months, ';
    $secs -= ((($a*60)*60)*24)*30;

    //days
    $a = date('j',$secs)-1;
    if ($a==1) $retval .= $a.' day, ';
    else if ($a>0) $retval .= $a.' days, ';
    $secs -= (($a*60)*60)*24;

    //hours
    $a = date('H',$secs)-1;
    if ($a>0) $retval .= $a.'h';
    $secs -= ($a*60)*60;

    //minutes
    $a = date('i',$secs)-0;
    if ($a>0) $retval .= $a.'m';
    $secs -= $a*60;

    //seconds
    $a = date('s',$secs)-0;
    if ($a>0) $retval .= $a.'s';

    if (substr($retval, -2) == ', ') $retval = substr($retval, 0, -2);
    if ($retval == '') $retval = '0s';

    return $retval;
}

/**
 * Formats number of elapsed seconds in a readable way, such as "2.5 hours" or "4 weeks"
 */
function elapsed_seconds($s)
{
    if (!is_numeric($s))
        throw new Exception ('not a number '.$s);

    if ($s < 60)
        return $s.' seconds';

    if ($s < (60 * 60))
        return round($s / 60, 1).' minutes';

    if ($s < (60 * 60 * 24))
        return round($s / 60 / 60, 1).' hours';

    if ($s < (60 * 60 * 24 * 7))
        return round($s / 60 / 60 / 24, 1).' days';

    if ($s < (60 * 60 * 24 * 28))
        return round($s / 60 / 60 / 24 / 7, 1).' weeks';

    if ($s < (60 * 60 * 24 * 30 * 12))
        return round($s / 60 / 60 / 24 / 30, 1).' months';

    return round($s / 60 / 60 / 24 / 365, 1).' years';
}

/** Translates a timestamp such as "18:40:22" into number of seconds (integer) */
function in_seconds($s)
{
    //XXX regexp validate format "nn:nn:nn"

    $x = explode(':', $s);
    if (count($x) != 3)
        throw new Exception ('bad format: '.$s);

    return ($x[0] * 3600) + ($x[1] * 60) + $x[2];
}

/** Returns current time of day as a formatted 24-hour timestamp "HH:MM:SS" */
function time_of_day()
{
    return date('H:i:s');
}

/**
 * Executes $c and returns the time it took
 *
 * @param $c command to execute
 * @param $retval return value of command executed
 */
function exectime($c, &$retval = 0)
{
    //XXX: Use 2>&1 in $c to redirect stderr to $output buffer
    $output = array();
    $exec_start = microtime(true);
    exec($c, $output, $retval);

    return microtime(true) - $exec_start;
}

/**
 * Returns MySQL datetime in UNIX timestamp format
 *
 * @param $datetime is a MySQL datetime
 * @return given MySQL datetime in UNIX timestamp format
 */
function datetime_to_timestamp($datetime) //XXX deprecate! use ts() instead
{
    return strtotime($datetime);
}

/**
 * Converts input (string or numeric) into a unix timestamp
 */
function ts($d)
{
    if (!$d) return 0;
    if (is_numeric($d)) return $d;
    return strtotime($d);
}

/**
 * Compares two MySQL datetime timestamps
 *
 * @param $d1 is a MySQL datetime
 * @param $d2 is a MySQL datetime
 * @return true if $d1 is older date than $d2
 */
function datetime_less($d1, $d2)
{
    if (strtotime($d1) < strtotime($d2)) return true;
    return false;
}

/**
 * Default time format display
 *
 * @param $ts unix timestamp or SQL DATETIME format
 * @param $relative show time relative to current time
 */
function formatTime($ts = 0, $relative = false)
{
    if (!$ts) $ts = time();

    if (function_exists('formatTimeOverride'))
        return formatTimeOverride($ts);

    if (!is_numeric($ts)) $ts = strtotime($ts);

    $datestamp = mktime (0,0,0,date('m',$ts), date('d',$ts), date('Y',$ts));
    $yesterday = mktime (0,0,0,date('m') ,date('d')-1,  date('Y'));
    $tomorrow  = mktime (0,0,0,date('m') ,date('d')+1,  date('Y'));

    $timediff = time() - $ts;

    if ($relative) {
        if (date('Y-m-d', $ts) == date('Y-m-d')) {
            //Today 18:13
            return date('H:i',$ts);
        } else if ($datestamp == $yesterday) {
            //Yesterday 18:13
            return t('Yesterday').' '.date('H:i',$ts);
        } else if ($datestamp == $tomorrow) {
            //Tomorrow 18:13
            return t('Tomorrow').' '.date('H:i',$ts);
        }
    }

    //2007-04-14 15:22
    return date('Y-m-d H:i', $ts);
}

/**
 * @return true if input string is a US date: "m/d/yyyy"
 */
function is_us_date($s)
{
    if (!is_string($s))
        return false;

    $p = explode('/', $s);
    if (count($p) != 3)
        return false;

    if (!checkdate($p[0], $p[1], $p[2]))
        return false;

    return true;
}

?>
