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

case 'show':
    // shows my recieved pokes
    echo '<h1>My recieved pokes</h1>';

    $list = Poke::getPokes($session->id);

    foreach ($list as $poke) {
        echo 'Poke from '.UserLink::render($poke->from).' sent at '.$poke->time.'<br/>';
    }

    break;

default:
    echo 'no such view: '.$this->owner;
}


?>
