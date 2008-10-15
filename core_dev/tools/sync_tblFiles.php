<?php
/**
 * $Id$
 *
 * This script syncs tblFiles with what is on disc,
 * updates mimeType, mediaType, fileSize and tblChecksum entries
 * Deletes missing files
 */

require_once('/var/www/www.phonecafe.se/config.php');

$list = $files->getFiles();

echo "Processing ".count($list)." files ...\n";

$deletes = 0;
$updates = 0;

foreach ($list as $row) {
	if ($files->updateFile($row['fileId'])) {
		echo ".";
		$updates++;
	} else {
		echo "*";
		$files->deleteFile($row['fileId'], 0, true);
		$deletes++;
	}
}

echo "\n------------------\n";
echo "Files updated: ".$updates."\n";
echo "Files deleted: ".$deletes."\n";

?>
