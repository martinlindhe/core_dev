<?php
/**
 * $Id$
 *
 * This file contains default formatting functions. They can be overridden by project-specific functions
 *
 * @disclaimer This file is a required component of core_dev
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

/**
 * Default time format display
 *
 * @param $ts unix timestamp or SQL DATETIME format
 */
function formatTime($ts = 0)
{
	if (!$ts) $ts = time();

	if (function_exists('formatTimeOverride'))
		return formatTimeOverride($ts);

	if (!is_numeric($ts)) $ts = strtotime($ts);

	return date('Y-m-d H:i', $ts);	//YYYY-MM-DD HH:MM
}

?>
