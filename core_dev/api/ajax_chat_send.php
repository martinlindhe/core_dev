<?php
/**
 * $Id$
 *
 * This script is called by ajax chat to store message
 */

$config['no_session'] = true;	//force session "last active" update to be skipped
require_once('find_config.php');
$session->requireLoggedIn();

$userId = $session->id;

if (isset($_GET['otherid'])) {
	$otherId = $_GET['otherid'];
} else {
	die(0);
}

if (isset($_GET['msg'])) {
	$msg = $_GET['msg'];
} else {
	die(0);
}


$msgs = $db->insert('INSERT INTO tblChat (userId, authorId, msg, msgDate, msgRead) VALUES ('.$otherId.', '.$userId.', "'.$msg.'", NOW(), 0)');

echo 1;
?>
