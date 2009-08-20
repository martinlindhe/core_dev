<?php
/**
 * $Id$
 *
 * This file contains default formatting functions. They can be overridden by project-specific functions
 *
 * @disclaimer This file is a required component of core_dev
 * @author Martin Lindhe, 2007-2009 <martin@startwars.org>
 */

require_once('locale.php');

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

	$datestamp = mktime (0,0,0,date('m',$ts), date('d',$ts), date('Y',$ts));
	$yesterday = mktime (0,0,0,date('m') ,date('d')-1,  date('Y'));
	$tomorrow  = mktime (0,0,0,date('m') ,date('d')+1,  date('Y'));

	$timediff = time() - $ts;

	if (date('Y-m-d', $ts) == date('Y-m-d')) {
		//Today 18:13
		$res = date('H:i',$ts);
	} else if ($datestamp == $yesterday) {
		//Yesterday 18:13
		$res = t('Yesterday').' '.date('H:i',$ts);
	} else if ($datestamp == $tomorrow) {
		//Tomorrow 18:13
		$res = t('Tomorrow').' '.date('H:i',$ts);
	} else {
		//2007-04-14 15:22
		$res = date('Y-m-d H:i', $ts);
	}

	return $res;
}

?>
