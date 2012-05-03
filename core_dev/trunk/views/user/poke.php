<?php

require_once('Bookmark.php');
require_once('Poke.php');

$session->requireLoggedIn();

switch ($this->owner) {
case 'send':
    // child = user to poke

    if (Bookmark::exists(BOOKMARK_USERBLOCK, $session->id, $this->child)) {
        echo 'User has blocked you from access';
        return;
    }

    echo 'A poke was sent to the user.<br/><br/>';

    Poke::send($this->child);

    echo ahref('u/profile/'.$this->child, 'Continue');
    break;

default:
    echo 'no such view: '.$this->owner;
}


?>
