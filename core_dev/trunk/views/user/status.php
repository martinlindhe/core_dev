<?php
/**
 * For normal users usage of chatrooms
 */

require_once('PersonalStatus.php');
require_once('Like.php');

$session->requireLoggedIn();

switch ($this->owner) {
case 'like':
    $status = PersonalStatus::get($this->child);
    if (!$status)
        die('WEEH');
//XXX: disallow multiple likes on same status
    Like::set($status->id, STATUS, $session->id);

    redir('u/profile/'.$status->owner);
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
