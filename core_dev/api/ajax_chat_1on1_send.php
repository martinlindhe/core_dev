<?php
/**
 * $Id$
 *
 * This script is called by ajax chat to store message
 */

require_once('find_config.php');
$h->session->requireLoggedIn();

$userId = $h->session->id;

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

$q = 'INSERT INTO tblChat (userId, authorId, msg, msgDate, msgRead) VALUES ('.$otherId.', '.$userId.', "'.$msg.'", NOW(), 0)';
$msgs = $db->insert($q);

echo 1;
?>
