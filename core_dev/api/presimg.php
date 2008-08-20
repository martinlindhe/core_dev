<?php

/**
 * $Id$
 *
 * Takes a user id, returns this user's presentation image
 */

if (empty($_GET['id']) || !is_numeric($_GET['id'])) die;

require_once('find_config.php');

$fieldId = getUserdataFieldIdByType(USERDATA_TYPE_IMAGE);
$fileId = loadUserdataSetting($_GET['id'], $fieldId);

if ($fileId && !isInQueue($fileId, MODERATION_PRES_IMAGE)) {
	$files->sendFile($fileId, true);
}
?>
