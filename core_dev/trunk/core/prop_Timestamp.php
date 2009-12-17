<?php
/**
 * $Id$
 *
 * Present a UNIX timestamp in different ways
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//STATUS: good

require_once('class.CoreProperty.php');
require_once('functions_time.php');

class Timestamp extends CoreProperty
{
	private $value; ///< internal representation of time, as a Unix timestamp

	/**
	 * @return a numeric Unix timestamp
	 */
	function get()
	{
		if (!$this->value)
			return false;

		return $this->value;
	}

	/**
	 * Sets internal timestamp to Unix time
	 *
	 * @param $ts Unix timestamp (numeric) or string
	 */
	function set($ts)
	{
		if (is_string($ts))
			$ts = strtotime($ts);

		if (!is_numeric($ts))
			return false;

		$this->value = $ts;
	}

	/**
	 * Converts a NTP timestamp to Unix timestamp
	 *
	 * @param $ts NTP timestamp
	 * @return timestamp in UNIX format
	 */
	function setFromNTP($ts)
	{
		if (!is_numeric($ts)) return false;
		$this->value = $ts - 2208988800;
	}

	/**
	 * Convert Unix timestamp to NTP timestamp
	 *
	 * @return timestamp in NTP format
	 */
	function getNTP()
	{
		return 2208988800 + $this->value;
	}

	/**
	 * Returns time in MySQL "date" format
	 */
	function getSqlDate()
	{
		return date('Y-m-d', $this->value);
	}

	/**
	 * Returns time in MySQL "datetime" format
	 *
	 * @example 2009-04-13 23:17:01
	 */
	function getSqlDateTime()
	{
		return date('Y-m-d H:i:s', $this->value);
	}

	/**
	 * Formats timestamp according to RFC 882 (actually RFC 2882 which supersedes RFC 882)
	 *
	 * @example Fri, 19 Dec 2008 16:50:19 +0100
	 * @return RFC 882 formatted timestamp
	 */
	function getRFC882()
	{
		return date('r', $this->value);
	}

	/**
	 * Formats timestamp according to RFC 3339
	 *
	 * @example 2008-12-19T16:50:19+01:00
	 * @return RFC 3339 formatted timestamp
	 */
	function getRFC3339()
	{
		$date = date('Y-m-d\TH:i:s', $this->value);

		$matches = array();
		if (preg_match('/^([\-+])(\d{2})(\d{2})$/', date('O', $this->value), $matches))
			return $date.$matches[1].$matches[2].':'.$matches[3];

		return $date.'Z';
	}

	/**
	 * Presents the time in relative form
	 *
	 * @example "2 weeks ago", "yesterday at 9:40"
	 */
	function getRelative() //XXX DEPRECATE, reimplement using prop_Duration (?)
	{
		if (time() >= $this->value)
			return shortTimePeriod(time() - $this->value).' ago';

		return shortTimePeriod($this->value - time()).' in the future';
	}

	/**
	 * @return the diff between two timestamps, in seconds
	 */
/* //XXX no code uses this
	function getDiff($ts)
	{
		if (!is_object($ts))
			$ts = new Timestamp($ts);

		return $this->get() - $ts->get();
	}
*/
	function render()
	{
		//XXX maybe call locale-specific functions to handle rendering

		$months = array(
		1=>'January',    2=>'February', 3=>'March',     4=>'April',
		5=>'May',        6=>'June',     7=>'July',      8=>'August',
		9=>'September', 10=>'October', 11=>'November', 12=>'December'
		);

		$datestamp = mktime(0,0,0,date('m', $this->value), date('d', $this->value), date('Y', $this->value));
		$yesterday = mktime(0,0,0,date('m'), date('d')-1, date('Y'));
		$tomorrow  = mktime(0,0,0,date('m'), date('d')+1, date('Y'));

		$timediff = time() - $this->value;

		if (date('Y-m-d', $this->value) == date('Y-m-d')) {
			//Today 18:13
			return t('Today').' '.date('H:i', $this->value);
		}
		if ($datestamp == $yesterday) {
			//Yesterday 18:13
			return t('Yesterday').' '.date('H:i', $this->value);
		}
		if ($datestamp == $tomorrow) {
			//Tomorrow 18:13
			return t('Tomorrow').' '.date('H:i', $this->value);
		}

		$year  = date('Y', $this->value);
		$month = date('n', $this->value);
		$day   = date('j', $this->value);
		if ($year == date('Y'))
			return $day.':e '.t($months[ $month ]); //.' '.date('H:i', $this->ts);

		//2007-04-14 15:22
		return date('Y-m-d H:i', $this->value);
	}
}
