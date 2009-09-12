<?php
/**
 * $Id$
 *
 * Present a UNIX timestamp in different ways
 */

//TODO: output as NTP time, various RFC time representations etc

class Timestamp
{
	private $timestamp;

	/**
	 * Initialize object to current time if none is provided
	 */
	function __construct($t = false)
	{
		if (!$t) $this->setTimestamp(time());
		else $this->setTimestamp($t);
	}

	/**
	 * @param $ts input numeric (unix timestamp) or string
	 */
	function setTimestamp($ts)
	{
		if (!is_numeric($ts)) $ts = strtotime($ts);
		$this->timestamp = $ts;
	}

	/**
	 * Presents the time in relative form, such as "2 weeks ago" or "yesterday at 9:40"
	 */
	function renderRelative()
	{
		//XXX FIXME implement
		return $this->render();
	}

	function render()
	{
		$datestamp = mktime(0,0,0,date('m', $this->timestamp), date('d', $this->timestamp), date('Y', $this->timestamp));
		$yesterday = mktime(0,0,0,date('m'), date('d')-1, date('Y'));
		$tomorrow  = mktime(0,0,0,date('m'), date('d')+1, date('Y'));

		$timediff = time() - $this->timestamp;

		if (date('Y-m-d', $this->timestamp) == date('Y-m-d')) {
			//Today 18:13
			return t('Today').' '.date('H:i', $this->timestamp);
		}
		if ($datestamp == $yesterday) {
			//Yesterday 18:13
			return t('Yesterday').' '.date('H:i', $this->timestamp);
		}
		if ($datestamp == $tomorrow) {
			//Tomorrow 18:13
			return t('Tomorrow').' '.date('H:i', $this->timestamp);
		}

		//2007-04-14 15:22
		return date('Y-m-d H:i', $this->timestamp);
	}
}
