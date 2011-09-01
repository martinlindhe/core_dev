<?php

//TODO: ability post entries in someone elses guestbook
//TODO: mark entries as read, see number of unread entries

require_once('Guestbook.php');

if (!$session->id)
    die('XXX gb only for logged in users');


$user_id = $this->owner;

if (!$this->owner)
    $user_id = $session->id;


$user = new User($user_id);

echo '<h1>Guestbook for '.$user->name.'</h1>';


$gb = Guestbook::getEntries($user_id);

d( $gb);



/*
$x = new Guestbook();
$x->owner = $session->id;
$x->creator = 123;
$x->time_created = sql_datetime( time() );
$x->body = 'hej svejs din gamle pelikan';
Guestbook::store($x);
*/

?>
