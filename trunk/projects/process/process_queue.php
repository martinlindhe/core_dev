<?php
/**
 * This script is intended to be called from cron every minute
 */

set_time_limit(0);	//no time limit
$config['no_session'] = true;	//force session "last active" update to be skipped
require_once('config.php');
$config['debug'] = false;

$limit = 10;	//do a few encodings each time the script is run

for ($i = 0; $i < $limit; $i++) {
	processQueue();
	sleep(1);
	echo '.';
}

?>
