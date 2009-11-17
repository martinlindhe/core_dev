<?php
/**
 * $Id$
 *
 * Duration property
 *
 * @author Martin Lindhe, 2009 <martin@startwars.org>
 */

//STATUS: good

require_once('core.php');

class Duration extends CoreBase
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
	 * Convert object representation to a string
	 */
	//XXX cp. '' evaluerar true eller javetinte nåt är fel
	function __toString()
	{
		return $this->get().'';
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

		$hrs  = floor($secs / 3600); $secs %= 3600;
		$mins = floor($secs / 60);   $secs %= 60;

		$retval = '';

		//hours
		if ($hrs)
			$retval .= $hrs.':';

		//minutes
		if ($mins < 10 && $hrs)
			$retval .= '0'.$mins.':';	//dont append '0' if no hour is set
		else
			$retval .= $mins.':';

		//seconds
		if ($secs < 10)
			$retval .= '0'.$secs;
		else
			$retval .= $secs;

		if ($retval == '')
			$retval = '00:00';

		return $retval;
	}
}

?>
