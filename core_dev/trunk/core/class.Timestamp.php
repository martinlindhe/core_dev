<?php
/**
 * $Id$
 *
 * Present a UNIX timestamp in different ways
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

require_once('functions_time.php');

class Timestamp
{
	private $ts = 0;

	/**
	 * Initialize object to specified time
	 *
	 * @param $t unix timestamp or strtotime() understandable string
	 */
	function __construct($t = 'now')
	{
		if (!$t) return;
		$this->set($t);
	}

	/**
	 * Sets internal timestamp to Unix time
	 *
	 * @param $ts Unix timestamp (numeric) or string
	 */
	function set($ts)
	{
		if (is_string($ts)) $ts = strtotime($ts);
		if (!is_numeric($ts) || !$ts) return false;
		$this->ts = $ts;
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
		$this->ts = $ts - 2208988800;
	}

	function getUnix()
	{
		return $this->ts;
	}

	/**
	 * Convert Unix timestamp to NTP timestamp
	 *
	 * @return timestamp in NTP format
	 */
	function getNTP()
	{
		return 2208988800 + $this->ts;
	}

	/**
	 * Returns time in MySQL "date" format
	 */
	function getSqlDate()
	{
		return date('Y-m-d', $this->ts);
	}

	/**
	 * Returns time in MySQL "datetime" format
	 *
	 * @example 2009-04-13 23:17:01
	 */
	function getSqlDateTime()
	{
		return date('Y-m-d H:i:s', $this->ts);
	}

	/**
	 * Formats timestamp according to RFC 882
	 *
	 * @example Fri, 19 Dec 2008 16:50:19 +0100
	 * @return RFC 882 formatted timestamp
	 */
	function getRFC882()
	{
		return date('r', $this->ts);	//XXX actually RFC 2882 (supersedes RFC 882)
	}

	/**
	 * Formats timestamp according to RFC 3339
	 *
	 * @example 2008-12-19T16:50:19+01:00
	 * @return RFC 3339 formatted timestamp
	 */
	function getRFC3339()
	{
		$date = date('Y-m-d\TH:i:s', $this->ts);

		$matches = array();
		if (preg_match('/^([\-+])(\d{2})(\d{2})$/', date('O', $this->ts), $matches)) {
			return $date.$matches[1].$matches[2].':'.$matches[3];
		}
		return $date.'Z';
	}

	/**
	 * Presents the time in relative form
	 *
	 * @example "2 weeks ago", "yesterday at 9:40"
	 */
	function getRelative()
	{
		if (time() >= $this->ts)
			return shortTimePeriod(time() - $this->ts).' ago';

		return shortTimePeriod($this->ts - time()).' in the future';
	}

	function render()
	{
		$datestamp = mktime(0,0,0,date('m', $this->ts), date('d', $this->ts), date('Y', $this->ts));
		$yesterday = mktime(0,0,0,date('m'), date('d')-1, date('Y'));
		$tomorrow  = mktime(0,0,0,date('m'), date('d')+1, date('Y'));

		$timediff = time() - $this->ts;

		if (date('Y-m-d', $this->ts) == date('Y-m-d')) {
			//Today 18:13
			return t('Today').' '.date('H:i', $this->ts);
		}
		if ($datestamp == $yesterday) {
			//Yesterday 18:13
			return t('Yesterday').' '.date('H:i', $this->ts);
		}
		if ($datestamp == $tomorrow) {
			//Tomorrow 18:13
			return t('Tomorrow').' '.date('H:i', $this->ts);
		}

		//2007-04-14 15:22
		return date('Y-m-d H:i', $this->ts);
	}
}