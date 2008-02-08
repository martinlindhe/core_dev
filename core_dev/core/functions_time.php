<?
/**
 * $Id$
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	/**
	 * Converts a SQL timestamp to a text string representing how long ago this timestamp occured
	 *
	 * \param $sql_time SQL timestamp
	 * \return text string representing how long ago this timestamp occured
	 */
	function ago($sql_time)
	{
		$old_time = strtotime($sql_time);
		$curr_time = time();

		if ($curr_time >= $old_time) {
			return shortTimePeriod($curr_time - $old_time).' ago';
		} else {
			return shortTimePeriod($old_time - $curr_time).' in the future';
		}
	}

	/**
	 * Converts a SQL timestamp to a text string representing a "niced" time
	 *
	 * \param $sql_time SQL timestamp
	 * \return text string representing a "niced" time
	 */
	function nicedTime($sql_time)
	{
// om typ samma dag/2, visa bara klockslag
// om typ samma månad/2, visa bara datum och månad
// om typ samma år/2, månad och datum i månad och år 
// FIXME: bajsbajsbajs! make time really nice!!
// FIXME: gör månadsnamn svenska

		$old_time = strtotime($sql_time);
		$curr_time = time();
		$diff = $curr_time - $old_time;
//echo 'old:'.$old_time.'<br>curr:'.$curr_time.'<br>';
		if ($diff < 43200) {
			return 'kl. '.date("H:i",$old_time);
		} else if ($diff < 1296000) { // 15 dagar
			return date("j M",$old_time);
		} else if ($diff < 15120000) { // 175 dagar
			return date("j M -y",$old_time);
		}
	}

	/**
	 * Converts a timespan into human-readable text
	 * 
	 * \param $seconds number of seconds to present
	 * \return returns a sting like: 4h10m3s
	 */
	function shortTimePeriod($seconds)
	{
		if (is_float($seconds)) $seconds = round($seconds);
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
		$a = date('i',$seconds)-0; //translate from 09 to 9 quickly ;)
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
	 * Return the current time in NTP timestamp format, or converts Unix timestamp to NTP timestamp
	 *
	 * \param $timestamp UNIX timestamp
	 * \return timestamp in NTP format
	 */
	function ntptime($timestamp = 0)
	{
		if (!$timestamp) $timestamp = time();
		return 2208988800 + $timestamp;
	}

	/**
	 * Converts a ntp timestamp to a unix timestamp
	 *
	 * \param $timestamp ntp timestamp
	 * \return timestamp in UNIX format
	 */
	function ntptime_to_unixtime($timestamp)
	{
		return $timestamp - 2208988800;
	}
?>
