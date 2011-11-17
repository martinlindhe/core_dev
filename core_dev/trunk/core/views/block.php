<?php

require_once('Bookmark.php');

$session->requireLoggedIn();

switch ($this->owner) {
case 'user':
    // child = user id
    if (!$this->child || $this->child == $session->id)
        die('meh');

    if (confirmed('You sure you want to block this user from contacting you?')) {
        Bookmark::create(BOOKMARK_USERBLOCK, $this->child);
        js_redirect('iview/profile/'.$this->child);
    }
    break;

case 'remove':
    // child = user id
    Bookmark::remove(BOOKMARK_USERBLOCK, $this->child);
    js_redirect('iview/block/manage');
    break;

case 'manage':
    echo '<h1>Manage your blocked users</h1>';

    $list = Bookmark::getList(BOOKMARK_USERBLOCK, $session->id);

    foreach ($list as $o) {
        echo ahref('iview/profile/'.$o->value, User::get($o->value)->name).' ';
        echo ahref('iview/block/remove/'.$o->value, 'Remove block').'<br/>';
    }
    break;

default:
    echo 'no such view: '.$this->owner;
}

?>
