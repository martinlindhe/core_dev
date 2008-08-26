<?php
/**
 * $Id$
 *
 * This script is called regulary. returns $userid of user who wants to chat with you, 0 otherwise
 * It uses atomEvents for handling
 */

require_once('find_config.php');
$session->requireLoggedIn();

$event = getSubscriptions(SUBSCRIPTION_USER_CHATREQ);

if ($event && !isset($_GET['nonewchat'])) {
	echo $session->id.';'.$event[0]['itemId'].';'.Users::getName($event[0]['itemId']);
	removeSubscription(SUBSCRIPTION_USER_CHATREQ, $event[0]['itemId']);
}
else {
	echo '0;0;0';
}

?>
