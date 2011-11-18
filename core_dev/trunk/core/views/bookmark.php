<?php

require_once('Bookmark.php');

$session->requireLoggedIn();

switch ($this->owner) {
case 'adduser':
    // child = user id
    Bookmark::create(BOOKMARK_FAVORITEUSER, $this->child);
    js_redirect('iview/profile/'.$this->child);
    break;

case 'removeuser':
    // child = user id
    Bookmark::remove(BOOKMARK_FAVORITEUSER, $this->child);
    js_redirect('iview/profile/'.$this->child);
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
