<?
/**
 * This script is intended to be called from the command line and run permanently
 *
 * In addition to this script, another script will be called regularry by crontab that does one iteration of processQueue()
 */
	set_time_limit(0);	//no time limit
	$config['no_session'] = true;	//force session "last active" update to be skipped
	require_once('config.php');

	do {
		processQueue();
		sleep(1);
	} while (1);

?>