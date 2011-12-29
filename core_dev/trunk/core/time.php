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
    $old_time  = strtotime($sql_time);
    $curr_time = time();

    if ($curr_time == $old_time)
        return 'just now';

    if ($curr_time >= $old_time)
        return elapsed_seconds($curr_time - $old_time, 0).' ago';

    return elapsed_seconds($old_time - $curr_time, 0).' in the future';

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
    if (is_float($secs))
        $secs = ceil($secs);

    if (is_duration($secs))
        $secs = parse_duration($secs);

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
function elapsed_seconds($s, $precision = 1)
{
    if (!is_numeric($s))
        throw new Exception ('not a number '.$s);

    if ($s < 60)
        return $s.' seconds';

//TODO: show singular units if its "1"
//XXXX FIXME is the rounding code used incorrectly here ... ???
    if ($s < (60 * 60))
        return round($s / 60, $precision).' minutes';

    if ($s < (60 * 60 * 24))
        return round($s / 60 / 60, $precision).' hours';

    if ($s < (60 * 60 * 24 * 14)) // rather show "13 days" than "1.5 weeks"
        return round($s / 60 / 60 / 24, $precision).' days';

    if ($s < (60 * 60 * 24 * 28))
        return round($s / 60 / 60 / 24 / 7, $precision).' weeks';

    if ($s < (60 * 60 * 24 * 30 * 12))
        return round($s / 60 / 60 / 24 / 30, $precision).' months';

    return round($s / 60 / 60 / 24 / 365, $precision).' years';
}

/**
 * Translates a time string such as "18:40:22", "18:40:22.11" or "18:44:22,09" into number of seconds
 */
function in_seconds($s)
{
    if (!is_hms($s))
        throw new Exception ('not a time string: '.$s);

    $x = explode(':', $s);
    if (count($x) != 3)
        throw new Exception ('bad format: '.$s);

    $x[2] = str_replace(',', '.', $x[2]);

    return ($x[0] * 3600) + ($x[1] * 60) + $x[2];
}

/**
 * Renders a second representation as "18:40:22"
 * @param $milli force millisecond rendering?
 * @param $precision decimal precision of milliseconds
 * @param $separator decimal separator for millisecond value
 * @param $pad_hours set to true to always pad hours to 2 digits
 */
function seconds_to_hms($secs, $show_milli = false, $precision = 2, $separator = '.', $pad_hours = false)
{
    if (!is_numeric($secs))
        throw new Exception ('bad input');

    if (!$secs)
        return '00:00:00';

    $frac = $secs - (int) $secs;

    $secs = intval($secs);

    $m = (int) ($secs / 60);
    $s = $secs % 60;
    $h = (int) ($m / 60);
    $m = $m % 60;

    if ($frac || $show_milli)
        $s = round_decimals($s + $frac, $precision);

    if ($pad_hours && $h < 10) $h = '0'.$h;
    if ($m < 10) $m = '0'.$m;
    if ($s < 10) $s = '0'.$s;

    if ($separator != '.')
        $s = str_replace('.', $separator, $s);

    return $h.':'.$m.':'.$s;
}

/**
 * @return true if input string a time string, such as HH:MM or HH:MM:SS or HH:MM:SS.mmm  (or HH:MM:SS,mmm)
 */
function is_hms($s)
{
    $regexp =
    '/^([0-9]+)'.
        ':[0-9]+'.
        '(:[0-9]+'.
            '([\.\,]\d{1,3})'.
        '?)'.
    '?$/';
    preg_match_all($regexp, $s, $matches);

    if ($matches && $matches[0] && $matches[0][0] == $s)
        return true;

    return false;
}

/**
 * @return true if input string is a YYYY-MM-DD date string
 */
function is_ymd($s)
{
    $regexp =
    '/^(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/';
    preg_match_all($regexp, $s, $matches);
    if ($matches && $matches[0] && $matches[0][0] == $s)
        return true;

    return false;
}

/**
 * @return true if input is a valid year range between 1900 and 2099, like 1977-81 or 1974-2011
 */
function is_year_period($s)
{
    // match YYYY-YY, YYYY-YYYY
    $regexp = '/^((19|20)\d\d)[-](\d+)$/';
    preg_match_all($regexp, $s, $matches);

    if ($matches && $matches[0] && $matches[0][0] == $s) {
        $year1 = $matches[1][0];
        if (strlen($matches[3][0]) == 2)
            $year2 = substr($year1, 0, 2).$matches[3][0];
        else
            $year2 = $matches[3][0];
    } else
        return false;

//    echo 'year1: '.$year1.', year2: '.$year2."\n";

    if (!intval($year1) || !intval($year2))
        throw new Exception ('years not numbers, hmm?!?'); // should not be possible with above regexp

    if ($year2 <= $year1)
        throw new Exception ('period end is less than start: '.$s.', y1: '.$year1.', y2: '.$year2);

    return true;
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
    if (!$d)
        return 0;

    if (is_numeric($d) && strlen($d) == 8 && substr($d, 0, 2) == '20')
    {
        // convert "20110502" to ts
        $yy = substr($d, 0, 4);
        $mm = substr($d, 4, 2);
        $dd = substr($d, 6, 2);

        if (!checkdate($mm, $dd, $yy))
            throw new Exception ('invalid ts form: '.$d);

        return mktime(0, 0, 0, $mm, $dd, $yy);
    }

    if (is_numeric($d))
        return $d;

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

/**
 * @param $s string represenation of a duration, eg "4h"
 * @return duration, in seconds
 */
function parse_duration($s)
{
    if (is_numeric($s))
        return $s;

    $delim = substr($s, -1);
    $val   = substr($s, 0, -1);

    if (!is_numeric($val))
        throw new Exception ('bad val: '.$val);

    switch ($delim) {
    case 'w': return $val * 604800;
    case 'd': return $val * 86400;
    case 'h': return $val * 3600;
    case 'm': return $val * 60;
    case 's': return $val;
    default: throw new Exception ('unknown delim:'.$delim);
    }
}

/**
 * Is $s a valid duration representation, such as "4h" ?
 */
function is_duration($s)
{
    if (is_numeric($s))
        return true;

    $pattern = '/^[0-9]+[wdhms]+$/';

    if (preg_match($pattern, $s))
        return true;

    return false;
}

?>
