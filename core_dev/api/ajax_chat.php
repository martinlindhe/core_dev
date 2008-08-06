<?php
/**
 * $Id$
 *
 * This script is called regulary. returns new chatmessages if any
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

$msgs = $db->getArray('SELECT * FROM tblChat WHERE userId = '.$userId.' AND authorId = '.$otherId.' AND msgRead = 0');

foreach ($msgs as $msg) {
	echo Users::getName($msg['authorId']).'|;'.$msg['msg'].'|;'.$msg['msgDate']."\n";
	$db->update('UPDATE tblChat SET msgRead = 1 WHERE userId = '.$userId.' AND authorId = '.$otherId.' AND msgDate = "'.$msg['msgDate'].'" AND msgRead = 0 LIMIT 1');

}

?>
