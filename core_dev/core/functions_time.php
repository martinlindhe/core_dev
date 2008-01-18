<?
/**
 * $Id$
 *
 * \author Martin Lindhe, 2007-2008 <martin@startwars.org>
 */

	/**
	 * Return the current time in NTP timestamp format, or converts Unix timestamp to NTP timestamp
	 *
	 * \param $timestamp UNIX timestamp
	 * \return timestamp in NTP format
	 */
	function ntptime($timestamp = 0)
	{
		if (!$timestamp) $timestamp = time();
		return 2208988800 + $timestamp;
	}

	/**
	 * Converts a ntp timestamp to a unix timestamp
	 *
	 * \param $timestamp ntp timestamp
	 * \return timestamp in UNIX format
	 */
	function ntptime_to_unixtime($timestamp)
	{
		return $timestamp - 2208988800;
	}
	
?>