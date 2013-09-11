<?php

namespace cd;

require_once('BlogEntry.php');

switch ($this->owner) {
case 'recent':
    $list = BlogEntry::getRecent(10);

    foreach ($list as $b) {
        echo '<h1>'.$b->subject.'</h1>';
        echo '<i>Written '.ago($b->time_created).' by '.UserLink::render($b->owner).'</i><br/>';
        echo nl2br($b->body);
    }
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
