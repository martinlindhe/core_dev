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
require_once('class.CoreProperty.php');

class Duration extends CoreProperty
{
	private $value; ///< seconds with decimal precision

	function get() { return $this->value; }

	/**
	 * Decodes a textual representation for a duration
	 *
	 * @param $s input string
	 */
	function set($s)
	{
		if (!$s) return;

		if (is_numeric($s)) {
			$this->value = $s;
			return;
		}

		$a = explode(':', $s);
		if (count($a) == 3) {
			//handle "00:03:39.00"
			$this->value = ($a[0] * 3600) + ($a[1] * 60) + $a[2];
			return;
		}

		if (count($a) == 2) {
			//handle "04:29"
			$this->value = ($a[0] * 60) + $a[1];
			return;
		}

		dtrace();
		die('Duration->set( '.$s.' ) FAIL');
		//$this->duration = $s;
	}

	function inSeconds()
	{
		return round($this->value, 0);
	}

	function inMilliseconds()
	{
		return round($this->value * 1000, 0);
	}

	/**
	 * Formats a duration into "MM:SS" or "HH:MM:SS"
	 *
	 * @return "04:37:11" h:m:s...
	 */
	function render()
	{
		if (is_float($this->value))
			$secs = ceil($this->value);
		else
			$secs = $this->value;

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
