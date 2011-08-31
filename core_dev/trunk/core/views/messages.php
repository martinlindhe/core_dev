<?php
/**
 * Default view for User's Private Message Inbox and Outbox
 */

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
