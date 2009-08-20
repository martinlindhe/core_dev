<?php
/**
 * $Id$
 *
 * @author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

/**
 * Converts a SQL timestamp to a text string representing how long ago this timestamp occured
 *
 * @param $sql_time SQL timestamp
 * @return text string representing how long ago this timestamp occured
 */
function ago($sql_time)
{
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

	$age = date_diff($sql_time, 'now', 2, true);
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
function date_diff($t1, $t2, $precision = 6, $arr = false)
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
			if (!$arr) $ret[] = "{$item[0]} {$item[1]}";
			else $ret[ $item[1] ] = $item[0];
		}
	}

	if (!$arr) return implode(', ', $ret);
	return $ret;
}

/**
 * Converts a timespan into human-readable text
 *
 * @param $seconds number of seconds to present
 * @return returns a sting like: 4h10m3s
 */
function shortTimePeriod($seconds)
{
	if (is_float($seconds)) $seconds = ceil($seconds);
	$retval = '';

	//years
	$a = date('Y', $seconds) - 1970;
	if ($a==1) $retval = $a.' year, ';
	else if ($a>0) $retval = $a.' years, ';
	$seconds -= (((($a*60)*60)*24)*30)*365;

	//months
	$a = date('n',$seconds)-1;
	if ($a==1) $retval .= $a.' month, ';
	else if($a>0) $retval .= $a.' months, ';
	$seconds -= ((($a*60)*60)*24)*30;

	//days
	$a = date('j',$seconds)-1;
	if ($a==1) $retval .= $a.' day, ';
	else if ($a>0) $retval .= $a.' days, ';
	$seconds -= (($a*60)*60)*24;

	//hours
	$a = date('H',$seconds)-1;
	if ($a>0) $retval .= $a.'h';
	$seconds -= ($a*60)*60;

	//minutes
	$a = date('i',$seconds)-0; //XXX ugly hack, translate from 09 to 9 quickly ;)
	if ($a>0) $retval .= $a.'m';
	$seconds -= $a*60;

	//seconds
	$a = date('s',$seconds)-0;
	if ($a>0) $retval .= $a.'s';

	if (substr($retval, -2) == ', ') $retval = substr($retval, 0, -2);
	if ($retval == '') $retval = '0s';

	return $retval;
}

/**
 * Convert Unix timestamp to NTP timestamp
 *
 * @param $ts UNIX timestamp or format strtotime() understands
 * @return timestamp in NTP format
 */
function ntptime($ts = 0)
{
	if (!$ts) $ts = time();
	if (!is_numeric($ts)) $ts = strtotime($ts);

	return 2208988800 + $ts;
}

/**
 * Converts a NTP timestamp to Unix timestamp
 *
 * @param $ts NTP timestamp
 * @return timestamp in UNIX format
 */
function ntptime_to_unixtime($ts)
{
	if (!is_numeric($ts)) return false;

	return $ts - 2208988800;
}

/**
 * Formats timestamp according to RFC 3339
 * Example: 2008-12-19T16:50:19+01:00
 *
 * @param $ts UNIX timestamp or format strtotime() understands
 * @return RFC 3339 formatted timestamp
 */
function date3339($ts = 0)
{
	if (!$ts) $ts = time();
	if (!is_numeric($ts)) $ts = strtotime($ts);

	$date = date('Y-m-d\TH:i:s', $ts);

	$matches = array();
	if (preg_match('/^([\-+])(\d{2})(\d{2})$/', date('O', $ts), $matches)) {
		return $date.$matches[1].$matches[2].':'.$matches[3];
	}
	return $date.'Z';
}

/**
 * Formats timestamp according to RFC 882
 * Example: Fri, 19 Dec 2008 16:50:19 +0100
 *
 * @param $ts UNIX timestamp or format strtotime() understands
 * @return RFC 882 formatted timestamp
 */
function date882($ts = 0)
{
	if (!$ts) $ts = time();
	if (!is_numeric($ts)) $ts = strtotime($ts);

	return date('r', $ts);	//XXX actually RFC 2882 (supersedes RFC 882)
}
?>