<?php
/**
 * $Id$
 *
 * Duration property
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//STATUS: good

class Duration
{
	private $duration; ///< seconds with decimal precision

	/**
	 * @param $n initialize object to a duration, in seconds
	 */
	function __construct($n = 0)
	{
		$this->duration = $n;
	}

	/**
	 * Decodes a textual representation for a duration
	 *
	 * @param $s input string
	 */
	function set($s)
	{
		if (!$s) return;

		if (is_numeric($s)) {
			$this->duration = $s;
			return;
		}

		$a = explode(':', $s);
		if (count($a) == 3) {
			//handle "00:03:39.00"
			$this->duration = ($a[0] * 3600) + ($a[1] * 60) + $a[2];
			return;
		}

		if (count($a) == 2) {
			//handle "04:29"
			$this->duration = ($a[0] * 60) + $a[1];
			return;
		}

		dtrace();
		die('Duration->set( '.$s.' ) FAIL');
		//$this->duration = $s;
	}

	function get()
	{
		return $this->duration;
	}

	function inSeconds()
	{
		return round($this->duration, 0);
	}

	function inMilliseconds()
	{
		return round($this->duration * 1000, 0);
	}

	/**
	 * Formats a duration into "MM:SS" or "HH:MM:SS"
	 *
	 * @return "04:37:11" h:m:s...
	 */
	function render()
	{
		if (is_float($this->duration))
			$secs = ceil($this->duration);
		else
			$secs = $this->duration;

		$retval = '';

		//hours
		$a = date('H', $secs) - 1;
		if ($a > 0)
			$retval .= $a.':';
		$secs -= ($a * 60) * 60;

		//minutes
		$a = date('i', $secs) - 0;
		$retval .= $a.':';
		$secs -= $a * 60;

		//seconds
		$a = date('s', $secs);
		$retval .= $a;

		if (substr($retval, -2) == ', ')
			$retval = substr($retval, 0, -2);

		if ($retval == '')
			$retval = '00:00';

		return $retval;
	}
}

?>
