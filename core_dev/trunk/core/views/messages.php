<?php
/**
 * Default view for User's Private Message Inbox and Outbox
 */

//TODO: ability to view 1 full msg (there also see previous conversation with that person)
//TODO: mark seen msg as "READ"
//TODO: ability to mark seen msg as "UNREAD" again
//TODO: ability to mark a message as "DELETED". really useful? will require 2 copies of each msg, or else if user 1 deletes the msg, user2 also wont see it

require_once('Message.php');

require_once('YuiDatatable.php');


echo '<h1>Private messages</h1>';



/*
    $m = new Message();
    $m->from = 55;
    $m->to   = $session->id;
    $m->time_sent = sql_datetime( time() );
    $m->subject   = 'Hello world';
    $m->body      = "hej svejs där din gamle räv!";
    Message::store($m);
*/


echo '<h2>Inbox</h2>';
$list = Message::getInbox($session->id);
//d($list);

$dt = new YuiDatatable();
$dt->setCaption('Inbox');
$dt->addColumn('from',         'From');    /// XXXX show username, show link to user page
$dt->addColumn('time_sent',    'Sent');
$dt->addColumn('subject',      'Subject');
$dt->setSortOrder('time_sent', 'desc');
$dt->setDataList( $list );
echo $dt->render();



echo '<h2>Outbox</h2>';
$list = Message::getOutbox($session->id);
d($list);


?>
