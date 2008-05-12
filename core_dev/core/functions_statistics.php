<?php
/**
 * $Id$
 *
 * Used by admin module to display statistics
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	/**
	 * Displays stats for each month.
	 * Will create tblStatistics entries if any are missing for requested page.
	 * Hopefully a cron-script updates this regulary, but otherwise the pages will load a bit slow the first times they are accessed
	 */
	function showStatsMonth($year, $month)
	{
		global $db, $config;
		if (!is_numeric($year) || !is_numeric($month)) return false;

		generateStatsMonth($year, $month);

		echo '<img src="'.$config['core']['web_root'].'api/image_statistics.php?y='.$year.'&amp;m='.$month.getProjectPath().'" alt="Stats"/>';
	}

	function generateStatsMonth($year, $month)
	{
		global $db;
		if (!is_numeric($year) || !is_numeric($month)) return false;

		$month_start = mktime(0, 0, 0, $month, 1, $year);
		$month_days  = date('t', $month_start);
		$month_end   = mktime(0, 0, 0, $month+1, 1, $year);

		//Remove previous entries, if any
		$q = 'DELETE FROM tblStatistics WHERE time BETWEEN "'.sql_datetime($month_start).'" AND "'.sql_datetime($month_end).'"';
		$db->query($q);

		for ($day=1; $day <= $month_days; $day++) {
			//Generate stats for each day
			for ($h=0; $h<24; $h++) {

				//Count logins
				$time_start = mktime($h, 0, 0, $month, $day, $year);
				$time_end   = mktime($h+1, 0, 0, $month, $day, $year);
				$q = 'SELECT COUNT(*) FROM tblLogins WHERE timeCreated BETWEEN "'.sql_datetime($time_start).'" AND "'.sql_datetime($time_end).'"';
				$logins = $db->getOneItem($q);

				//Count registrations
				$q = 'SELECT COUNT(*) FROM tblUsers WHERE timeCreated BETWEEN "'.sql_datetime($time_start).'" AND "'.sql_datetime($time_end).'"';
				$regs = $db->getOneItem($q);

				//Store information
				$q = 'INSERT INTO tblStatistics SET time="'.sql_datetime($time_start).'",logins='.$logins.',registrations='.$regs;
				$db->insert($q);
			}
		}
	}

	function getOldestLoginTime()
	{
		global $db;

		$q = 'SELECT timeCreated FROM tblLogins ORDER BY timeCreated ASC LIMIT 1';
		return $db->getOneItem($q);
	}
?>
