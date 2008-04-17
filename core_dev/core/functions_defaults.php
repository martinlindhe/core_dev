<?
/**
 * $Id$
 *
 * This file contains default formatting functions. They can be overridden by project-specific functions 
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	/**
	 * Default time format display, YYYY-MM-DD HH:MM:SS
	 */
	function formatTime($ts)
	{
		if (function_exists('formatTimeOverride')) {
			return formatTimeOverride($ts);
		}
		return $ts;
	}

?>
