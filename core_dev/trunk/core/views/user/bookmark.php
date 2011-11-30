<?php

require_once('Bookmark.php');

$session->requireLoggedIn();

switch ($this->owner) {
case 'adduser':
    // child = user id
    Bookmark::create(BOOKMARK_FAVORITEUSER, $this->child);
    js_redirect('u/bookmark/listusers');
    break;

case 'removeuser':
    // child = user id
    Bookmark::remove(BOOKMARK_FAVORITEUSER, $this->child);
    js_redirect('u/bookmark/listusers');
    break;


case 'listusers':
    echo '<h1>Favorite users</h1>';

    $bookmarks = Bookmark::getList(BOOKMARK_FAVORITEUSER, $session->id);
    foreach ($bookmarks as $bm)
    {
        $u = User::get($bm->value);
        echo ahref('u/profile/'.$u->id, $u->name);
        echo ' ';
        echo ahref('u/bookmark/removeuser/'.$u->id, 'Remove');
        echo '<br/>';
    }
    break;

default:
    echo 'No handler for view '.$this->owner;
}

?>
