<?php
/**
 * $Id$
 *
 * This file contains default formatting functions. They can be overridden by project-specific functions
 *
 * \disclaimer This file is a required component of core_dev
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

/**
 * Default time format display, handles unix times or SQL DATETIME input formats
 */
function formatTime($ts)
{
	if (function_exists('formatTimeOverride')) {
		return formatTimeOverride($ts);
	}
	if (is_numeric($ts)) return date('Y-m-d H:i', $ts);

	$time = strtotime($ts);
	return date('Y-m-d H:i', $time);	//YYYY-MM-DD HH:MM
}

?>
