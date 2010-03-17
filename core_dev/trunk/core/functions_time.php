<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

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
function age($sql_time)
{
	if (!$sql_time) return false;

	$age = date_diff2($sql_time, 'now', 2, true);
	return $age['years'];
}

/**
 * Calculates difference between two dates
 *
 * @param $t1 oldest timestamp (or datetime)
 * @param $t2 newer timestamp (or datetime)
 * @param $precision how exact? (year,month,day,hour,minute,second)
 * @param $arr set to true to return result as a array with precision as index, default is text string
 */
function date_diff2($t1, $t2, $precision = 6, $arr = false) ///XXX php5.3 has a date_diff() function
{
	if (preg_match('/\D/', $t1) && ($t1 = strtotime($t1)) === false) return false;
	if (preg_match('/\D/', $t2) && ($t2 = strtotime($t2)) === false) return false;

	if ($t1 > $t2) list($t1, $t2) = array($t2, $t1);

	$diffs = array(
		'year' => 0, 'month' => 0, 'day' => 0,
		'hour' => 0, 'minute' => 0, 'second' => 0,
	);

	foreach (array_keys($diffs) as $interval) {
		while ($t2 >= ($t3 = strtotime("+1 ${interval}", $t1))) {
			$t1 = $t3;
			++$diffs[$interval];
		}
	}
	$stack = array();
	foreach ($diffs as $interval => $num) {
		$name = $interval . ($num != 1 ? 's' : '');
		$stack[] = array($num, $name);
	}

	$ret = array();

	while (count($ret) < $precision && ($item = array_shift($stack)) !== null) {
		if ($item[0] > 0) {
			if (!$arr) $ret[] = $item[0].' '.t($item[1]);
			else $ret[ $item[1] ] = $item[0];
		}
	}

	if (!$arr) return implode(', ', $ret);
	return $ret;
}

/**
 * @return number of days the date period spans over
 */
function num_days($date1, $date2)
{
	if (!is_numeric($date1)) $date1 = strtotime($date1);
	if (!is_numeric($date2)) $date2 = strtotime($date2);

	if ($date1 > $date2)
		$date_diff = $date1 - $date2;
	else
		$date_diff = $date2 - $date1;

	$days = floor($date_diff / (60*60*24)) + 1;

	return $days;
}

/**
 * Converts a timespan into human-readable text
 *
 * @param $secs number of seconds to present
 * @return returns a sting like: 4h10m3s
 */
function shortTimePeriod($secs) //XXX rename to something with duration. also see Duration class
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
	exec($c, $output, &$retval);

	return microtime(true) - $exec_start;
}

/**
 * Returns the current time in the same format as the MySQL "NOW()" command
 * @return time in MySQL datetime format
 */
function now()
{
	return strftime('%Y-%m-%d %H:%M:%S');
}

/**
 * Returns given UNIX timestamp in MySQL datetime format
 *
 * @param $ts is a UNIX timestamp
 * @return given UNIX timestamp in MySQL datetime format
 */
function sql_datetime($ts)
{
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
	return date('Y-m-d', $ts);
}

/**
 * Returns MySQL datetime in UNIX timestamp format
 *
 * @param $datetime is a MySQL datetime
 * @return given MySQL datetime in UNIX timestamp format
 */
function datetime_to_timestamp($datetime)
{
	return strtotime($datetime);
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

?>
