<?php
/**
 * This script removes old files from the process server
 */


die; // FIXME script is nonfunctional!


require_once('find_config.php');

define('EXPIRE_DAYS', 90);


//removes source media
$list = getProcessQueue(PROCESS_FETCH, '', ORDER_COMPLETED, EXPIRE_DAYS);
foreach ($list as $row) {
	//$h->files->deleteFile($row['refererId'], 0, true);
}

//removes converted media - FIXME lookup is wierd
$list = getProcessQueue(PROCESS_CONVERT_TO_DEFAULT, '', ORDER_COMPLETED, EXPIRE_DAYS);
foreach ($list as $row) {
	getProcessQueueEntry($row['refererId']);
	//$h->files->deleteFile($row['refererId'], 0, true);
}

?>
